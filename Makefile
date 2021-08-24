test:
	${CURDIR}/vendor/bin/phpunit

dev:
	composer install
	npm install & npm run dev

ide:
	php artisan ide-helper:generate
	php artisan ide-helper:meta
	
