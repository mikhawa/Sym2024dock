# Sym2024dock

    symfony new Sym2024dock --version=lts --webapp

    cd Sym2024dock


## Docker

Après avoir viré les autres fichiers docker.
`docker-compose.yaml`

```yaml

services:
  php:
    image: php:8.2-fpm
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

    docker-compose up -d

    docker-compose down

Création de Post

Puis appel des fixtures :

    composer require orm-fixtures --dev

