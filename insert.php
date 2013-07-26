<?php

require_once(__DIR__."/nonpublicstuff/classes/jwm_utility.class.php");
require_once(__DIR__.'/nonpublicstuff/config.inc');
include(__DIR__."/nonpublicstuff/classes/template.class.php");

// Make the page non-existant if you're not getting to it remotely correctly.
if(empty($_POST['inputbody']) && empty($_POST['textareabody']) && empty($_GET['post'])) {
  JwmUtility::fake404();
}


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
$textareabody = htmlentities(trim($_POST[textareabody]));

if ($textareabody) {
  $postbody = $textareabody;
} else {
  $postbody = $getbody;
}

// See if the postbody is a URL or not.
if(JwmUtility::filter_for_url($postbody)) {
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
  $page = new Template($GLOBALS['templates_dir']."failure.tpl.html");
  $page->set("currdate", date("r"));
  $page->set("msg", "Something didn't work right.");
  echo $page->output();
  exit();
}


// If successful, show succes then redirect via js after a pause.
$page = new Template($GLOBALS['templates_dir']."success.tpl.html");
$page->set("currdate", date("r"));
$page->set("msg", $msg);
echo $page->output();


?>
