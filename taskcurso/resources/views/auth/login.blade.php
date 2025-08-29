<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
  <style>body{min-height:100vh;display:flex;align-items:center;justify-content:center;background:#f3f4f6}</style>
</head>
<body>
  <div class="w-full max-w-md bg-white p-6 rounded shadow">
    <h1 class="text-xl font-semibold mb-4">Iniciar sesión</h1>
    @if($errors->any())
      <div class="mb-3 text-red-600">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ url('/login') }}">
      @csrf
      <div class="mb-3">
        <label class="block mb-1">Email</label>
        <input name="email" type="email" value="{{ old('email') }}" class="w-full border px-3 py-2 rounded" required />
      </div>
      <div class="mb-3">
        <label class="block mb-1">Contraseña</label>
        <input name="password" type="password" class="w-full border px-3 py-2 rounded" required />
      </div>
      <div class="flex items-center justify-between">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Entrar</button>
        <a href="/" class="text-sm text-gray-600">Volver</a>
      </div>
    </form>
  </div>
</body>
</html>
