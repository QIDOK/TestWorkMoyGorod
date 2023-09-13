#!/bin/bash
mysql -uroot -proot < /var/www/build.sql
cd /var/www/testwork && composer install
cp -rf /var/www/testwork/docker/dev/config /var/www/testwork/common
chown -R 1000:1000 /var/www/testwork
chmod -R 0777 /var/www/testwork

