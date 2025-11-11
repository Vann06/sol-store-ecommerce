# Usamos Node.js 18 en su variante Alpine para reducir el tamaño de la imagen
FROM node:18-alpine

# Establece el directorio de trabajo dentro del contenedor
WORKDIR /app

# Copia los archivos de definición de dependencias (package.json y package-lock.json)
COPY package*.json ./

# Instala las dependencias definidas en el package.json (incluyendo Vue y Tailwind)
RUN npm install && \
     npm install vue-router@4 axios && \
     npm install @fortawesome/fontawesome-svg-core @fortawesome/free-solid-svg-icons @fortawesome/vue-fontawesome@latest && \
     npm install js-cookie @types/js-cookie


# Copia el resto del código fuente de la aplicación al contenedor
COPY . .

RUN apk update && apk add --no-cache curl iputils

# Expone el puerto 5173, que es el que se utilizará para el servidor de desarrollo (Vite)
EXPOSE 5173

# Comando por defecto: inicia el servidor de desarrollo
CMD ["npm", "run", "dev", "--", "--host"]
