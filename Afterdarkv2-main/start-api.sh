#!/bin/bash
set -e

cd /home/runner/workspace/Afterdarkv2-main

php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan serve --host=0.0.0.0 --port=8000
