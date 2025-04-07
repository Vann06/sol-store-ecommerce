<!DOCTYPE html>
<html>
<head>
    <title>Productos</title>
</head>
<body>
    <h1>Lista de Productos</h1>
    @foreach ($products as $product)
        <p><strong>{{ $product->name }}</strong> - ${{ $product->price }}
        <a href="{{ route('admin.products.edit', $product->id) }}" class="text-blue-500 hover:underline">Edit</a>
        @endforeach
</body>
</html>
