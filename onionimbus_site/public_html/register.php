<?php
require_once "/var/onionimbus/includes/global.php";
if(isset($_SESSION['user_id'])) {
  header("Location: /account/"); exit;
}
$pageTitle = "Sign Up";
if(isset($_POST['username']) && isset($_POST['password'])) {
  $username = noinject($_POST['username']);
  $password = create_hash($_POST['password']);
  # DEBUGGING
  header("Content-Type: text/plain;charset=UTF-8");
  echo "{$username}\n";
  echo "{$password}";
  exit;
  # END DEBUGGING
  // TODO: insert into a database
} else {
  require_once "/var/onionimbus/includes/layout_top.php";
  ?>
<h2>Sign Up</h2>
<form method="post">
  <label class="formLabel textright">Username:</label>
    <input type="text" id="signup_username" required="required" name="username" pattern="^([A-Za-z0-9_\-]+)$" style="width: 600px;" /><br />
  <label class="formLabel textright">Passphrase:</label>
    <input type="password" id="signup_username" required="required" name="password" pattern="^(.{10,})$" />
    (minimum 10 characters)<br />
  <label class="formLabel textright">Contact Information:</label>
    <textarea name="contactInfo" style="margin-top: -1em; margin-left: 160px;" placeholder="Email address, phone number, XMPP account, GPG keys; anything we would need to contact you in case we received a DMCA notice or other legal document"></textarea><br /> 
  <input type="hidden" name="hmac" value="<?php
    $HMAC['signupForm'] = random_bytes(32);
    echo hash_hmac('sha256', $_SERVER['REMOTE_ADDR'], $HMAC['signupForm']);
  ?>" />
  <button type="submit">
    Create Account
  </button>
</form>
  <?
  require_once "/var/onionimbus/includes/layout_bottom.php";
}
?>