DC = docker-compose

# Shows the containers logs
logs:
	${DC} logs -f

# Creates a connection to the apache container
shell-app:
	${DC} exec apache /bin/bash

#Creates a connection to the database container
shell-db:
	${DC} exec database /bin/bash

#Creates a connection to the queue container
shell-queue:
	${DC} exec queue /bin/bash
