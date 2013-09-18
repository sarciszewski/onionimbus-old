<?
################################################################################
# Functions written by other people, released as open source often modified by #
# Scott to make them more efficient and less glitchy.                          #
################################################################################
/**
  Validate an email address.
  Provide email address (raw input)
  Returns true if the email address has the email
  address format and the domain exists. 
 */
if(!function_exists('validEmail')) {
  function validEmail($email) {
    $atIndex = strrpos($email, "@");
    if (is_bool($atIndex) && !$atIndex) {
      return false;
    } else {
      $domain = substr($email, $atIndex + 1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64) {
        // local part length exceeded
        return false;
      } else if ($domainLen < 1 || $domainLen > 255) {
        // domain part length exceeded
        return false;
      } else if ($local[0] == '.' || $local[$localLen - 1] == '.') {
        // local part starts or ends with '.'
        return false;
      } else if (preg_match('/\\.\\./', $local)) {
        // local part has two consecutive dots
        return false;
      } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
        // character not valid in domain part
        return false;
      } else if (preg_match('/\\.\\./', $domain)) {
        // domain part has two consecutive dots
        return false;
      } else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
        // character not valid in local part unless 
        // local part is quoted
        if(
          !preg_match(
            '/^"(\\\\"|[^"])+"$/',
            str_replace("\\\\", "", $local)
          ) // end preg_match
        ) {
          return false;
        }
      }
      if (!(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
        // domain not found in DNS
        return false;
      }
    }
    return true;
  }
}
if(!function_exists('convBase')) {
  function convBase($numberInput, $fromBaseInput, $toBaseInput) {
    // FROM PHP.NET         PHPCoder@niconet2k.com
    // Modified by Scott Arciszewski     kobrasrealm@gmail.com
    $noPeriods = false;
    if( (strpos($fromBaseInput, '.') === false) && (strpos($toBaseInput, '.') === false) ) {
      $noPeriods = true;
    }
    if ($fromBaseInput == $toBaseInput) {
      return $numberInput;
    }
    $fromBase = str_split($fromBaseInput, 1);
    $toBase = str_split($toBaseInput, 1);
    $number = str_split($numberInput, 1);
    $fromLen = strlen($fromBaseInput);
    $toLen = strlen($toBaseInput);
    $numberLen = strlen($numberInput);
    $retval = '';
    if($toBaseInput == '0123456789') {
      $retval = 0;
      for($i = 1; $i <= $numberLen; $i++) {
        $retval = bcadd(
          $retval,
          bcmul(
            array_search($number[$i - 1], $fromBase),
            bcpow($fromLen, $numberLen - $i)
          )
        );
      }
      if($noPeriods) {
        // Prevent floats from screwing with things
        return preg_replace('/\..*/', '', $retval); # Everything after the period
      }
      return $retval;
    }
    if ($fromBaseInput != '0123456789') {
      $base10 = convBase($numberInput, $fromBaseInput, '0123456789');
    } else {
      $base10 = $numberInput;
    }
    if ($base10 < strlen($toBaseInput)) {
      return $toBase[$base10];
    }
    while ($base10 != '0') {
      $retval = $toBase[bcmod($base10, $toLen)] . $retval;
      $base10 = bcdiv($base10, $toLen, 0);
    }
    if (is_float($retval)) {
      return intval($retval);
    }
    return $retval;
  }
}
#+----------------------------------------------------------------------------+#
if(!function_exists('AES256_Encrypt')) {
  function AES256_Encrypt($sValue, $sSecretKey, $IV = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0") {
    return trim(
      base64_encode(
        mcrypt_encrypt(
          MCRYPT_RIJNDAEL_256, $sSecretKey, $sValue, MCRYPT_MODE_CBC, $IV
        )
      )
    );
  }
}
#+----------------------------------------------------------------------------+#
if(!function_exists('AES256_Decrypt')) {
  function AES256_Decrypt($sValue, $sSecretKey, $IV = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0") {
    return trim(
      mcrypt_decrypt(
        MCRYPT_RIJNDAEL_256, $sSecretKey, base64_decode($sValue), MCRYPT_MODE_CBC, $IV
      )
    );
  }
}
#+----------------------------------------------------------------------------+#
if(!function_exists('TwoFish_Encrypt')) {
  function TwoFish_Encrypt($sValue, $sSecretKey, $IV = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0") {
    return trim(
      base64_encode(
        mcrypt_encrypt(
          MCRYPT_TWOFISH, $sSecretKey, $sValue, MCRYPT_MODE_CBC, $IV
        )
      )
    );
  }
}
#+----------------------------------------------------------------------------+#
if(!function_exists('TwoFish_Decrypt')) {
  function TwoFish_Decrypt($sValue, $sSecretKey, $IV = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0") {
    return trim(
      mcrypt_decrypt(
        MCRYPT_TWOFISH, $sSecretKey, base64_decode($sValue), MCRYPT_MODE_CBC, $IV
      )
    );
  }
}

// Just commit already