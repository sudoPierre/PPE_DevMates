import axios from 'axios';

/**
 * Instance Axios partagée pour l'API DevMates
 * Injecte automatiquement le token JWT et gère les 401 globalement
 */
const api = axios.create({
  baseURL: '/api',
  headers: { 'Content-Type': 'application/json' },
  timeout: 10000,
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('dm_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

api.interceptors.response.use(
  (response) => response,
  (error) => {
    // Token expiré ou invalide : nettoyage et redirection vers la page de connexion
    if (error.response?.status === 401) {
      localStorage.removeItem('dm_token');
      localStorage.removeItem('dm_user');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default api;
