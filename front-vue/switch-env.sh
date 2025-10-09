#!/bin/bash

# Script para cambiar entre entornos de desarrollo
# Uso: ./switch-env.sh [docker|local|production]

if [ $# -eq 0 ]; then
    echo "❌ Error: Debes especificar un entorno"
    echo ""
    echo "Uso: ./switch-env.sh [docker|local|production]"
    echo ""
    echo "Entornos disponibles:"
    echo "  docker      - Desarrollo con Docker (usa Nginx en puerto 80)"
    echo "  local       - Desarrollo local (backend directo en puerto 8000)"
    echo "  production  - Configuración de producción"
    echo ""
    exit 1
fi

ENV=$1
ENV_FILE=""

case $ENV in
    docker)
        ENV_FILE=".env"
        API_URL="http://localhost/api"
        echo "🐳 Cambiando a entorno Docker..."
        ;;
    local)
        ENV_FILE=".env.local"
        API_URL="http://localhost:8000/api"
        echo "💻 Cambiando a entorno Local..."
        ;;
    production)
        ENV_FILE=".env.production"
        API_URL="https://tu-dominio.com/api"
        echo "🚀 Cambiando a entorno Producción..."
        ;;
    *)
        echo "❌ Entorno desconocido: $ENV"
        echo "Entornos disponibles: docker, local, production"
        exit 1
        ;;
esac

# Verificar que el archivo existe
if [ ! -f "$ENV_FILE" ]; then
    echo "❌ Error: El archivo $ENV_FILE no existe"
    exit 1
fi

# Copiar el archivo de entorno
cp "$ENV_FILE" .env

echo "✅ Archivo .env actualizado desde $ENV_FILE"
echo "📡 API Base URL: $API_URL"
echo ""
echo "⚠️  IMPORTANTE: Reinicia el servidor de desarrollo para que los cambios surtan efecto"
echo "   npm run dev"
echo ""

# Mostrar la configuración actual
echo "📄 Configuración actual:"
grep "VITE_API_BASE_URL" .env
echo ""
