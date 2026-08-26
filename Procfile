web: php artisan migrate --force && php artisan storage:link && php artisan inertia:start-ssr & php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
worker: php artisan queue:work --sleep=3 --tries=3 --max-time=3600
