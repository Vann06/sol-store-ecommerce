# Usamos Node.js 18 en su variante Alpine para reducir el tamaño de la imagen
FROM node:18-alpine

# Instalar dependencias del sistema
RUN apk add --no-cache \
    curl \
    git \
    chromium \
    nss \
    freetype \
    harfbuzz \
    ca-certificates \
    ttf-freefont

# Establece el directorio de trabajo dentro del contenedor
WORKDIR /app

# Copia los archivos de definición de dependencias
COPY package*.json ./

# Instalar dependencias del proyecto
RUN npm ci

# Instalar Playwright y sus dependencias
RUN npx playwright install --with-deps chromium

# Copia el resto del código fuente de la aplicación al contenedor
COPY . .

# Expone el puerto 5173
EXPOSE 5173

# Comando por defecto: inicia el servidor de desarrollo
CMD ["npm", "run", "dev", "--", "--host"]