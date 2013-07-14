<?php

// Make the page non-existant if you're not getting to it remotely correctly.
if(empty($_POST['inputbody']) && empty($_POST['textareabody']) && empty($_GET['post'])) {
  header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
  header("Status: 404 Not Found");
  $_SERVER['REDIRECT_STATUS'] = 404;
  echo "<h1>404 Not Found</h1>";
  //readfile('404missing.html');
  echo "The page that you have requested could not be found.";
  exit();
}

require_once(__DIR__.'/nonpublicstuff/config.inc');
include(__DIR__."/nonpublicstuff/classes/template.class.php");


// Used to rotated the messages
session_start();
if (!isset($_SESSION['count'])) {
  $_SESSION['count'] = 0;
} else {
  $_SESSION['count']++;
}

date_default_timezone_set('America/New_York');
// if this doesn't work on the server, see:
// http://stackoverflow.com/questions/5535514/how-to-fix-warning-from-date-in-php

$msgs = array('Data Appended <strong>Succesfully</strong>.',
              'Write Was <strong>Succesful</strong>.',
              'Data <strong>Accepted</strong>.',
              '<strong>It Worked.</strong>',
              'It\'s All <strong>Good</strong>.');

$msg = $msgs[$_SESSION['count'] % count($msgs)]; // Determine msg by iterating in $msgs array.

$templates_dir    = "nonpublicstuff/templates/";

$new_url = NULL;
$new_note = NULL;
$postbody = NULL;

$getbody = trim($_GET['post']); // getbody should already have encoded html entities, from the js.
//$inputbody = htmlentities(trim($_POST[inputbody]));
$textareabody = htmlentities(trim($_POST[textareabody]));

if ($textareabody) {
  $postbody = $textareabody;
} else {
  $postbody = $getbody;
}

// See if the postbody is a URL or not.
if(filter_for_url($postbody)) {
  $new_url = $postbody;
} else {
  $new_note = $postbody;
}

// Set up the params to use in the safe db query, then do the query (insert the data).
$params = array(':url' => $new_url, ':note' => $new_note);

try {
  $dbh = new PDO("mysql:host=".$config['db']['host'].";dbname=".$config['db']['dbname'], $config['db']['username'], $config['db']['password']);
  $sth = $dbh->prepare("INSERT INTO posts (url, note) VALUE (:url, :note)");
  $sth->execute($params);
  $dbh = null;
} catch (PDOException $e) {
  error_log("Error!: " . $e->getMessage());
  $page = new Template($GLOBALS['templates_dir']."failure.tpl");
  $page->set("currdate", date("r"));
  $page->set("msg", "Something didn't work right.");
  echo $page->output();
  exit();
}


// Don't go to success.php via header(), since then insert.php stays in the history.
//   (If you really want to go to success.php, use this js window.location.replace,
//   then use it again when you leave success after a setTimeout().)

// If successful, redirect after a pause.

$page = new Template($GLOBALS['templates_dir']."success.tpl.html");
$page->set("currdate", date("r"));
$page->set("msg", $msg);
echo $page->output();




/**
  * Returns the url string if input is a valid url, if not, returns FALSE
  * (I know it's technically not correct, otherwise the filter_var thing would 
  * be enough, but it's correct for  how i intend to use my note taker thing.)
 **/
function filter_for_url($orig_string) {
  if(filter_var($orig_string, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED)) {
    $result = $orig_string; // totally a url
  } else {
    if(!filter_var('http://'.$orig_string, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED)) {
      $result = FALSE; // still not a url
    } else {
      if (preg_match("/^[a-zA-Z0-9]+$/i", $orig_string)) {
        $result = FALSE; // one word, therefore not a url
      } else {
        $result = 'http://'.$orig_string; // a url so assume scheme and append it
      }
    }
  }
  return $result;
}

?>
