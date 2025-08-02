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

<<<<<<< HEAD
# Instala las dependencias definidas en el package.json (incluyendo Vue y Tailwind)
RUN npm install && \
     npm install vue-router@4 axios && \
     npm install @fortawesome/fontawesome-svg-core @fortawesome/free-solid-svg-icons @fortawesome/vue-fontawesome@latest && \
     npm install js-cookie @types/js-cookie
=======
# Instalar dependencias del proyecto
RUN npm ci
>>>>>>> bf1ed8b (dockerChange)

# Instalar Playwright y sus dependencias
RUN npx playwright install --with-deps chromium

# Copia el resto del código fuente de la aplicación al contenedor
COPY . .

# Expone el puerto 5173
EXPOSE 5173

# Comando por defecto: inicia el servidor de desarrollo
CMD ["npm", "run", "dev", "--", "--host"]