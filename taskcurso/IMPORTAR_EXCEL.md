# 📊 Guía de Importación desde Excel

Esta guía explica cómo poblar la base de datos desde archivos Excel/CSV.

## 🎯 Opciones Disponibles

### Opción 1: Script PHP Independiente
**Archivo:** `import-excel.php`  
**Ventajas:** 
- No requiere librerías adicionales
- Ejecución directa
- Fácil de personalizar

**Uso:**
```bash
php import-excel.php
```

### Opción 2: Seeder de Laravel
**Archivo:** `database/seeders/ExcelImportSeeder.php`  
**Ventajas:**
- Integrado con Laravel
- Manejo automático de transacciones
- Mejor para múltiples tablas

**Uso:**
```bash
php artisan db:seed --class=ExcelImportSeeder
```

---

## 📝 Preparación del Archivo Excel

### 1. Formato del Excel
Tu archivo Excel debe tener:
- **Primera fila:** Encabezados (nombres de columnas)
- **Filas siguientes:** Datos a importar
- **Sin celdas combinadas**
- **Formato consistente**

Ejemplo:
```
| Nombre      | Descripción           | Precio | Stock | Categoría |
|-------------|-----------------------|--------|-------|-----------|
| Camiseta    | Camiseta de algodón   | 29.99  | 50    | Ropa      |
| Pantalón    | Pantalón de mezclilla | 49.99  | 30    | Ropa      |
| Zapatillas  | Zapatillas deportivas | 79.99  | 25    | Calzado   |
```

### 2. Exportar a CSV
1. Abre tu archivo Excel
2. **Archivo → Guardar como**
3. Selecciona formato: **CSV (delimitado por comas) (*.csv)**
4. Guarda el archivo

---

## 🚀 Configuración

### Para `import-excel.php`:

```php
// 1. Ubicación del archivo CSV
$csvFile = 'storage/app/productos.csv';

// 2. Tabla destino
$tableName = 'productos';

// 3. Mapeo de columnas (Excel → Base de datos)
$columnMapping = [
    'Nombre' => 'nombre',           // Columna Excel → Columna BD
    'Descripcion' => 'descripcion',
    'Precio' => 'precio',
    'Stock' => 'stock',
    'Categoria' => 'id_categoria',
];
```

### Para `ExcelImportSeeder.php`:

```php
// En el método run(), configura:
$importConfigs = [
    'productos' => [
        'file' => 'storage/app/imports/productos.csv',
        'table' => 'productos',
        'mapping' => [
            'Nombre' => 'nombre',
            'Precio' => 'precio',
            // ...
        ],
        // Transformaciones de datos
        'transforms' => [
            'precio' => fn($value) => floatval(str_replace(',', '', $value)),
        ],
        // Relaciones (busca o crea registros relacionados)
        'relations' => [
            'id_categoria' => ['table' => 'categories', 'column' => 'nombre'],
        ],
    ],
];
```

---

## 📦 Proceso de Importación

### Usando el Script PHP

```bash
# 1. Coloca tu CSV en storage/app/
cp tu-archivo.csv taskcurso/storage/app/productos.csv

# 2. Edita import-excel.php y ajusta la configuración
code taskcurso/import-excel.php

# 3. Ejecuta el script
cd taskcurso
php import-excel.php
```

### Usando el Seeder

```bash
# 1. Crea la carpeta de importación
mkdir -p taskcurso/storage/app/imports

# 2. Coloca tu CSV
cp tu-archivo.csv taskcurso/storage/app/imports/productos.csv

# 3. Edita el seeder y ajusta la configuración
code taskcurso/database/seeders/ExcelImportSeeder.php

# 4. Ejecuta el seeder
cd taskcurso
php artisan db:seed --class=ExcelImportSeeder
```

---

## 🔧 Características Avanzadas

### 1. Transformación de Datos

Puedes transformar los datos antes de insertarlos:

```php
'transforms' => [
    // Convertir precio (eliminar comas, convertir a decimal)
    'precio' => fn($value) => floatval(str_replace(',', '', $value)),
    
    // Convertir a entero
    'stock' => fn($value) => intval($value),
    
    // Limpiar email
    'email' => fn($value) => strtolower(trim($value)),
    
    // Limpiar teléfono (solo números)
    'telefono' => fn($value) => preg_replace('/[^0-9]/', '', $value),
    
    // Fecha
    'fecha' => fn($value) => date('Y-m-d', strtotime($value)),
],
```

### 2. Relaciones Automáticas

El script puede buscar o crear automáticamente registros relacionados:

```php
'relations' => [
    'id_categoria' => [
        'table' => 'categories',      // Tabla donde buscar
        'column' => 'nombre'          // Columna a comparar
    ],
],
```

Si la categoría no existe, se creará automáticamente.

### 3. Valores por Defecto

```php
'defaults' => [
    'password' => bcrypt('password123'),
    'estado' => 'activo',
    'visible' => true,
],
```

### 4. Procesamiento por Lotes

Para archivos grandes, los datos se procesan en lotes:

```php
$batchSize = 100; // Procesar 100 registros a la vez
```

---

## 🎯 Ejemplos Prácticos

### Ejemplo 1: Importar Productos

