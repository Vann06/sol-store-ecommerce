<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function apiIndex(): JsonResponse
    {
        return response()->json(category::all());
    }
}
