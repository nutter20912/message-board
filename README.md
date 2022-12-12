# Build a simple message board app
## Packages
- [laravel/sanctum](https://github.com/laravel/sanctum)
- [DarkaOnLine/L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger)
- [zircote/swagger-php](https://github.com/zircote/swagger-php)
- [laravel/horizon](https://github.com/laravel/horizon)
- [pusher/pusher-http-php](https://github.com/pusher/pusher-http-php)

---
## Setup
### Config
- generate local env file
    ```=bash
    cp .env.example .env
    ```

- config env variable
    ```=env
    # mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=message_board
    DB_USERNAME=root
    DB_PASSWORD=password

    # redis
    REDIS_HOST=redis

    # queue
    QUEUE_CONNECTION=redis

    # broadcast
    BROADCAST_DRIVER=pusher

    # pusher
    PUSHER_APP_ID=
    PUSHER_APP_KEY=
    PUSHER_APP_SECRET=
    PUSHER_APP_CLUSTER=

    # l5-swagger
    L5_SWAGGER_GENERATE_ALWAYS=true
    ```

### Build
- build docker image (nginx, php-fpm, mysql, redis)
    ```=bash
    docker-compose up -d nginx
    ```

- create database
    ```=bash
    //attach shell
    docker exec -it <mysql container id> sh
    //connection
    mysql -u root -p
    //mysql cli
    mysql> CREATE DATABASE {database_name};
    ```
- database migrate
    ```=bash
    //attach shell
    docker exec -it <php-fpm container id> sh
    //資料庫 migration
    php artisan migrate
    ```

### Test
- Coverage Report In HTML
    - docker-compose.yml
    ```=yml
    #services.php-fpm
        ...
        environment:
            XDEBUG_MODE: '${SAIL_XDEBUG_MODE:-off}'
    ```

    - attach shell && composer script
    ```=zh
    // Generate code coverage report in HTML format
    composer run test-coverage
    ```

