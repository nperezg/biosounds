# BioSounds

## Description

BioSounds is a web application for ecoacoustics that archives and organises soundscape collections, hosts reference recording collections, generates navigable sound spectrograms, and allows annotation of animal vocalisations or other sounds.

## Credits and re-use

Developed with [Kevin Darras](https://kevindarras.weebly.com/), from the University of Goettingen.
Biosounds is licensed under the [GNU General Public License, v3](https://www.gnu.org/licenses/gpl-3.0.en.html).
The corresponding citable, updateable scientific publication will be available soon in [F1000Research](https://f1000research.com/).

## Quick start (for developers)

BioSounds uses [Docker](https://www.docker.com) which provides a solution based on containers where the app can run easily in your computer. That avoids having to manually install all libraries, database and other components necessary for BioSounds. For facilitating the process, we have added a Makefile with the necessary commands.

You need to [install Docker](https://docs.docker.com/engine/install) and [docker-compose](https://docs.docker.com/compose/install) directly in your machine.

### Installation

```make init```

### Run

```make run```

### Stop

```make stop```

### Technical notes

Originally based on the archived [Pumilio](https://github.com/ljvillanueva/pumilio), it has evolved into a new, refactored project with new functionalities. Biosounds still uses the original Python spectrogram generation code, but we intend to fully use Web Audio API in the future. We will tackle the implementation of automated detection of sounds next.

## Quick start (for end users)

A working instance of Biosounds can be accessed [here](https://soundefforts.uni-goettingen.de/biosounds/) with limited functionality within the open collections.
We also wrote a [guide](docs/guide.md) for end users explaining the basic functionality.
