FROM ubuntu:latest
RUN apt update
RUN apt update && apt install -y tzdata
RUN apt install software-properties-common -y
RUN LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php
RUN apt update
RUN apt install curl -y
RUN apt install apt-transport-https -y
RUN apt install apache2 -y
RUN apt install php8.2 -y
RUN apt install mysql-server -y
RUN apt install libcurl4 -y
RUN apt install git -y
RUN apt install php8.2-bcmath php8.2-mysql php8.2-curl php8.2-gd php8.2-mbstring php8.2-simplexml php8.2-zip -y
RUN a2enmod rewrite
RUN phpenmod mbstring
RUN phpenmod curl
RUN apt-cache search php8.2-*
RUN cd /var/www/ && curl -sS https://getcomposer.org/installer -o composer-setup.php && php composer-setup.php --install-dir=/usr/local/bin --filename=composer && rm composer-setup.php

RUN apt install apache2-utils -y
RUN apt install unzip

ENV MYSQL_ROOT_PASSWORD=root

COPY docker/launch.sh /var/www/launch.sh
COPY docker/configure.sh /var/www/configure.sh
COPY docker/build.sql /var/www/build.sql
COPY docker/web.conf /etc/apache2/sites-available/000-default.conf
COPY docker/mysql.cnf /etc/mysql/mysql.cnf
