@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto p-4">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-red-700">Administrar FAQs</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Admin › FAQs</p>
        </div>

        <!-- Filtros -->
        <form method="GET" class="flex flex-wrap gap-3 items-center" id="faq-filter-form">
    <input type="text" name="search" value="{{ request('search') }}"
        placeholder="Buscar pregunta..."
        class="border rounded px-3 py-2 text-sm dark:bg-gray-700 dark:text-white"
        oninput="document.getElementById('faq-filter-form').submit()" />

    <select name="faq_category_id" class="border rounded px-3 py-2 text-sm dark:bg-gray-700 dark:text-white"
        onchange="document.getElementById('faq-filter-form').submit()">
        <option value="">Todas las categorías</option>
        @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('faq_category_id') == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>

    <a href="{{ route('admin.faqs.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
        + Agregar FAQ
    </a>
</form>

    </div>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Pregunta</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Categoría</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Respuesta</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($faqs as $faq)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">{{ $faq->question }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $faq->category->name ?? 'Sin categoría' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">{{ $faq->answer }}</td>
                        <td class="px-6 py-4 text-sm text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.faqs.edit', $faq->id) }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded transition">
                                Editar
                            </a>
                            <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta FAQ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded transition">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No hay FAQs encontradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
