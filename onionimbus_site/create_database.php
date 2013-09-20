<?
// Use argv, argc to determine if we're creating a sqlite database or a MySQL db
/*
 * CURRENT STATUS: Rethinking the approach here :(
 */

function usage() {
  echo <<<EOUSAGE
USAGE (one of the following formats):
  php create_database.php sqlite [filename]
  php create_database.php mysql username [databasename] [localhost]
EOUSAGE;
  die("\n");
}
if($argc < 2) {
  usage();
}
$dbtype = array_shift($argv);
switch($dbtype) {
  case 'sqlite':
      $dbfile = array_shift($argv);
      if(!is_writeable($dbfile)) {
        die("{$dbfile} is not writeable! Please chmod or chown it.\n");
      }
      $DB = new PDO("sqlite:{$dbfile}");
    break;
  case 'mysql':
    break;
  default:
    usage();
    break;
}
?>