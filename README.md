[![DOI](https://zenodo.org/badge/289528634.svg)](https://zenodo.org/badge/latestdoi/289528634)

# BioSounds

## Description

BioSounds is a web application for ecoacoustics that archives and organises soundscape collections, hosts reference recording collections, generates navigable sound spectrograms, and allows annotation of animal vocalisations or other sounds.

## Team and collaborations

### Team structure and organisation

#### Core Team
Kevin F.A. Darras (idea owner) and Noemi Peréz (technical owner).
The core team members are official authors responsible for the tool and corresponding authors for future papers. They decide on what functionalities to implement, which collaborators to add, give recommendations on how implement tasks and take care of planning.
The technical owner is responsible for approving/rejecting new code before being added to the repository and planning the technical design. The idea owner is responsible for deciding on the new functionalities (user-related) and planning them accordingly.

#### Engineering Team
Laura X.
The engineering team collaborates permanently with the core team, developing main functionalities, maintaining the code, reviewing code from external, temporary collaborators. With the supervision of the core team, they can decide on technical topics and code design. The members can be co-authors on the scientific article.

#### Scientific Team
Kevin F.A. Darras.
The scientific team collaborates permanently with the core team, either financially, looking for resources and/or actively discussing new ideas for implementation. The members are co-authors on the scientific article.

#### External Collaborators
IT professionals, students, scientists and other persons who at a certain point and during a limited amount of time contribute to the project, both paid or voluntarily. They can be included on the scientific article (depending on each individual contribution). These collaborators' work is closely supervised by the engineering team.

### Potential collaborations

We have compiled a [list of BioSounds tasks](docs/tasks.md) to implement in the near future. Completion of any of the listed tasks grants co-authorship on the corresponding upcoming version of the scientific article. If you are interested in joining, send your information (qualification and motivation) to discuss its implementation to kdarras at gwdg dot de.

## Using BioSounds

## Live version (University of Goettingen)

A working instance of Biosounds can be accessed [here](https://soundefforts.uni-goettingen.de/biosounds) with limited functionality within the open collections.

## User guide

We wrote a [guide](docs/user_guide.md) for end users explaining the basic functionality.

## Quick start (for developers)

BioSounds uses [Docker](https://www.docker.com) which provides a solution based on containers where the app can run easily in your computer. That avoids having to manually install all libraries, database and other components necessary for running BioSounds. 

For facilitating the process, we have added a couple of files: install.sh and run.sh with all necessary commands. There is also a Makefile with some extra helpful commands to access the docker containers.

You need to install [docker](https://docs.docker.com/engine/install) and [docker-compose](https://docs.docker.com/compose/install) directly in your machine. Please read the documentation and follow the instructions carefully. We don't offer support for docker installation and configuration.

Important: this setup is intended for developing and testing purposes **ONLY**. It is in no way ready for production. Please read the _Server Installation_ section.

### Installation

```sh install.sh```

### Run

```sh run.sh```

### Stop

```docker-compose stop```

### Opening BioSounds

Open http://localhost:8080

Log in with username: admin, password: Administrator20

Important: please **change the password** of this administrator user or **delete** it once you have BioSounds running on production and have your own admin users.

## Server installation

### With Docker

If you want to use Docker for your own server installation, please consult with a devOps engineer or someone with the necessary knowledge to manage it properly, depending on your hosting setup. 

The current Docker configuration [Dockerfile](src/Dockerfile) can be used for your preferred setup.

### Without Docker

Like any other web app, BioSounds can be installed without Docker. Please read the dedicated documentation [here](docs/installation.md).

### Configuration file

For both cases (with and without Docker), you'll need to set the configuration values in the [config.ini](src/config/config.ini) file, according to your server setup.

## About BioSounds

### Credits and license

Developed by [Noemi Perez](https://github.com/nperezg) and [Kevin Darras](https://github.com/kdarras), from the University of Goettingen.

Biosounds is licensed under the [GNU General Public License, v3](https://www.gnu.org/licenses/gpl-3.0.en.html).

The corresponding citable, updatable scientific publication will be available soon in [F1000Research](https://f1000research.com/).

### History

Originally based on the archived [Pumilio](https://github.com/ljvillanueva/pumilio) project, it has evolved into a new, refactored project with new functionalities. 

BioSounds still uses the original Python spectrogram generation code (copyright by Luis J. Villanueva), but we intend to fully use Web Audio API in the future if it can fulfill all BioSounds needs.
