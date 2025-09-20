<!DOCTYPE html>
<html lang="es"
      :class="{ 'dark': darkMode }"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind + darkMode config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {}
        }
      }
    </script>

    <!-- Alpine.js -->
    <script src="https://unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100">

  <!-- Botón modo oscuro/claro -->
  <div class="p-4">
      <button @click="darkMode = !darkMode"
              class="px-4 py-2 bg-gray-200 dark:bg-gray-700 dark:text-white rounded-lg text-sm">
          Cambiar Modo
      </button>
  </div>

  <div class="flex h-screen">
      <!-- Sidebar -->
      <aside class="w-64 bg-white dark:bg-gray-800 border-r dark:border-gray-700 p-4">
          <div class="flex items-center space-x-2">
              <img src="{{ asset('images/SOL_LOGO.jpg') }}" alt="Logo" class="h-12 w-12 rounded-full">
              <span class="text-xl font-bold text-red-700">Admin</span>
          </div>
          <nav class="mt-8 space-y-2">
              <a href="{{ route('admin.products.index') }}"
                 class="block px-3 py-2 rounded-lg {{ request()->is('admin/products*') ? 'bg-red-700 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                  Productos
              </a>
              <a href="{{ route('admin.faqs.index') }}"
                 class="block px-3 py-2 rounded-lg {{ request()->is('admin/faqs*') ? 'bg-red-700 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                  FAQs
              </a>
              <a href="{{ route('admin.categories.index') }}"
                 class="block px-3 py-2 rounded-lg {{ request()->is('admin/categories*') ? 'bg-red-700 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                  Categorías
              </a>
              <a href="{{ route('admin.themes.index') }}"
                 class="block px-3 py-2 rounded-lg {{ request()->is('admin/themes*') ? 'bg-red-700 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                  Temáticas
              </a>
              <a href="{{ route('admin.inventario.index') }}"
                 class="block px-3 py-2 rounded-lg {{ request()->is('admin/inventario*') ? 'bg-red-700 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                  Inventario
              </a>
              <a href="{{ route('admin.orders.index') }}"
                 class="block px-3 py-2 rounded-lg {{ request()->is('admin/orders*') ? 'bg-red-700 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                  Pedidos
              </a>
          </nav>
      </aside>

      <!-- Contenido principal -->
      <main class="flex-1 p-6 overflow-y-auto">
          @yield('content')
      </main>
  </div>

</body>
</html>