**CSV: productos.csv**
```csv
Nombre,Descripción,Precio,Stock,Categoría,Tema
Camiseta Gamer,Camiseta 100% algodón,29990,50,Ropa,Videojuegos
Taza Star Wars,Taza cerámica 350ml,15990,100,Hogar,Películas
Figura Naruto,Figura coleccionable,39990,30,Juguetes,Anime
```

**Configuración:**
```php
'productos' => [
    'file' => 'storage/app/imports/productos.csv',
    'table' => 'productos',
    'mapping' => [
        'Nombre' => 'nombre',
        'Descripción' => 'descripcion',
        'Precio' => 'precio',
        'Stock' => 'stock',
        'Categoría' => 'id_categoria',
        'Tema' => 'id_tema',
    ],
    'transforms' => [
        'precio' => fn($v) => intval($v),
        'stock' => fn($v) => intval($v),
    ],
    'relations' => [
        'id_categoria' => ['table' => 'categories', 'column' => 'nombre'],
        'id_tema' => ['table' => 'themes', 'column' => 'nombre'],
    ],
    'defaults' => [
        'visible' => true,
    ],
],
```

### Ejemplo 2: Importar Usuarios

**CSV: usuarios.csv**
```csv
Nombre,Email,Teléfono,DNI,Rol
Juan Pérez,juan@email.com,+56912345678,12345678-9,Cliente
María García,maria@email.com,+56987654321,98765432-1,Cliente
```

**Configuración:**
```php
'usuarios' => [
    'file' => 'storage/app/imports/usuarios.csv',
    'table' => 'usuarios',
    'mapping' => [
        'Nombre' => 'nombre',
        'Email' => 'email',
        'Teléfono' => 'telefono',
        'DNI' => 'dni',
    ],
    'transforms' => [
        'email' => fn($v) => strtolower(trim($v)),
        'telefono' => fn($v) => preg_replace('/[^0-9]/', '', $v),
    ],
    'defaults' => [
        'password' => bcrypt('password123'),
        'estado' => 'activo',
    ],
],
```

---

## ⚠️ Problemas Comunes

### 1. Encoding de Caracteres

**Problema:** Caracteres especiales (ñ, tildes) aparecen mal.

**Solución:** 
- Guarda el CSV con encoding UTF-8
- En Excel: **Guardar como → Más opciones → Herramientas → Opciones web → Codificación → UTF-8**

### 2. Separador Incorrecto

**Problema:** Los datos no se separan correctamente.

**Solución:**
```php
// Cambiar el delimitador (puede ser ; en lugar de ,)
$data = $this->readCSV($filePath, ';');
```

### 3. Datos Duplicados

**Problema:** Se insertan registros duplicados.

**Solución:**
```php
// Verificar antes de insertar
$exists = DB::table($tableName)
    ->where('email', $mappedData['email'])
    ->exists();

if (!$exists) {
    DB::table($tableName)->insert($mappedData);
}
```

### 4. Relaciones No Encontradas

**Problema:** Una categoría o relación no existe.

**Solución:** El script automáticamente crea el registro relacionado. Si no quieres esto:

```php
// Buscar sin crear
$category = DB::table('categories')->where('nombre', $value)->first();
if ($category) {
    $mappedData['id_categoria'] = $category->id;
} else {
    $this->command->warn("Categoría no encontrada: $value");
    continue; // Saltar este registro
}
```

---

## 🧪 Testing

Antes de importar datos reales:

```bash
# 1. Backup de la base de datos
php artisan db:backup

# 2. Crear un CSV de prueba con 5-10 registros

# 3. Ejecutar la importación

# 4. Verificar los datos en la base de datos
php artisan tinker
>>> DB::table('productos')->count()
>>> DB::table('productos')->latest()->first()

# 5. Si algo sale mal, restaurar backup
php artisan db:restore
```

---

## 📊 Monitoreo

Los errores se registran en:
- **Log de Laravel:** `storage/logs/laravel.log`
- **Consola:** Muestra progreso en tiempo real

```bash
# Ver logs en tiempo real
tail -f storage/logs/laravel.log
```

---

## 💡 Tips

1. **Divide archivos grandes:** Si tienes más de 10,000 registros, divide el archivo en varios CSV más pequeños.

2. **Limpia los datos primero:** Revisa que no haya:
   - Celdas vacías
   - Saltos de línea dentro de celdas
   - Caracteres especiales raros

3. **Prueba con pocos registros:** Siempre prueba primero con 5-10 registros.

4. **Usa transacciones:** El script usa transacciones automáticamente, así que si algo falla, no se inserta nada.

5. **Valida los datos:** Puedes agregar validaciones:
   ```php
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
       throw new \Exception("Email inválido: $email");
   }
   ```

---

## 🔗 Recursos

- [Documentación Laravel Seeding](https://laravel.com/docs/seeding)
- [Documentación CSV PHP](https://www.php.net/manual/es/function.fgetcsv.php)
- [Laravel Excel Package](https://laravel-excel.com/) (opcional, más avanzado)

---

## 📞 Soporte

Si tienes problemas:
1. Revisa los logs en `storage/logs/laravel.log`
2. Verifica que el archivo CSV esté bien formateado
3. Asegúrate de que las columnas del mapeo coincidan exactamente
4. Ejecuta `php artisan tinker` para inspeccionar la base de datos

¡Listo! Ahora puedes poblar tu base de datos fácilmente desde Excel. 🎉
