#!/bin/bash
# Run this script every minute to ensure nginx is updated and rebuilt quickly
# in response to a valid "rebuild" request
#
# This allows nginx config to be as dynamic as Apache without sacrificing its
# performance (except possible DoS vectors?)
#
# Written by Scott Arciszewski on September 15, 2013 for Onionimbus
# See attached license (WTFPL) for usage restrictions [rather, the lack thereof]

################################################################################
#               CONFIGURATION SETTINGS (YOU CAN CHANGE THESE!)                 #
################################################################################
EVERYSECOND=1 # Do we need to rebuild and reload nginx config every second?

################################################################################
#      DO NOT CHANGE THE STUFF BELOW UNLESS YOU KNOW WHAT YOU ARE DOING        #
################################################################################
# Make sure the run directory exists and is writeable
if [ -e /var/run/onionimbus ]
  then
    if [ -d /var/run/onionimbus ]
      then
      # This is where we want to be
    else
      # It isn't a directory? BLASPHEMY!
      rm -rf /var/run/onionimbus
      mkdir /var/run/onionimbus
      chown www-data:www-data /var/run/onionimbus
      chmod 0770 /var/run/onionimbus
    fi
else
  # Make directory, allow www-data to write to it
  mkdir /var/run/onionimbus
  chown www-data:www-data /var/run/onionimbus
  chmod 0770 /var/run/onionimbus
fi
if [ EVERYSECOND == 1 ]
  then
  # Run this every second. Disable this feature on low-end VPS or during DoS
    for i in {0..59}
      do       
        if [ -e /var/run/onionimbus/rebuild ]
          then
            php /etc/onionimbus/make_nginx_config.php > /etc/onionimbus/nginx.conf
            service nginx reload
            rm -f /var/run/onionimbus/rebuild
        fi
        sleep 1
    done
else
  # We only check once per minute. More reasonable for enduring high load
    if [ -e /var/run/onionimbus/rebuild ]
      then
        php /etc/onionimbus/make_nginx_config.php > /etc/onionimbus/nginx.conf
        service nginx reload
        rm -f /var/run/onionimbus/rebuild
    fi
fi