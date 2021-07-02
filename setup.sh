#!/bin/sh

echo 'Make sure that you have created the database todo_api in your dbms'
composer install
mv .env.example .env
read -p "Do you want to configure the .env file ? (You should) y/N " rep
if [ "$rep" == "y" ] || [ "$rep" == "Y" ]
then
    vi .env
fi
php artisan key:generate
php artisan migrate
php artisan serve
