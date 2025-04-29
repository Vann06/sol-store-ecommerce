<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        // List all FAQs grouped by category
        $categories = FaqCategory::all();
        $result = [];
        foreach ($categories as $category) {
            $faqs = Faq::where('faq_category_id', $category->id)->get();
            $result[] = [
                'category' => $category->name,
                'faqs' => $faqs
            ];
        }
        return response()->json($result);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'faq_category_id' => 'required|exists:faq_categories,id',
        ]);
        $faq = new Faq();
        $faq->question = $validated['question'];
        $faq->answer = $validated['answer'];
        $faq->faq_category_id = $validated['faq_category_id'];
        $faq->save();
        return response()->json($faq, 201);
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::find($id);
        if (!$faq) {
            return response()->json(['error' => 'FAQ not found'], 404);
        }
        $validated = $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'faq_category_id' => 'required|exists:faq_categories,id',
        ]);
        $faq->question = $validated['question'];
        $faq->answer = $validated['answer'];
        $faq->faq_category_id = $validated['faq_category_id'];
        $faq->save();
        return response()->json($faq);
    }

    public function destroy($id)
    {
        $faq = Faq::find($id);
        if (!$faq) {
            return response()->json(['error' => 'FAQ not found'], 404);
        }
        $faq->delete();
        return response()->json(['message' => 'FAQ deleted']);
    }
}
