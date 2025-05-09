@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-semibold text-red-700 mb-4">Preguntas Frecuentes</h1>

        @forelse ($faqs as $faq)
            <div class="mb-4 p-4 bg-white dark:bg-gray-800 rounded shadow">
                <h2 class="font-bold">{{ $faq->question }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $faq->answer }}</p>
                <span class="text-xs text-gray-400">Categoría: {{ $faq->category->name ?? 'Sin categoría' }}</span>
            </div>
        @empty
            <p class="text-gray-500">No hay preguntas aún.</p>
        @endforelse
    </div>
@endsection
