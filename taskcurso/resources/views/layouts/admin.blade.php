
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white dark:bg-gray-800 border-r dark:border-gray-700 p-4">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/SOL_LOGO.jpg') }}" alt="Logo" class="h-12 w-12 rounded-full">
                <span class="text-xl font-bold text-red-700">Admin</span>
            </div>
            <nav class="mt-8 space-y-2">
                <a href="{{ route('admin.products.index') }}" class="block px-3 py-2 rounded-lg {{ request()->is('admin/products*') ? 'bg-red-700 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                    Productos
                </a>
                <a href="{{ route('admin.faqs.index') }}" class="block px-3 py-2 rounded-lg {{ request()->is('admin/faqs*') ? 'bg-red-700 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                    FAQs
                </a>
            </nav>
        </aside>

        <!-- Main content -->
        <main class="flex-1 p-6 overflow-y-auto">
            @yield('content')
        </main>
    </div>
</body>
</html>