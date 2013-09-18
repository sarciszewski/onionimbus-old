#!/bin/bash
# September 15, 2013 - Deploy the onionimbus_site configuration to a new server
if [[ $EUID -ne 0 ]]; then
  echo "This script must be run as root" 1>&2
  exit 1
fi
# Move configuration
mv ./conf/php.conf /etc/nginx
mv ./conf/sslciphers.conf /etc/nginx
mv ./conf/onionimbus.conf /etc/nginx/sites-enabled

# Deploy
mkdir /var/onionimbus
mv ./outside_webroot/db /var/onionimbus/db
mv ./outside_webroot/includes /var/onionimbus/includes
mkdir /var/onionimbus/public_html
mv ./public_html/* /var/onionimbus/public_html
mkdir /var/onionimbus/db

chown -R www-data:www-data /var/onionimbus
service nginx restart