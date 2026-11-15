<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\KnowledgeArticle;
use Illuminate\View\View;

class KnowledgeController extends Controller
{
    public function index(): View
    {
        $faqs = Faq::where('is_active', true)->orderBy('id')->get();

        $articles = KnowledgeArticle::where('is_active', true)
            ->orderBy('category')
            ->orderBy('title')
            ->get()
            ->groupBy('category');

        return view('knowledge.index', compact('faqs', 'articles'));
    }
}