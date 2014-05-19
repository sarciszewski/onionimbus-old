<?php
// Use argv, argc to determine if we're creating a sqlite database or a MySQL db
/*
 * CURRENT STATUS: Rethinking the approach here :(
 */

function usage() {
  echo <<<EOUSAGE
USAGE (one of the following formats):
  php create_database.php sqlite [filename]
  php create_database.php mysql username password [databasename] [localhost]
  php create_database.php pgsql username password [databasename] [localhost]
EOUSAGE;
  die("\n");
}
if($argc < 2) {
  usage();
}
// First, let's read the CLI options passed
$me = array_shift($argv); // Useless
$dbtype = array_shift($argv);
switch($dbtype) {
  case 'sqlite':
      $dbfile = array_shift($argv);
      if(!is_writeable($dbfile)) {
        die("{$dbfile} is not writeable! Please chmod or chown it.\n");
      }
      try {
        $DB = new PDO("sqlite:{$dbfile}");
      } catch (Exception $ex) {
        die("Could not create Sqlite database!\n");
      }
    break;
  case 'mysql':
    $dbuser = $argc > 2 ? array_unshift() : die("You did not supply a username.\n");
    $dbpass = $argc > 3 ? array_unshift() : '';
    $dbname = $argc > 4 ? array_unshift() : 'onionimbus';
    $dbhost = $argc > 5 ? array_unshift() : 'localhost';
    try {
      $DB = new PDO("mysql:host={$dbhost};db={$dbname}", $dbuser, $dbpass);
    } catch (Exception $ex) {
      die("Could not connect to MySQL database!\n");
    }
    break;
  case 'pgsql':
    $dbuser = $argc > 2 ? array_unshift() : die("You did not supply a username.\n");
    $dbpass = $argc > 3 ? array_unshift() : '';
    $dbname = $argc > 4 ? array_unshift() : 'onionimbus';
    $dbhost = $argc > 5 ? array_unshift() : 'localhost';
    try {
      $DB = new PDO("pgsql:host={$dbhost};dbname={$dbname}", $dbuser, $dbpass);
    } catch (Exception $ex) {
      die("Could not connect to PostgreSQL database!\n");
    }
    break;
  default:
    usage();
    break;
}
// Now, let's do the stuff.
try {
  $query = file_get_contents('install_'.$dbtype.'.sql');
  $PDO->exec($query);
} catch(Exception $ex) {
  var_dump($ex);
  die("An unknown error has occurred.\n");
}
?>