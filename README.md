# WT Library

Sample Library web application on Symfony 6.4 

## Setup

### Run in Docker

1. Start dockerized application:

    ```
    docker-compose up
    ```

2. Open browser [http://localhost:7789](http://localhost:7789)  To open /Admin page use  username/password or user/pass


3. Run tests inside Docker:

    ```bash
    docker exec wt-library-app bash -c "
        php bin/console doctrine:database:drop --env=test --force --if-exists --no-interaction && 
        php bin/console doctrine:database:create --env=test --no-interaction && 
        php bin/console doctrine:migrations:migrate --env=test --no-interaction && 
        APP_ENV=test vendor/bin/phpunit tests/ && 
        php bin/console doctrine:database:drop --env=test --force --if-exists --no-interaction
    "
    ```

### Run Locally

1. Install dependencies:

    ```
    composer install
    ```

2. Set up database:

    ```
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    ```

3. Start local PHP server:

    ```
    php -S localhost:7789 -t public
    ```

4. Open browser [http://localhost:7789](http://localhost:7789)  To open /Admin page use  username/password or user/pass


### Run Tests Locally

1. Set up test environment:

    ```
    php bin/console doctrine:database:drop --env=test --force --if-exists --no-interaction
    php bin/console doctrine:database:create --env=test --no-interaction
    php bin/console doctrine:migrations:migrate --env=test --no-interaction
    ```

2. Run tests:

    ```
    APP_ENV=test vendor/bin/phpunit tests/
    ```

3. Drop test database:

    ```
    php bin/console doctrine:database:drop --env=test --force --if-exists --no-interaction
    ```


