@echo off

REM Run Laravel migration
php artisan migrate

REM Clear cache
php artisan cache:clear

REM Refresh database
php artisan migrate:refresh

REM Install passport client
php artisan passport:install --uuids

REM publish passport configuration
php artisan vendor:publish --tag=passport-config


