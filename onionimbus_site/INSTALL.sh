#!/bin/bash
# September 15, 2013 - Deploy the onionimbus_site configuration to a new server
if [[ $EUID -ne 0 ]]; then
  echo "This script must be run as root" 1>&2
  exit 1
fi
# Move configuration
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