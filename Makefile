.PHONY: local prod down-local down-prod logs-local logs-prod artisan artisan-shell artisan-queue

# Поднимаем локальное окружение (прод + локальные дополнительные сервисы)
local:
	docker-compose -f docker-compose.prod.yml -f docker-compose.local.yml up -d --build --force-recreate

# Поднимаем продакшн окружение (без базы данных)
prod:
	docker-compose -f docker-compose.prod.yml up -d

# Останавливаем локальное окружение
down-local:
	docker-compose -f docker-compose.prod.yml -f docker-compose.local.yml down

# Останавливаем продакшн окружение
down-prod:
	docker-compose -f docker-compose.prod.yml down

# Просмотр логов локального окружения
logs-local:
	docker-compose -f docker-compose.prod.yml -f docker-compose.local.yml logs -f

# Просмотр логов продакшн окружения
logs-prod:
	docker-compose -f docker-compose.prod.yml logs -f

# Выполнение команды artisan в контейнере API
# Для использования вызовите: make artisan ARGS="migrate --seed"
artisan:
	docker-compose -f docker-compose.prod.yml exec app-api php artisan $(ARGS)

# Запуск интерактивного режима artisan (например, Tinker) в контейнере API
artisan-shell:
	docker-compose -f docker-compose.prod.yml exec app-api php artisan tinker

# Запуск очереди в отдельном контейнере (daemon)
artisan-queue:
	docker-compose exec daemon php artisan queue:work

enter: #> entering into current contaner instances
##? make enter ...  => docker compose exec -it ... bash
	docker-compose -f docker-compose.prod.yml exec -it $(wordlist 2,2,$(MAKECMDGOALS)) sh $(wordlist 3,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
#>