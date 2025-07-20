@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-red-700">Temáticas</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Admin › Temáticas</p>
        </div>
        <a href="{{ route('admin.themes.create') }}" class="bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-800">
            + Agregar Temática
        </a>
    </div>

    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="text-left border-b border-gray-200 dark:border-gray-700">
                    <th class="py-2 px-4">Nombre</th>
                    <th class="py-2 px-4">Imagen</th>
                    <th class="py-2 px-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($themes as $theme)
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <td class="py-2 px-4">{{ $theme->name }}</td>
                        <td class="py-2 px-4">
                            @if ($theme->imagen)
                                <img src="{{ $theme->imagen }}" alt="Imagen" class="h-10 w-10 rounded object-cover">
                            @else
                                <span class="text-xs text-gray-500">Sin imagen</span>
                            @endif
                        </td>
                        <td class="py-2 px-4">
                            <a href="{{ route('admin.themes.edit', $theme->id) }}" class="text-blue-500 hover:underline">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-4 text-gray-500">No hay temáticas disponibles.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
