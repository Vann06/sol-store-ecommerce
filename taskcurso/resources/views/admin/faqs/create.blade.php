@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto p-4">
    <h2 class="text-2xl font-bold text-red-700 mb-6">Crear Nueva FAQ</h2>

    <form method="POST" action="{{ route('admin.faqs.store') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-200 mb-1 font-semibold">Pregunta</label>
            <input type="text" name="question" value="{{ old('question') }}" required
                class="w-full p-3 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-200 mb-1 font-semibold">Respuesta</label>
            <textarea name="answer" rows="4" required
                class="w-full p-3 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">{{ old('answer') }}</textarea>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 dark:text-gray-200 mb-1 font-semibold">Categoría</label>
            <select name="faq_category_id" required
                class="w-full p-3 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">Seleccione una categoría</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('faq_category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.faqs.index') }}"
                class="px-4 py-2 rounded border text-gray-700 dark:text-gray-300 dark:border-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700">
                Cancelar
            </a>
            <button type="submit"
                class="px-5 py-2 bg-red-700 hover:bg-red-800 text-white rounded font-semibold">
                Guardar
            </button>
        </div>
    </form>
</div>
@endsection
