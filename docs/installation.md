# Installation

BioSounds can be installed in your server without using Docker. In this guide we will describe all necessary installation steps for having a working BioSounds instance.

## Requirements

### Server

We assume you have a Linux server with PHP 7 installed. For our Docker configuration we have used **PHP 7.4**, which is the latest version.

### PHP Extensions

* PDO MySql
* Bcmath
* Sockets

### Libraries

Several libraries need to be installed in your server for BioSounds to run properly. For all libraries the latest version should be installed, except where indicated otherwise.

* Python, [Numpy](https://numpy.org), Python Setuptools, PIP
* libsndfile1-dev, libasound2-dev 
* [ImageMagick](https://imagemagick.org/)
* [Montage](https://imagemagick.org/script/montage.php)
* [Sox](http://sox.sourceforge.net)
* [Lame](https://lame.sourceforge.io)
* [Audiolab](https://pypi.org/project/scikits.audiolab) (scikits.audiolab) **version 0.8**
* [Pillow](https://pillow.readthedocs.io)

### Web Server

The recommended web server for BioSounds is Apache. You could use another web server like Nginx, but then you would need to manually configure certain rules manually. You can take a look at the [.htaccess](../src/.htaccess) file for more information in this regard.

The file [000-default.conf](../src/apache/000-default.conf) has the basic necessary configuration for a virtual host. You can use it for your Apache web server.

Once you have Apache installed, please set the app main URL value in the [config.ini](../src/config/config.ini) file under the _App URL_ section. You will need to set the _ABSOLUTE_DIR_ value under the _Directories_ section if it differs from the one in your server installation.

### Database

BioSounds uses the MySQL database. MySQL is a really popular database, easy to use and well-known in the scientific community.

Once MySQL is installed in your server, you can proceed with the database setup.

#### Database setup

* Create a database called _biosounds_.
* Create a user that has read/write permissions for that database.
* Import the database structure using the file [init.sql](../init.sql).
* Import the database basic data using the file [data.sql](../data.sql).
* Set the host and user credentials values in the [config.ini](../src/config/config.ini) file under the _database_ section.

### RabbitMQ

BioSounds uses [RabbitMQ](https://www.rabbitmq.com/) as queue for the sound files upload. That needs to be installed in your server as well.

Once you have it configured, please create a new queue and user credentials in RabbitMQ and set the values in the [config.ini](../src/config/config.ini) file under the _queue_ section.

### Composer

You need to install [Composer](https://getcomposer.org) in order to be able to download all necessary app level libraries.

After installing Composer, please go to ```biosounds/src``` and run:

```composer install```

That will download all app libraries in the ```biosounds/src/vendor``` folder.

### Copy files

Once you have the server configured, you can copy the following files and folders from the root ```biosounds/src``` to the corresponding app root location referred by your web server for BioSounds.

* assets
* bin
* cache
* config
* scripts
* sounds
* src
* templates
* tmp
* vendor
* .htaccess
* index.php
* worker.php

### Access BioSounds

Go to the URL that points to the BioSounds folder in your web server. You can log in with the same admin user specified in the [README](../README.md) file.




