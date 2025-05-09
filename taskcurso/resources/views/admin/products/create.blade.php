@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
         <a href="{{ route('admin.products.index') }}" class="text-red-700 hover:text-red-900 text-xl">
        ←
    </a>
        <h1 class="text-2xl font-semibold text-red-700 mb-1">Agregar Producto</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Admin › Panel</p>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Nombre</label>
                <input name="nombre" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" required />
            </div>

            <div>
                <label class="block text-sm font-medium">Precio Base</label>
                <input type="number" name="precio_base" step="0.01" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" required />
            </div>

            <div>
                <label class="block text-sm font-medium">Categoría</label>
                <select name="id_categoria" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" required>
                    <option value="">-- Selecciona una categoría --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium">Temática</label>
                <select name="id_tematica" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" required>
                    <option value="">-- Selecciona una temática --</option>
                    @foreach ($themes as $theme)
                        <option value="{{ $theme->id }}">{{ $theme->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium">Stock</label>
                <input type="number" name="stock" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" required />
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

        </div>

        <div>
            <label class="block text-sm font-medium">Descripción</label>
            <textarea name="descripcion" rows="3" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium">Estado</label>
            <select name="status" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600">
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
        </div>

        <div class="text-right">
            <button type="submit" class="bg-red-700 text-white px-6 py-2 rounded-lg hover:bg-red-800">
                Guardar Producto
            </button>
        </div>
    </form>
</div>
@endsection