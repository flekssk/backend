.PHONY: artisan

artisan:
	docker compose -f docker-compose.yml exec php php artisan $(filter-out $@,$(MAKECMDGOALS))

artisan-socket:
	docker compose -f docker-compose.yml exec websocket php artisan $(filter-out $@,$(MAKECMDGOALS))

bash:
	docker compose -f docker-compose.yml exec bash

build:
	docker compose up -d --build --force-recrete

error:
	tail -f -n 2000000 storage/logs/laravel.log | grep "production.ERROR"

%:
	@:
