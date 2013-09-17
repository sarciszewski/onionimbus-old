<?
if(!defined('LIBSCOTT_LOADED')) {
################################################################################ 
# SECURITY LIBRARY                                           Scott Arciszewski #
#      Contains classes and functions to create secure web applications        #
#                             (and to type less)                               #
################################################################################
#&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&#
################################################################################
# SQL INJECTION PREVENTION:                                                    #
#+----------------------------------------------------------------------------+#
function noinject($string, $mode = 'mysqli') {
  // Input should never be an array. If one is passed by accident, then it was
  // a result of malicious user input. Return a blank, empty string. :)
  if(is_array($string)) return '';
  if(get_magic_quotes_gpc()) $string = stripslashes($string);
  switch($mode) {
    case 'sqlite':
      return SQLite3::escapeString($string);
      break;
    case 'mysqli':
      return mysqli_real_escape_string($GLOBALS['MySQL'], $string);
      break;
    default:
      return mysql_real_escape_string($string);
      break;
  }
}
#+----------------------------------------------------------------------------+#
function noinject_array($array, $depth = 0) {
  // Special noinject() wrapper for arrays!
  if($depth > MAX_REC_DEPTH) {
    trigger_error("Recursion depth {$depth} greater than maximum allowed (".MAX_REC_DEPTH.")", E_USER_ERROR);
    return ''; // Recursion depth too high
  }
  foreach($array as $i => $v) {
    if(is_array($v)) {
      $array[$i] = noinject_array($v, $depth + 1);
    } else {
      $array[$i] = noinject($v);
    }
  }
  return $array;
}
#+----------------------------------------------------------------------------+#
# END SQL INJECTION PREVENTION                                                 #
################################################################################
# LOCAL/REMOTE FILE INCLUSION PREVENTION:                                      #
#+----------------------------------------------------------------------------+#
function safeInclude($file, $path = "./") {
  include "{$path}".str_replace('/', '', forceASCII($file));
}
#+----------------------------------------------------------------------------+#
# END LOCAL/REMOTE FILE INCLUSION PREVENTION                                   #
################################################################################
# CROSS-SITE-SCRIPTING PREVENTION:                                             #
#+----------------------------------------------------------------------------+#
function stripXSS($input, $inForm = false) {
  // Input should never be an array. If one is passed by accident, then it was
  // a result of malicious user input. Return a blank, empty string. :)
  if(is_array($input)) return '';
  if(empty($GLOBALS['XSS'])) { // Fault tolerance
    include_once "HTMLPurifier.auto.php";
    $GLOBALS['XSS'] = new HTMLPurifier();
  }
  if($inForm) {
    return htmlspecialchars($GLOBALS['XSS']->purify($input), ENT_QUOTES | ENT_HTML5, 'UTF-8');
  }
  return $GLOBALS['XSS']->purify($input);
}
#+----------------------------------------------------------------------------+#
function stripXSS_array($array, $blockHTML = false, $depth = 0) {
  // Special stripXSS() wrapper for arrays!
  if(!is_array($array)) return stripXSS($array);
  if($depth > MAX_REC_DEPTH) {
    trigger_error("Recursion depth {$depth} greater than maximum allowed (".MAX_REC_DEPTH.")", E_USER_ERROR);
    return ''; // Recursion depth too high
  }
  foreach($array as $i => $v) {
    if(is_array($v)) {
      $array[$i] = stripXSS_array($v, $blockHTML, $depth + 1);
    } else {
      $array[$i] = stripXSS($v, $blockHTML);
    }
  }
  return $array;
}
#+----------------------------------------------------------------------------+#
# END CROSS-SITE-SCRIPTING PREVENTION                                          #
################################################################################
# CHARACTER ENCODING AND RELATED:                                              #
#+----------------------------------------------------------------------------+#
function binToFullkey($raw) {
  return convBase(bin2hex($raw), '0123456789abcdef',
        '!"#$%^\'()+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~');
  // All printable ASCII characters
}
#+----------------------------------------------------------------------------+#
function makeSnippet($in, $i=144, $mw=40) {
  // Clean snippet generator. Doesn't cut off mid-word
  if(empty($in)) return '';
  $i = intval($i)-1;
  $c = strlen($in);
  do {
    $pre = $in;
    if(preg_match("/\b([^\s\-\&\;]{{$mw},})\b/", $in, $m)) {
      $in = str_replace($m[1], chunk_split($m[1], $mw-1, "  "), $in);
    }
  } while($pre != $in);
  while($i < $c) {
    if(!preg_match('/[A-Za-z0-9\']/', $in[$i])) break;
    $i++;
   }
   if($i >= $c) return stripXSS(substr($in, 0, $i));
   // Add a ... (Horizonal ellipses)
   return str_replace('  ', '&#zwsp;', stripXSS(substr($in, 0, $i)."&hellip;"));
}
#+----------------------------------------------------------------------------+#
// If simulating a cooperplate gothic type of font with another font face (all
// caps, but lowercase is smaller) use this to wrap capital letters with an HTML
// element (span, etc.) then use CSS To make them larger :)
function makeTitular($in, $elem='big') {
  return preg_replace('/([^a-z\s]+)/', "<{$elem}>$1</{$elem}>", $in);
}
#+----------------------------------------------------------------------------+#
function blogtime($datetimestring = '1970-01-01 00:00:00', $format = 'U') {
  $dt = new DateTime($datetimestring);
  return $dt->format($format);
}
#+----------------------------------------------------------------------------+#
function create_slug($in) {
  $prev = '';
  /*$input = str_replace('-', ' ', strtolower(trim($input)));
  $input = preg_replace('/\b(the|a|an|of)\b/', '-', $input);
    // Unnecessary
  $input = preg_replace('/([^a-z0-9\-])+/', '-', $input);*/
  $input =  preg_replace('/([^a-z0-9\-])+/', '-', 
              preg_replace('/\b(the|a|an|of)\b/', '-', 
                str_replace('-', ' ', strtolower(trim($in)))
              )
            );
  do {
    $prev = $input;
    $input = str_replace("--", "-", $input);
    $c = strlen($input)-1;
    if($input[0] == '-') $input = substr($input, 1); // Trim first character?
    if($input[$c] == '-') $input = substr($input, 0, $c);// Trim last character?
  } while($prev != $input);
  // Get rid of leading dashes
  return $input;
}
#+----------------------------------------------------------------------------+#
function makebinsafe($in) { // Return a binary-safe base-64 string
  return convBase(bin2hex($in), '0123456789abcdef',
          './0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
}
#+----------------------------------------------------------------------------+#
function forceASCII($in, $allow_linebreaks=false) {
  // Remove non-ASCII characters
  if($allow_linebreaks) {
    $a = explode("\n", $in);
    foreach($a as $i => $n) {
      $a[$i] = forceASCII($n);
    }
    return trim(implode("\n", $a));
  } else {
    return preg_replace('/([^\x20-\x7f]+)/', '', $in);
  }
}
#+----------------------------------------------------------------------------+#
function xmlspace($XmlObject)
{
  $dom = new DOMDocument('1.0');
  $dom->preserveWhiteSpace = false;
  $dom->formatOutput = true;
  $dom->loadXML($XmlObject->asXML());
  return $dom->saveXML();
}
#+----------------------------------------------------------------------------+#
# END CHARACTER ENCODING                                                       #
################################################################################
function mysqli_single($query, $link = null) {
  if(empty($link)) $link = $GLOBALS['MySQL'];
  try {
    $res = mysqli_query($link, $query) or die(mysqli_error($link));
    $row = mysqli_fetch_row($res);
  } catch(Exception $e) {
    ob_start();
    var_dump($e);
    return ob_get_clean();
  }
  return $row[0];
}
function mysqli_quick_assoc($query, $link = null) {
  if(empty($link)) $link = $GLOBALS['MySQL'];
  $res = mysqli_query($link, $query);
  return mysqli_fetch_assoc($res);
}
function my_query($string, $resultmode=null) {
  if(!empty($resultmode)) { 
    return mysqli_query($GLOBALS['MySQL'], $string, $resultmode);
  }
  return mysqli_query($GLOBALS['MySQL'], $string);
}
$months = [
           '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', 
           '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', 
           '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
          ];
  define('LIBSCOTT_LOADED', true);
}