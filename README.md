[![DOI](https://zenodo.org/badge/289528634.svg)](https://zenodo.org/badge/latestdoi/289528634)

# BioSounds

## Description

BioSounds is a web application for ecoacoustics that archives and organises soundscape collections, hosts reference recording collections, generates navigable sound spectrograms, and allows annotation of animal vocalisations or other sounds.

## Credits and license

Developed by [Noemi Perez](https://github.com/nperezg) and [Kevin Darras](https://github.com/kdarras), from the University of Goettingen.

Biosounds is licensed under the [GNU General Public License, v3](https://www.gnu.org/licenses/gpl-3.0.en.html).

The corresponding citable, updatable scientific publication will be available soon in [F1000Research](https://f1000research.com/).

### Technical notes

Originally based on the archived [Pumilio](https://github.com/ljvillanueva/pumilio) project, it has evolved into a new, refactored project with new functionalities. 

Biosounds still uses the original Python spectrogram generation code (copyright by Luis J. Villanueva), but we intend to fully use Web Audio API in the future if it can fulfill all BioSounds needs. 

We will tackle the implementation of automated detection of sounds next.

## Quick start (for developers)

BioSounds uses [Docker](https://www.docker.com) which provides a solution based on containers where the app can run easily in your computer. That avoids having to manually install all libraries, database and other components necessary for running BioSounds. 

For facilitating the process, we have added a couple of files: install.sh and run.sh with all necessary commands. There is also a Makefile with some extra helpful commands to access the docker containers.

You need to install [docker](https://docs.docker.com/engine/install) and [docker-compose](https://docs.docker.com/compose/install) directly in your machine. Please read the documentation and follow the instructions carefully. We don't offer support for docker installation and configuration.

Important: this setup is intended for developing and testing purposes **ONLY**. It is in no way ready for production. Please read the _Server Installation_ section.

### Installation

```sh install.sh```

### Run

```sh run.sh```

### Using BioSounds

Open http://localhost:8080

Log in with username: admin, password: Administrator20

Important: please **change the password** of this administrator user or **delete** it once you have BioSounds running on production and have your own admin users.

### Stop

```docker-compose stop```

## Server installation

### With Docker

If you want to use Docker for your own server installation, please consult with a devOps engineer or someone with the necessary knowledge to manage it properly, depending on your hosting setup. 

The current Docker configuration [Dockerfile](src/Dockerfile) can be used for your preferred setup.

### Without Docker

Like any other web app, BioSounds can be installed without Docker. Please read the dedicated documentation [here](docs/installation.md).

### Configuration file

For both cases (with and without Docker), you'll need to set the configuration values in the [config.ini](src/config/config.ini) file, according to your server setup. 

## Live version (SoundEfforts Project - University of Goettingen)

A working instance of Biosounds can be accessed [here](https://soundefforts.uni-goettingen.de/biosounds) with limited functionality within the open collections.

## User guide

We also wrote a [guide](docs/user_guide.md) for end users explaining the basic functionality.
