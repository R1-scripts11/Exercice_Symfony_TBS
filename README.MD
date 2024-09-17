# Symfony Docker Setup with MySQL, PHP, and Nginx

Ce projet configure un environnement Docker pour exécuter une application Symfony avec MySQL, PHP-FPM, et Nginx.
### 1. Extraire les Fichiers
https://we.tl/t-fIwqzzOfWF
Assurez-vous d'extraire les fichiers `symfony_stack_images.tar` et `mysql_data.tar` si ce n'est pas déjà fait.

### 2. Charger les Images Docker

Chargez les images Docker à partir du fichier `symfony_stack_images.tar` :

docker load -i /path/to/symfony_stack_images.tar

### 3. Créez un volume Docker pour stocker les données MySQL :
docker volume create symfony_db_data

### 4. Restaurer les Données MySQL
docker run --rm -v symfony_db_data:/var/lib/mysql -v /path/to/mysql_data:/backup ubuntu bash -c "cd /var/lib/mysql && tar xvf /backup/mysql_data.tar --strip 1"

### 5. Lancer les Conteneurs Docker (dans le dossier ou se trouve le docker-compose)
docker-compose up -d
