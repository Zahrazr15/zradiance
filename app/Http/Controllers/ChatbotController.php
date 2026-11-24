<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use App\Services\GeminiChatbotService;
use App\Services\SupportDiagnosisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ChatbotController extends Controller
{
    private const HISTORY_KEY = 'chatbot.history';
    private const TOPIC_KEY = 'chatbot.support_topic';
    private const HISTORY_MAX_ITEMS = 8;
    private const HISTORY_TTL_SECONDS = 1800;

    public function send(
        Request $request,
        ChatbotService $chatbot,
        GeminiChatbotService $gemini,
        SupportDiagnosisService $support
    ): JsonResponse {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:500'],
            'product_id' => ['nullable', 'integer'],
        ]);

        $message = trim($data['message']);
        $user = $request->user();
        $session = $request->session();

        $history = $this->loadHistory($request);
        $previousTopic = $session->get(self::TOPIC_KEY);

        // PHP service menentukan intent, mengambil konteks dari database,
        // dan mendiagnosis kendala sistem milik pelanggan.
        $result = $chatbot->reply(
            $message,
            $data['product_id'] ?? null,
            $user?->name,
            $user,
            is_string($previousTopic) ? $previousTopic : null
        );

        // Metadata internal tidak boleh dikirim ke frontend.
        $useAi = (bool) ($result['_use_ai'] ?? false);
        $aiContext = is_array($result['_ai_context'] ?? null) ? $result['_ai_context'] : [];
        $topic = $result['_support_topic'] ?? null;
        $problems = is_array($result['_support_problems'] ?? null) ? $result['_support_problems'] : [];
        $needsHuman = (bool) ($result['_support_needs_human'] ?? false);

        unset(
            $result['_use_ai'],
            $result['_ai_context'],
            $result['_support_topic'],
            $result['_support_problems'],
            $result['_support_needs_human']
        );

        $intent = (string) ($aiContext['intent'] ?? '');

        if ($useAi) {
            try {
                $aiReply = $gemini->sendMessage($message, $aiContext, $history);

                if ($aiReply !== '') {
                    [$clean, $marker] = $gemini->extractHumanMarker($aiReply);

                    // Jika Gemini hanya mengirim penanda, pertahankan jawaban fallback.
                    if ($clean !== '') {
                        $result['reply'] = $clean;
                    }

                    if ($marker && ! $needsHuman) {
                        $needsHuman = true;

                        $support->logEscalation(
                            $user,
                            is_string($topic) ? $topic : 'general',
                            $problems,
                            'gemini'
                        );
                    }
                }
            } catch (Throwable $e) {
                // Jawaban fallback dari ChatbotService tetap dikirim jika AI gagal.
                Log::warning('Gemini chatbot gagal memproses pesan.', [
                    'exception' => $e::class,
                    'message' => $e->getMessage(),
                ]);

                // Pertanyaan yang tidak terjawab dan AI tidak tersedia:
                // jangan biarkan pelanggan buntu.
                if ($intent === 'general_fallback' && ! $needsHuman) {
                    $needsHuman = true;

                    $support->logEscalation($user, 'general', [], 'ai_unavailable');
                }
            }
        }

        if ($needsHuman && ! $this->hasContactLink($result['links'] ?? [])) {
            $result['links'] = array_merge(
                $result['links'] ?? [],
                [$support->contactLink(
                    $user,
                    is_string($topic) ? $topic : 'general',
                    $problems
                )]
            );
        }

        // Simpan konteks percakapan di server agar Gemini bisa memahami
        // lanjutan seperti "terus gimana?" atau "masih gagal".
        $this->saveHistory($request, $history, $message, (string) ($result['reply'] ?? ''));

        if (is_string($topic) && $topic !== '') {
            $session->put(self::TOPIC_KEY, $topic);
        } else {
            $session->forget(self::TOPIC_KEY);
        }

        return response()->json($result);
    }

    private function hasContactLink(array $links): bool
    {
        foreach ($links as $link) {
            if (str_starts_with((string) ($link['url'] ?? ''), 'https://wa.me/')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, array{role:string,text:string}>
     */
    private function loadHistory(Request $request): array
    {
        $stored = $request->session()->get(self::HISTORY_KEY);

        if (
            ! is_array($stored)
            || (time() - (int) ($stored['at'] ?? 0)) > self::HISTORY_TTL_SECONDS
            || ! is_array($stored['turns'] ?? null)
        ) {
            return [];
        }

        return array_values($stored['turns']);
    }

    private function saveHistory(
        Request $request,
        array $history,
        string $userMessage,
        string $botReply
    ): void {
        $history[] = ['role' => 'user', 'text' => $userMessage];

        if (trim($botReply) !== '') {
            $history[] = ['role' => 'model', 'text' => mb_substr($botReply, 0, 600)];
        }

        $request->session()->put(self::HISTORY_KEY, [
            'at' => time(),
            'turns' => array_slice($history, -self::HISTORY_MAX_ITEMS),
        ]);
    }
}
