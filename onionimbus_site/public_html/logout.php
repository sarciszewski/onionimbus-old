<?
require_once "/var/onionimbus/includes/global.php";
// Delete every piece of data in $_SESSION
function blankArray($arr, $depth) {
  foreach($arr as $i => $v) {
    if(is_array($v)) {
     blankArray($arr[$i], $depth + 1);
    } else {
      unset($arr[$i]);
    }
  }
  if($depth > 0) unset($arr);
}
blankArray($_SESSION);
// Now destroy the session
session_destroy();
// When we land on the new page, we should have a fresh session ID
header("Location: /");
exit;