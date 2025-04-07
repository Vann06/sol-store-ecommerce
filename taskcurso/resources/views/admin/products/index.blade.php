<x-layout>
    <div class="flex h-screen bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white dark:bg-gray-800 border-r dark:border-gray-700 p-4">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/SOL_LOGO.jpg') }}" alt="Logo" class="h-12 w-12 rounded-full">
                <span class="text-xl font-bold text-red-700">Admin</span>
            </div>
            <nav class="mt-8 space-y-2">
                <a href="{{ route('admin.products.index') }}" class="flex items-center px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-red-700">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3h12a1 1 0 011 1v12a1..."></path></svg>
                    Products
                </a>
                <a href="#" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="..."></path></svg>
                    Pedidos
                </a>
            </nav>
        </aside>

        <!-- Main -->
        <div class="flex-1 p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-semibold">Products</h1>
                    <p class="text-sm text-gray-500">Admin &rsaquo; Panel</p>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.products.create') }}" class="bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-800">
                        Add product
                    </a>
                    <form id="searchForm" method="GET" action="{{ route('admin.products.index') }}" class="flex items-center space-x-2">
                        <input
                            type="text"
                            name="search"
                            id="searchInput"
                            value="{{ request('search') }}"
                            placeholder="Search products"
                            class="px-3 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600"
                        />
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border-collapse">
                    <thead>
                        <tr class="text-left border-b border-gray-200 dark:border-gray-700">
                            <th class="py-2 px-4">#</th>
                            <th class="py-2 px-4">Name</th>
                            <th class="py-2 px-4">Price</th>
                            <th class="py-2 px-4">Stock</th>
                            <th class="py-2 px-4">Category</th>
                            <th class="py-2 px-4">Theme</th>
                            <th class="py-2 px-4">Status</th>
                            <th class="py-2 px-4">Image</th>
                            <th class="py-2 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="py-2 px-4">{{ $loop->iteration }}</td>
                                <td class="py-2 px-4">{{ $product->nombre }}</td>
                                <td class="py-2 px-4">${{ number_format($product->precio_base, 2) }}</td>
                                <td class="py-2 px-4">{{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}</td>
                                <td class="py-2 px-4">{{ $product->category->name ?? 'N/A' }}</td>
                                <td class="py-2 px-4">{{ $product->theme->name ?? 'N/A' }}</td>
                                <td class="py-2 px-4">{{ ucfirst($product->status) }}</td>
                                <td class="py-2 px-4">
                                    @if ($product->imagen)
                                        <img src="{{ asset('storage/' . $product->imagen) }}" alt="Imagen" class="h-10 w-10 rounded">
                                    @else
                                        <span class="text-xs text-gray-500">No image</span>
                                    @endif
                                </td>
                                <td class="py-2 px-4">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="text-blue-500 hover:underline">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
    const searchInput = document.getElementById('searchInput');
    let timeout = null;

    searchInput.addEventListener('keyup', function () {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            const value = searchInput.value.trim();
            const url = new URL(window.location.href);
            if (value.length > 0) {
                url.searchParams.set('search', value);
            } else {
                url.searchParams.delete('search');
            }
            window.location.href = url.toString();
        }, 500); // espera medio segundo después de dejar de escribir
    });
</script>

</x-layout>
