#!/bin/bash
# September 15, 2013 - Deploy the onionimbus_site configuration to a new server
if [[ $EUID -ne 0 ]]; then
  echo "This script must be run as root" 1>&2
  exit 1
fi
if [ -d /etc/nginx ] 
then
  # nginx is installed on this server. Hooray! :D
  apt-get install -y php5-dev php-pear
  pecl install scrypt
  # TEST=`rgrep "scrypt" /etc/php5/*/php.ini`
  # Todo: add a check that makes sure extension=scrypt.so is added to php.ini
  # For now, make it manual
  echo "extension=scrypt.so" >> /etc/php5/fpm/php.ini

  # Move configuration files
  mv ./conf/php.conf /etc/nginx/php.conf
  mv ./conf/sslciphers.conf /etc/nginx/sslciphers.conf
  mv ./conf/onionimbus.conf /etc/nginx/sites-enabled/onionimbus.conf
  
  # Deploy
  mkdir /var/onionimbus
  cp ./outside_webroot/db/* /var/onionimbus/db
  cp ./outside_webroot/includes/* /var/onionimbus/includes
  mkdir /var/onionimbus/public_html
  cp ./public_html/* /var/onionimbus/public_html
  mkdir /var/onionimbus/db

  chown -R www-data:www-data /var/onionimbus
  service nginx restart
else 
  # nginx not installed
  echo "Please install nginx before you continue!" 1>&2
  echo "sudo apt-get update && sudo apt-get install nginx" 1>&2
  exit 1
fi