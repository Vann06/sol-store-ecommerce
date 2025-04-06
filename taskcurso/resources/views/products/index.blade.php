<!DOCTYPE html>
<html>
<head>
    <title>Productos</title>
</head>
<body>
    <h1>Lista de Productos</h1>
    @foreach ($products as $product)
        <p><strong>{{ $product->name }}</strong> - ${{ $product->price }}
        <a href="{{ route('products.edit', $product) }}">Editar</a></p>
    @endforeach
</body>
</html>
