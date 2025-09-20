// http.js - Service HTTP para JWT authentication
import axios from 'axios';

// Base URL para el API (puerto 8000 donde está Laravel)
const BASE_URL = 'http://localhost:8000/api';

// Crear instancia de axios
const http = axios.create({
  baseURL: BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Request interceptor - añadir token JWT a todas las peticiones
http.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('access_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor - manejar refreshing de tokens
http.interceptors.response.use(
  (response) => {
    return response;
  },
  async (error) => {
    const original = error.config;

    // Si recibimos 401 y no hemos intentado refrescar aún
    if (error.response?.status === 401 && !original._retry) {
      original._retry = true;

      try {
        const refreshToken = localStorage.getItem('refresh_token');
        if (refreshToken) {
          const response = await axios.post(`${BASE_URL}/auth/refresh`, {
            refresh_token: refreshToken
          });
          
          const { access_token } = response.data;
          localStorage.setItem('access_token', access_token);
          
          // Reintentar la petición original con el nuevo token
          original.headers.Authorization = `Bearer ${access_token}`;
          return http(original);
        }
      } catch (refreshError) {
        // Si el refresh falla, limpiar tokens y redirigir al login
        localStorage.removeItem('access_token');
        localStorage.removeItem('refresh_token');
        window.location.href = '/login';
      }
    }

    return Promise.reject(error);
  }
);

export default http;