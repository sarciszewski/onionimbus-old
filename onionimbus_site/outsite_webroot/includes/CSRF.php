<?php
/* 
 * to verify:
 * if(CSRF::verify('unique_form_id', $_POST['csrf']))
 * or
 * if(CSRF::post('unique_form_id', 'csrf'))
 * or
 * if(CSRF::get('unique_form_id', 'csrf'))
 * 
 * to generate:
 * echo CSRF::generate("unique_form_id");
 */
abstract class CSRF {
  /*~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~*/ 
  public function generate($index) {
    $strong = false;
    $_SESSION['csrfTokens'][$index] = openssl_random_pseudo_bytes(32, $strong);
    if(!$strong) {
      // FUCK! Let's get inventive then...
      $fp = fopen("/dev/urandom", "rb");
      $_SESSION['csrfTokens'][$index] = hash_hmac('sha256',
           fread($fp, 32), // 
           microtime(true).$_SERVER['REMOTE_ADDR'].mt_rand(0, PHP_INT_MAX),
           true); // Raw binary
      fclose($fp);
    }
    return hash_hmac('sha256', $_SERVER['REMOTE_ADDR'], $_SESSION['csrfTokens'][$index]);
  }
  /*~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~*/ 
  public function get($req_index = null, $session_index = null) {
    return self::req_helper($_GET, $req_index, $session_index);
  }
  /*~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~*/ 
  public function insert() {
    $index = base64_encode(mcrypt_create_iv(15, MCRYPT_DEV_URANDOM));
    $hmac = self::generate($index);
    echo "<input type=\"hidden\" name=\"_CSRF_KEY\" value=\"".$index."\" />\n";
    echo "<input type=\"hidden\" name=\"_CSRF_TOKEN\" value=\"".$hmac."\" />\n";
  }
  /*~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~*/ 
  public function post($req_index = null, $session_index = null) {
    return self::req_helper($_POST, $req_index, $session_index);
  }
  /*~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~*/ 
  public function req_helper($array, $req_index, $session_index) {
    if(empty($req_index)) {
      $req_index = '_CSRF_TOKEN';
    }
    if(empty($session_index)) {
      if(isset($array['_CSRF_KEY'])) {
        $session_index = $array['_CSRF_KEY'];
      }
    }
    if(empty($_POST[$req_index]) || is_array($_POST[$req_index])) {
      $_POST[$req_index] = bin2hex(mcrypt_create_iv(32)); // Random garbage
    }
    return self::verify($session_index, $_POST[$req_index]);
  }
  /*~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~*/ 
  public function verify($index, $hash) {
    if(empty($_SESSION['csrfTokens'][$index])) {
      // They never loaded the page before!
    } elseif( $hash === hash_hmac('sha256', $_SERVER['REMOTE_ADDR'], $_SESSION['csrfTokens'][$index])) {
      unset($_SESSION['csrfTokens'][$index]);
      return true;
    }
    return false;
  }
}