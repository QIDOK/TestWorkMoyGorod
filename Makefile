build:
	docker build -t alyubimov_testwork .
	bash ./docker/run.sh
	docker exec alyubimov_testwork bash /var/www/launch.sh
	docker exec alyubimov_testwork bash /var/www/configure.sh
	make migrate

exec:
	docker exec -it alyubimov_testwork /bin/bash

drop:
	docker stop alyubimov_testwork
	docker rm alyubimov_testwork

drop-all:
	make drop
	docker rmi alyubimov_testwork

rebuild:
	make drop
	make build

start:
	docker start alyubimov_testwork
	docker exec alyubimov_testwork bash /var/www/launch.sh

stop:
	docker stop alyubimov_testwork

restart:
	make stop
	make start

migrate:
	docker exec -it alyubimov_testwork php /var/www/testwork/yii migrate

generate-models:
	docker exec -it alyubimov_testwork php /var/www/testwork/yii migrate generate-models

fix-rights:
	docker exec alyubimov_testwork chown -R 1000:1000 /var/www/testwork
	docker exec alyubimov_testwork chmod -R 0777 /var/www/testwork

services-start:
	docker exec alyubimov_testwork php /var/www/testwork/yii services/start

services-stop:
	docker exec alyubimov_testwork php /var/www/testwork/yii services/stop

services-restart:
	docker exec alyubimov_testwork php /var/www/testwork/yii services/stop
	docker exec alyubimov_testwork php /var/www/testwork/yii services/start

