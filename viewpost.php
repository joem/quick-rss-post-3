<?php
require_once __DIR__.'/nonpublicstuff/config.inc';
require_once(__DIR__."/nonpublicstuff/classes/jwm_utility.class.php");
require_once(__DIR__."/nonpublicstuff/classes/template.class.php");

if(empty($_GET)) {
  JwmUtility::fake404();
  //header ("location:http://google.com");
  //exit;
}

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
    if ( !JwmUtility::starts_with($row['url'], 'https://') && !JwmUtility::starts_with($row['url'], 'http://') ) {
      $display_url = 'http://' . $row['url'];
    }
    $link = "<a href=\"" . $display_url . "\">" . $row['url'] . "</a>";
    $template = 'viewpost-link.tpl.html';
  } else {
    $link = "";
    $template = 'viewpost-note.tpl.html';
  }

  $page = new Template($GLOBALS['templates_dir'].$template);
  $page->set('timestamp', $row["post_timestamp"]);
  $page->set('id', $row['id']);
  $page->set('note', stripslashes($row['note']));
  $page->set('link', $link);

  $dbh = null;
} catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";  //DEBUG FIXME: Disable before production!!!
  error_log("Error!: " . $e->getMessage() . "<br/>");
  $dbh = null;
  die();
}

echo $page->output();


?>
