up:
	docker-compose up -d
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan migrate
	docker-compose exec app php artisan db:seed

down:
	docker-compose down

migrate:
	docker-compose exec app php artisan migrate

seed:
	docker-compose exec app php artisan db:seed

test:
	docker-compose exec app php artisan test