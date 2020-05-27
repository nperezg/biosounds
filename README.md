# BioSounds

## Description

BioSounds is a sound analysis web application for biological scientific research that generates sound spectrograms and allows animal tagging. Originally based on Pumilio, it has evolved into a new project. The code is being completely refactored and lots of new functionalities have been added. It still uses the Python spectrogram generation code from the original application, but this is going to be removed soon, as we intend to fully use the Web Audio API. Developed together with [Kevin Darras](src/index.php), from the University of Goettingen.

## Quick start

BioSounds uses [Docker](https://www.docker.com) which provides a solution based on containers where the app can run easily in your machine. That avoids having to manually install all libraries, database and other components necessary for BioSounds. For facilitating the process, we have added a Makefile with the necessary commands.

You need to install Docker and docker-compose directly in your machine. Follow the instructions depending on your operating system.

Install BioSounds (only the first time)
```make init```

Run BioSounds (every other time)
```make run```

Stop BioSounds
```make stop```

## Comments

The application is still under development and within the refactoring process, so don't be surprised if certain parts are not yet clean code. We are working on it ;)
