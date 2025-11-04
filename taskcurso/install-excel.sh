#!/bin/bash
# Script para instalar maatwebsite/excel en un contenedor Docker existente
# Uso: ./install-excel.sh

set -e

echo "🚀 Instalando Maatwebsite Excel para Laravel..."

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Función para imprimir mensajes
print_message() {
    echo -e "${GREEN}[✓]${NC} $1"
}

print_error() {
    echo -e "${RED}[✗]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[!]${NC} $1"
}

# 1. Verificar que estamos en el directorio correcto
if [ ! -f "composer.json" ]; then
    print_error "No se encontró composer.json. Asegúrate de estar en el directorio raíz del proyecto."
    exit 1
fi

print_message "Directorio del proyecto encontrado"

# 2. Instalar maatwebsite/excel
print_message "Instalando maatwebsite/excel via Composer..."
composer require maatwebsite/excel

if [ $? -ne 0 ]; then
    print_error "Error al instalar maatwebsite/excel"
    exit 1
fi

print_message "Maatwebsite Excel instalado correctamente"

# 3. Publicar configuración
print_message "Publicando archivo de configuración..."
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config

if [ $? -eq 0 ]; then
    print_message "Configuración publicada en config/excel.php"
else
    print_warning "No se pudo publicar la configuración (puede que ya exista)"
fi

# 4. Verificar instalación
print_message "Verificando instalación..."
if grep -q "Maatwebsite\\\\Excel" composer.json; then
    print_message "✅ Maatwebsite Excel está instalado correctamente"
else
    print_error "❌ Parece que hubo un problema con la instalación"
    exit 1
fi

# 5. Crear directorio de exports si no existe
print_message "Creando directorio para exports..."
mkdir -p app/Exports
mkdir -p storage/app/exports

# 6. Verificar que ReportesExport existe
if [ ! -f "app/Exports/ReportesExport.php" ]; then
    print_warning "No se encontró app/Exports/ReportesExport.php"
    print_warning "Asegúrate de crear este archivo con la clase de exportación"
fi

# 7. Limpiar caché
print_message "Limpiando caché de Laravel..."
php artisan config:clear
php artisan cache:clear

# 8. Ajustar permisos
print_message "Ajustando permisos..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

print_message "🎉 Instalación completada exitosamente!"
echo ""
echo "Próximos pasos:"
echo "1. Asegúrate de tener app/Exports/ReportesExport.php"
echo "2. Verifica las rutas en web.php"
echo "3. Prueba la exportación con: php artisan tinker"
echo "   > Excel::download(new App\Exports\ReportesExport('ventas', now()->subMonth(), now()), 'test.xlsx');"
echo ""
print_message "✅ Todo listo para usar Excel exports!"