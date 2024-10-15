# Sym2024dock

    symfony new Sym2024dock --version=lts --webapp

    cd Sym2024dock

## .env

! `mysql:3306` est le nom du service dans le fichier `docker-compose.yaml`

    DATABASE_URL="mysql://user:password@mysql:3306/symfony"

## Docker

Création du fichier `Dockerfile`

```dockerfile
FROM php:8.2-fpm

# Installer les extensions PDO et MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installer APCu
RUN pecl install apcu && docker-php-ext-enable apcu

# Installer Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Configurer Xdebug
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Activer et configurer OPcache
RUN docker-php-ext-install opcache \
    && echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=1" >> /usr/local/etc/php/conf.d/opcache.ini



```

Après avoir viré les autres fichiers docker, création du fichier
`docker-compose.yaml`

```yaml

services:
  php:
    build:
      context: .
      dockerfile: Dockerfile
    volumes:
      - ./:/var/www/html/
      - ./var:/var/www/html/var
      - ./public:/var/www/html/public
      - ./src:/var/www/html/src
      - ./config:/var/www/html/config
      - ./templates:/var/www/html/templates
      - ./migrations:/var/www/html/migrations
      - ./vendor:/var/www/html/vendor

    networks:
      - symfony-network

  nginx:
    image: nginx:latest
    volumes:
      - ./public:/public
      - ./nginx/default.conf:/etc/nginx/conf.d/default.conf
    ports:
      - "8080:80"
    networks:
      - symfony-network

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: symfony
      MYSQL_USER: user
      MYSQL_PASSWORD: password
    ports:
      - "3306:3306"
    volumes:
      - mysql-data:/var/lib/mysql
    networks:
      - symfony-network

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    environment:
      PMA_HOST: mysql
      MYSQL_USER: user
      MYSQL_PASSWORD: password
    ports:
      - "8081:80"
    networks:
      - symfony-network

volumes:
  mysql-data:

networks:
  symfony-network:


```

`nginx/default.conf`

```nginx
server {
    listen 80;
    server_name localhost;

    root /var/www/html/public;
    index index.php index.html index.htm;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ \.php$ {
        # include snippets/fastcgi-php.conf;
        fastcgi_pass php:9000;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

## Lancement de Docker

    docker-compose down
    docker-compose build
    docker-compose up -d

## Pour utiliser PHP de l'intérieur du container

    docker-compose exec php bash

Par exemple pour installer les dépendances :

    composer install

Pour quitter le container :

    exit

## Création de Post
    
        php bin/console make:entity Post
    
        php bin/console make:migration
    
        php bin/console doctrine:migrations:migrate

Puis appel des fixtures :

    docker-compose exec php bash

    composer require orm-fixtures --dev

    php bin/console make:fixture

    php bin/console doctrine:fixtures:load

    exit
