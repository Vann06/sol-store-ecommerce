#!/bin/sh

#realiza la migración de la DB por primera vez
php artisan migrate

#deja el pod ejecutandose en 2do plano
exec apache2-foreground

echo "Migraciones aplicadas correctamente."
