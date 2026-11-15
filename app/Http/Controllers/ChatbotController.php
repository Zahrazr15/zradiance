<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use App\Services\GeminiChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function send(
        Request $request,
        ChatbotService $chatbot,
        GeminiChatbotService $gemini
    ): JsonResponse {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:500'],
            'product_id' => ['nullable', 'integer'],
        ]);

        // PHP service menentukan intent dan mengambil konteks dari database.
        $result = $chatbot->reply(
            trim($data['message']),
            $data['product_id'] ?? null,
            $request->user()?->name
        );

        // Metadata internal tidak boleh dikirim ke frontend.
        $useAi = (bool) ($result['_use_ai'] ?? false);
        $aiContext = $result['_ai_context'] ?? [];
        unset($result['_use_ai'], $result['_ai_context']);

        if ($useAi) {
            try {
                $aiReply = $gemini->sendMessage(
                    trim($data['message']),
                    is_array($aiContext) ? $aiContext : []
                );

                if ($aiReply !== '') {
                    $result['reply'] = $aiReply;
                }
            } catch (\Throwable $e) {
                // Jawaban fallback dari ChatbotService tetap dikirim jika AI gagal.
                Log::warning('Gemini chatbot gagal memproses pesan.', [
                    'exception' => $e::class,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return response()->json($result);
    }
}
