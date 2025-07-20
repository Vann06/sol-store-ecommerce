<?php
namespace App\Http\Controllers;

use App\Models\Theme;
use Illuminate\Http\JsonResponse;

class ThemeController extends Controller
{
    public function apiIndex(): JsonResponse
    {
        return response()->json(Theme::all());
    }
}
