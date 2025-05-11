@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.themes.index') }}" class="text-red-700 hover:text-red-900 text-xl">←</a>
        <h1 class="text-2xl font-semibold text-red-700 mb-1">Editar Temática</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Admin › Temáticas</p>
    </div>

    <form action="{{ route('admin.themes.update', $theme->id) }}" method="POST" enctype="multipart/form-data"
          class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium">Nombre</label>
            <input name="name" value="{{ old('name', $theme->name) }}" required class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" />
        </div>

        <div>
            <label class="block text-sm font-medium">Imagen actual</label>
            @if ($theme->imagen)
                <img src="{{ $theme->imagen }}" alt="Imagen actual" class="h-20 w-20 mt-1 rounded border shadow">
            @else
                <p class="text-sm text-gray-500">Sin imagen</p>
            @endif
        </div>

        <div>
            <label class="block text-sm font-medium">Nueva imagen (opcional)</label>
            <input 
                type="file" 
                name="imagen"
                class="w-full mt-1 border rounded-md dark:bg-gray-700 dark:border-gray-600"
                accept="image/*"
            />
        </div>

        <div class="text-right">
            <button type="submit" class="bg-red-700 text-white px-6 py-2 rounded-lg hover:bg-red-800">
                Actualizar Temática
            </button>
        </div>
    </form>
</div>
@endsection
