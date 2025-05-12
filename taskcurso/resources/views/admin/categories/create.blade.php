@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.categories.index') }}" class="text-red-700 hover:text-red-900 text-xl">←</a>
        <h1 class="text-2xl font-semibold text-red-700 mb-1">Agregar Categoría</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Admin › Categorías</p>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium">Nombre</label>
            <input name="name" required class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" />
        </div>

        <div>
            <label class="block text-sm font-medium">Imagen</label>
            <input 
                type="file" 
                name="imagen" 
                id="imagenInput"
                class="w-full mt-1 border rounded-md dark:bg-gray-700 dark:border-gray-600"
                accept="image/*"
                onchange="previewImage(event)"
            />
            <div id="previewContainer" class="mt-2 hidden">
                <span class="text-sm text-gray-500">Vista previa:</span>
                <img id="previewImg" src="#" alt="Vista previa" class="h-20 w-20 mt-1 rounded shadow border">
            </div>
        </div>

        <div class="text-right">
            <button type="submit" class="bg-red-700 text-white px-6 py-2 rounded-lg hover:bg-red-800">
                Guardar Categoría
            </button>
        </div>
    </form>
</div>

<script>
function previewImage(event) {
    const input = event.target
    const previewContainer = document.getElementById('previewContainer')
    const previewImg = document.getElementById('previewImg')

    if (input.files && input.files[0]) {
        const reader = new FileReader()
        reader.onload = (e) => {
            previewImg.src = e.target.result
            previewContainer.classList.remove('hidden')
        }
        reader.readAsDataURL(input.files[0])
    }
}
</script>
@endsection
