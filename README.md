# 🛍️ Sol_Store_Ecomerce

**Sol_Store_Ecomerce** es una tienda de impresiones 3D que estamos desarrollando como página web para ofrecer productos personalizados. Este repositorio contiene todo el entorno de desarrollo necesario usando contenedores Docker.

## 👥 Colaboradores

- Vianka  
- Ricardo  
- Paula  
- Roberto  

## 📁 Estructura del Proyecto

```
DOCKERIZANDO_2/
├── docker/
├── front-vue/
├── taskcurso/
├── .gitattributes
├── .gitignore
└── docker-compose.yml
```

## 🧰 Tecnologías Utilizadas

- **Backend:** PHP con Laravel  
- **Base de Datos:** PostgreSQL  
- **Frontend:** Vue.js  
- **Contenedores:** Docker y Docker Compose

## ℹ️ Crea tus puntos env
Asegurate de crear tus .env apartir de los .env.example para que todo funcione al 100
en estas dos carpetas 
```
├── front-vue/
├── taskcurso/
```


## 🚀 Instrucciones para levantar el entorno

Asegúrate de tener instalado Docker y Docker Compose antes de comenzar.

### 1. Baja los contenedores anteriores (por si ya estaban corriendo)

```bash
docker compose down --remove-orphans
```

### 2. Reconstruye e inicia los contenedores en segundo plano

```bash
docker compose up --build -d
```

### 3. Entra al contenedor del backend

```bash
docker compose exec backend bash
```

### 4. Corre las migraciones y seeders para preparar la base de datos

```bash
php artisan migrate:fresh --seed
chmod -R 777 storage bootstrap/cache
```

### 5. Sal del contenedor

```bash
exit
```

### 6. Abre la aplicación en el navegador

[http://localhost/admin/products](http://localhost/admin/products)

---

¡Y listo! Ya tienes todo funcionando localmente 🚀
