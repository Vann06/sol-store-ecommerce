<!DOCTYPE html>
<html>
<head>
    <title>Editar Producto</title>
</head>
<body>
    <h1>Editar producto</h1>
    <form action="{{ route('products.update', $product->id) }}" method="POST">
        @csrf
        @method('PUT')
        <label>Nombre:</label>
        <input type="text" name="name" value="{{ $product->name }}"><br>

        <label>Descripción:</label>
        <textarea name="description">{{ $product->description }}</textarea><br>

        <label>Precio:</label>
        <input type="number" name="price" step="0.01" value="{{ $product->price }}"><br>

        <button type="submit">Guardar</button>
    </form>
</body>
</html>
