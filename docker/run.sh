docker run -d -it -p 80:80 -p 3306:3306 -p 15672:15672 --name alyubimov_testwork --mount type=bind,source=$(pwd),target=/var/www/testwork alyubimov_testwork
