<?
if(isset($_SERVER['REMOTE_ADDR'])) {  die("This script can only be run from a command line context."); }
/* USAGE:
 * php /path/to/make_nginx_config.php > /etc/nginx/sites-enabled/yourfilename.conf
 * 
 * Best to run it in a cron script :)
 */