DC = docker-compose
EXEC = exec apache
EXEC_USER = exec -T -u www-data apache

#Initializes BioSounds (only first install)
init: start composer-install create-database chown wait run-worker

#Runs BioSounds
run: start chown wait run-worker

#Starts all containers
start:
	${DC} up -d

#Stop all containers
stop:
	${DC} stop

#Restart all containers
restart:
	${DC} restart

#Builds all containers
build:
	${DC} build

#Shows the container logs
logs:
	${DC} logs -f

#Waits for the queue service to be ready
wait:
	${DC} ${EXEC} bash -c 'dpkg -s netcat > /dev/null 2>&1 || apt-get update && apt-get install -y -qq -o=Dpkg::Use-Pty=0 netcat; \
	while ! (nc -z queue 5672); do \
		echo "Waiting for services.."; \
		sleep 1; \
	done;'

composer-install:
	${DC} ${EXEC} composer install

#Creates a ssh connection to the apache container
shell-app:
	${DC} ${EXEC} /bin/bash

#Creates a ssh connection to the database container
shell-db:
	${DC} exec database /bin/bash

#Creates a ssh connection to the queue container
shell-queue:
	${DC} exec queue /bin/bash

#Initializes database
create-database:
	${DC} up -d database
	docker exec -i $$(docker-compose ps -q database) mysql -ubiosounds -pbiosounds biosounds < init.sql
	docker exec -i $$(docker-compose ps -q database) mysql -ubiosounds -pbiosounds biosounds < data.sql

#Sets files permissions
chown:
	${DC} ${EXEC} chown -R www-data:www-data cache tmp sounds bin

#Runs queue worker
run-worker:
	${DC} ${EXEC_USER} nohup php worker.php > files_update.log 2>&1 &
