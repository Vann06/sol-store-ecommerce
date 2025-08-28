<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Direccion;

class DireccionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $items = Direccion::where('id_usuario', $user->id)->orderByDesc('is_default')->latest()->get();
        return response()->json($items);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'direccion' => 'required|string|min:6',
            'id_municipio' => 'nullable|integer|exists:municipios,id',
            'is_default' => 'nullable|boolean'
        ]);

        return DB::transaction(function () use ($data, $user) {
            $isDefault = (bool)($data['is_default'] ?? false);
            if ($isDefault) {
                Direccion::where('id_usuario', $user->id)->update(['is_default' => false]);
            }

            $dir = Direccion::create([
                'id_usuario' => $user->id,
                'direccion' => $data['direccion'],
                'id_municipio' => $data['id_municipio'] ?? null,
                'is_default' => $isDefault,
            ]);

            return response()->json($dir, 201);
        });
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $dir = Direccion::where('id', $id)->where('id_usuario', $user->id)->firstOrFail();

        $data = $request->validate([
            'direccion' => 'sometimes|required|string|min:6',
            'id_municipio' => 'nullable|integer|exists:municipios,id',
            'is_default' => 'nullable|boolean'
        ]);

        return DB::transaction(function () use ($dir, $data, $user) {
            if (array_key_exists('is_default', $data) && $data['is_default']) {
                Direccion::where('id_usuario', $user->id)->update(['is_default' => false]);
                $dir->is_default = true;
            }
            if (array_key_exists('direccion', $data)) {
                $dir->direccion = $data['direccion'];
            }
            if (array_key_exists('id_municipio', $data)) {
                $dir->id_municipio = $data['id_municipio'];
            }
            $dir->save();
            return response()->json($dir);
        });
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $dir = Direccion::where('id', $id)->where('id_usuario', $user->id)->firstOrFail();
        $dir->delete();
        return response()->json(['message' => 'Eliminada']);
    }

    public function setDefault(Request $request, $id)
    {
        $user = $request->user();
        $dir = Direccion::where('id', $id)->where('id_usuario', $user->id)->firstOrFail();

        return DB::transaction(function () use ($user, $dir) {
            Direccion::where('id_usuario', $user->id)->update(['is_default' => false]);
            $dir->is_default = true;
            $dir->save();
            return response()->json(['message' => 'Actualizada', 'direccion' => $dir]);
        });
    }
}
