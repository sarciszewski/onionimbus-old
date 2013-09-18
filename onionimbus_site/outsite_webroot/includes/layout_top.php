<?
/* 
 * Open the site-wide HTML for the layout
 */
require_once "./global.php";
?>
<!DOCTYPE html>
<html>
  <head>
    <title>OnioNimbus</title>
  </head>
  <body>
    <header>
      <h1 id="siteHeader"><a href="https://onionimb.us">Onionimb.us</a></h1>
    <?
    if(isset($_SESSION['user_id'])) {
      // We are logged in as a user, show the user menu
      ?>
      <nav>
        <ul>
          <li><a href='/sites/'>Sites</a></li>
          <li><a href='/account/'>Account</a></li>
          <li><a href='https://github.com/sarciszewski/onionimbus'>Github</a></li>
          <li><a href='/about/'>About</a></li>
          <li><a href='/contact/'>Contact</a></li>
        </ul>
      </nav>
      <?
    } else {
      // We are a guest, show the guest menu
      ?>
      <nav>
        <ul>
          <li><a href='/login/'>Log In</a></li>
          <li><a href='/sign_up/'>Sign Up</a></li>
          <li><a href='https://github.com/sarciszewski/onionimbus'>Github</a></li>
          <li><a href='/about/'>About</a></li>
          <li><a href='/contact/'>Contact</a></li>
        </ul>
      </nav>
      <?
    }
    ?>
    </header>