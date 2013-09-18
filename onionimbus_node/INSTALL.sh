#!/bin/bash
# September 15, 2013 - Deploy the onionimbus_node configuration to a new server
if [[ $EUID -ne 0 ]]; then
  echo "This script must be run as root" 1>&2
  exit 1
fi
if [ -d /etc/nginx ] 
then
  # nginx is installed -- hooray!
  if [ -d /etc/tor ] 
  then
    # tor is installed -- hooray!
    # Create the directory for all of our configurations
    mkdir /etc/onionimbus
    
    # Run this script every minute
    crontab -l > mycron
    echo "* * * * * /etc/onionimbus/everyminute.sh" >> mycron
    crontab mycron
    rm mycron
    
    # Deply configuration files
    mv ./conf/nginx.conf /etc/onionimbus/nginx.conf
    ln -s /etc/onionimbus/nginx.conf /etc/nginx/sites-enabled/onionimbus
    # Move shell scripts
    mv ./outside_webroot/everyminute.sh /etc/onionimbus/everyminute.sh
  else
    echo "Please install Tor before you continue!" 1>&2
    exit 1  
  fi
else 
  # nginx not installed
  echo "Please install nginx before you continue!" 1>&2
  echo "sudo apt-get update && sudo apt-get install nginx" 1>&2
  exit 1  
fi