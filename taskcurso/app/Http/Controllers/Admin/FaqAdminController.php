<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FaqAdminController extends Controller
{
    public function index()
    {
        $faqs = Faq::with('category')->get();
        return view('admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        $categories = FaqCategory::all();
        return view('admin.faqs.create', compact('categories'));
    }

    public function edit(Faq $faq)
    {
        $categories = FaqCategory::all();
        return view('admin.faqs.edit', compact('faq', 'categories'));
    }
}
