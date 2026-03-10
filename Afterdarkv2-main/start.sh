#!/bin/bash
set -e

cd /home/runner/workspace/Afterdarkv2-main

# Clear and regenerate caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run vite in background for HMR
npm run dev &
VITE_PID=$!

# Wait for vite to start
sleep 3

# Start Laravel on port 5000
php artisan serve --host=0.0.0.0 --port=5000
