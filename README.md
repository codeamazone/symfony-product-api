# Symfony Product API
A simple REST API for managing products, built with Symfony 5 and API Platform

## Requires:

- PHP 8 or higher
- [Composer](https://getcomposer.org/)
- a database, a web server 
=> For this project I used [xampp](https://www.apachefriends.org/de/index.html), which comes with Apache server and MariaDB



## Usage:
- Clone the repo into '/opt/lampp/htdocs/'

- Create a .env file based on the .env.example like this:
```
cp .env.example .env
```

- In the .env file, adjust the databse URL with your database user and password, as well as your database version. If you are using MariaDB, make sure you explicitly specify this when specifying the database version. Make sure that both DATABASE_URL and APP_SECRET are uncommented.

- Install the dependencies with Composer:
```
composer install
```

- Create the project database and the schema:
```
php bin/console doctrine:database:create

php bin/console doctrine:schema:create
```

- run the inbuilt php server:
``` 
php -S 127.0.0.1:8000 -t public
```

You'll find the API in your browser at http://127.0.0.1:8000/api

(If you prefer to use the Apache server, you'll have to navigate to http://localhost/symfony-product-api/public/index.php/api)

<br>

## Load fixtures into your project database
To load the fixtures into your project database, run the following command:
```
php bin/console hautelook:fixtures:load
```

Use phpMyAdmin (http://localhost/phpmyadmin) to see if the database was created and fixtures were loaded correctly, or connect to the database in your terminal

<br>

## TESTS
For the tests, create a .env.test file at the root of your project and copy your database URL into it.

Then, create a test database:
```
php bin/console --env=test doctrine:database:create

php bin/console --env=test doctrine:schema:create
```
To load fixtures into the test database, use the following workaround:

- In the .env file, add the suffix _test to the specified database name in the DATABASE_URL
- Load the test fixtures into the test database:
```
php bin/console doctrine:fixtures:load
```
- Change the DATABASE_URL in the .env back to the original project database name by removing the _test suffix

- Run the tests:
```
php bin/phpunit
```