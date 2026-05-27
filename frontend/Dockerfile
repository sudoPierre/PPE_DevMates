# ── Étape 1 : Construction de l'application React avec Vite ──
FROM node:20-alpine AS builder

WORKDIR /app

# Copie des manifestes de dépendances en premier pour profiter du cache Docker
COPY package.json package-lock.json ./
RUN npm ci --silent

# Copie des sources et construction de la version de production
COPY . .
RUN npm run build

# ── Étape 2 : Serveur Nginx pour les fichiers statiques ──
FROM nginx:1.25-alpine

# Suppression de la configuration Nginx par défaut
RUN rm /etc/nginx/conf.d/default.conf

# Configuration personnalisée (SPA + proxy vers le backend)
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Copie des fichiers construits depuis l'étape précédente
COPY --from=builder /app/dist /usr/share/nginx/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
