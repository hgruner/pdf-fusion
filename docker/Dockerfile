# Utilisez une image de base avec PHP et Nginx
FROM php:8.2-fpm
# Mettez à jour les paquets et installez Nginx
RUN apt-get update && apt-get install -y \
    nginx 

# Installer pdftk
RUN apt-get update && apt-get install -y pdftk

# Copiez la configuration Nginx
COPY nginx/default /etc/nginx/sites-available/default

# Copiez les fichiers PHP de votre application
COPY src/ /var/www/html

RUN mkdir -p /var/www/html/uploads && chmod 777 /var/www/html/uploads

# Exposez le port 80 pour le serveur web
EXPOSE 80

# Commande par défaut pour exécuter Nginx et PHP-FPM
CMD service nginx start && php-fpm
# Vous pouvez également ajouter d'autres configurations et extensions PHP selon vos besoins
