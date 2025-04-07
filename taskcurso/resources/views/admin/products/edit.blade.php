<x-layout>
    <div class="p-8 max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-red-700 mb-1">Edit Product</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Admin › Panel</p>
            </div>
            <!-- Botón Eliminar -->
            <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" onsubmit="return confirm('Are you sure you want to delete this product?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                    Delete
                </button>
            </form>
        </div>

        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Name</label>
                    <input name="nombre" value="{{ old('nombre', $product->nombre) }}" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" />
                </div>

                <div>
                    <label class="block text-sm font-medium">Price</label>
                    <input type="number" name="precio_base" value="{{ old('precio_base', $product->precio_base) }}" step="0.01" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" />
                </div>

                <div>
                    <label class="block text-sm font-medium">Category</label>
                    <select name="id_categoria" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ $product->id_categoria == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium">Theme</label>
                    <select name="id_tematica" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600">
                        @foreach ($themes as $theme)
                            <option value="{{ $theme->id }}" {{ $product->id_tematica == $theme->id ? 'selected' : '' }}>
                                {{ $theme->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium">Stock</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" />
                </div>

                <div>
                    <label class="block text-sm font-medium">Image</label>
                    <input type="file" name="imagen" class="w-full mt-1 border rounded-md dark:bg-gray-700 dark:border-gray-600" />

                    @if ($product->imagen)
                        <div class="mt-2">
                            <span class="text-sm text-gray-500">Current image:</span>
                            <img src="{{ asset('storage/' . $product->imagen) }}" alt="Imagen actual" class="h-20 w-20 mt-1 rounded shadow border">
                        </div>
                    @endif
                </div>

            </div>

            <div>
                <label class="block text-sm font-medium">Description</label>
                <textarea name="descripcion" rows="3" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600">{{ old('descripcion', $product->descripcion) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium">Status</label>
                <select name="status" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600">
                    <option value="activo" {{ $product->status == 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ $product->status == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>

            <button type="submit" class="bg-red-700 text-white px-6 py-2 rounded-lg hover:bg-red-800">
                Update Product
            </button>
        </form>
    </div>
</x-layout>
