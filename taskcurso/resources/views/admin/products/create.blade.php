<x-layout>
    <div class="p-8">
        <h1 class="text-2xl font-semibold text-red-700 mb-2">Add Product</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Admin › Panel</p>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4 max-w-4xl">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <input name="nombre" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" required />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Base Price</label>
                    <input type="number" step="0.01" name="precio_base" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" required />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                    <select name="id_categoria" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" required>
                        <option value="">-- Select Category --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Theme</label>
                    <select name="id_tematica" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" required>
                        <option value="">-- Select Theme --</option>
                        @foreach ($themes as $theme)
                            <option value="{{ $theme->id }}">{{ $theme->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600">
                        <option value="activo">Active</option>
                        <option value="inactivo">Inactive</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Available Quantity</label>
                    <input type="number" name="stock" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Image</label>
                    <input type="file" name="imagen" class="w-full mt-1 border rounded-md dark:bg-gray-700 dark:border-gray-600" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                <textarea name="descripcion" rows="3" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600"></textarea>
            </div>

            <button type="submit" class="bg-red-700 text-white px-6 py-2 rounded-lg hover:bg-red-800">
                Save Product
            </button>
        </form>
    </div>
</x-layout>
