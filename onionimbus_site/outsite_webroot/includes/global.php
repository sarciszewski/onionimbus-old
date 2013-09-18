<?
if(!defined('GLOBAL_LOADED')) {
$SETTINGS =  ini_get('/var/onionimbus/settings.ini');
define('SETTINGS_KEEP', serialize($SETTINGS));
/* 
 * Just in case we get paranoid in the future, we can do this:
 * $SETTINGS = unserialize(SETTINGS_KEEP);
 */
// If you have time, update your php.ini file and comment these lines out
// You'll get better performance
ini_set('session.cookie_httponly', true);
ini_set('session.cookie_secure', true); # Comment this out if not using HTTPS
ini_set('session.entropy_file', '/dev/urandom');
ini_set('session.cache_expire', '43200');
ini_set('session.cookie_lifetime', '43200');
ini_set('session.entropy_length', '32'); # 256 bits
ini_set('session.hash_function', 'sha256'); # 256 bits
ini_set('session.hash_bits_per_character', '6'); # 43 characters
if(!is_dir(ini_get('session.save_path'))) {
  // Prevent session errors during server reboot
  @mkdir(ini_get('session.save_path'));
  @chmod(ini_get('session.save_path'), 0777);
}
if(!session_id()) session_start();
if(empty($_SESSION['birth'])) {
  if(!empty($_SESSION)) {
    foreach($_SESSION as $i => $v) {
      $_SESSION[$i] = openssl_random_pseudo_bytes(strlen($v));
      unset($_SESSION[$i]);
    }
  }
  $_SESSION['birth'] = time();
  session_regenerate_id(true);
} elseif(time() - $_SESSION['birth'] > 1800) {
  // After 30 minutes, update session ID
  $_SESSION['birthTime'] = time();
  session_regenerate_id(false);
  $_SESSION['birthTime'] = time()+1;
}
// Allows $HMAC['specificForm'] to store HMAC keys in $_SESSION. Laziness.
if(empty($_SESSION['hmac'])) $_SESSION['hmac'] = array();
$HMAC =& $_SESSION['hmac'];
//error_reporting(E_ALL); ini_set('display_errors', 'On');
include_once "HTMLPurifier.auto.php";
$XSS = new HTMLPurifier();

define('MAX_REC_DEPTH', 50); // Used in functions
$jqueryver = '1.10.2'; // Load this version of jQuery everywhere!

require_once $SETTINGS['includes'].'libscott.php';
require_once $SETTINGS['includes'].'pbkdf2.php';
# WE CAN USE SQLITE, MYSQL, ETC.
$DB = new PDO('sqlite:/var/onionimbus/db/main.db');
//$DB = new PDO('mysql:dbname=onionimbus;host=127.0.0.1', 'username', 'password');
  define('GLOBAL_LOADED', true);
}