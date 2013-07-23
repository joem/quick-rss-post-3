<?php
if(empty($_GET)) {
  header ("location:http://google.com");
  exit;
}
require_once __DIR__.'/nonpublicstuff/config.inc';
include(__DIR__."/nonpublicstuff/classes/template.class.php");
$templates_dir    = "nonpublicstuff/templates/";

$id = $_GET['id'];

try {
  $dbh = new PDO("mysql:host=".$config['db']['host'].";dbname=".$config['db']['dbname'], $config['db']['username'], $config['db']['password']);
  $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

  $params = array(':id' => $id);

  $sth = $dbh->prepare("SELECT * FROM posts WHERE id = :id LIMIT 1");
  $sth->execute($params);
  $row = $sth->fetch(); // just get one row, but that's all i want anyway.

  if ( $row['url'] ) {
    $display_url = $row['url'];
    if ( !starts_with($row['url'], 'https://') && !starts_with($row['url'], 'http://') ) {
      $display_url = 'http://' . $row['url'];
    }
    $link = "<a href=\"" . $display_url . "\">" . $row['url'] . "</a>";
  } else {
    $link = "";
  }

  //TODO: Make the template way nicer and prettier... Maybe some sort of
  //        typographically-pretty grid based design?

  $page = new Template($GLOBALS['templates_dir']."viewpost.tpl.html");
  $page->set('timestamp', $row["post_timestamp"]);
  $page->set('id', $row['id']);
  $page->set('note', $row['note']);
  $page->set('link', $link);
  echo $page->output();

  $dbh = null;
} catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";  //DEBUG FIXME: Disable before production!!!
  error_log("Error!: " . $e->getMessage() . "<br/>");
  $dbh = null;
  die();
}

function starts_with($haystack, $needle) {
    return strpos($haystack, $needle) === 0;
}

?>
