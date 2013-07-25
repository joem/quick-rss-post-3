<?php
require_once __DIR__.'/nonpublicstuff/config.inc';
include(__DIR__."/nonpublicstuff/classes/template.class.php");
$templates_dir    = "nonpublicstuff/templates/";

$page_template = 'rss-xml.tpl.html';
$item_template = 'rss-xml-item.tpl.html';

$desired_type = trim($_GET['type']);

switch ($desired_type) {
  case 'plain':
  case 'text':
  case 'plaintext':
  case 'debug':
    // For easy debugging
    header('Content-Type: text/plain');
    break;

  case 'xml':
    // Not sure if this is useful, but whatevs.
    header('Content-Type: text/xml');
    break;

  case 'html':
    // For your viewing pleasure
    header('Content-Type: text/html');
    $page_template = 'rss-html.tpl.html';
    $item_template = 'rss-html-item.tpl.html';
    break;

  default:
    // RSS 2.0
    header("Content-Type: application/rss+xml; charset=ISO-8859-1");
    break;
}


date_default_timezone_set('America/New_York');
// if this doesn't work on the server, see:
// http://stackoverflow.com/questions/5535514/how-to-fix-warning-from-date-in-php

$items = "";
$latest_post_timestamp_rfc_2822 = "";


//TODO: Make all this title, link, description stuff for the main feed configurable!!!!!


try {
  $dbh = new PDO("mysql:host=".$config['db']['host'].";dbname=".$config['db']['dbname'], $config['db']['username'], $config['db']['password']);
  $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

  $sth = $dbh->query('SELECT * FROM posts ORDER BY post_timestamp DESC LIMIT 1');
  $sth->setFetchMode(PDO::FETCH_ASSOC);
  $first_row = $sth->fetch();
  $latest_post_timestamp_rfc_2822 = date("r", strtotime($first_row['post_timestamp']));


  foreach($dbh->query('SELECT * FROM posts ORDER BY post_timestamp DESC LIMIT 20') as $row) {
    // EACH ITEM
    $item = new Template($GLOBALS['templates_dir'] . $item_template);
    $item->set("post_timestamp", $row["post_timestamp"]);
    $item->set("root_address", $config['root_address']);
    $item->set("post_id", $row["id"]);

    if ($row["url"]) {
      if ($desired_type == 'html') {
        $link = '<p><a href="' . ensure_protocol($row['url']) . '">' . $row['url'] . '</a></p>';
      } else {
        $link = "&lt;p&gt;&lt;a href=&quot;" . ensure_protocol($row['url']) . "&quot;&gt;" . $row['url'] . "&lt;/a&gt;&lt;/p&gt;";
      }
      $item->set("body", $link);
    } else {
      if ($desired_type == 'html') {
        $note = '<pre>' . stripslashes($row["note"]) . '</pre>';
      } else {
        $note = '&lt;pre&gt;' . stripslashes($row["note"]) . '&lt;/pre&gt;';
      }
      $item->set("body", $note);
    }

    $item->set("post_timestamp_rfc_2822", date("r", strtotime($row['post_timestamp'])));

    $items .= $item->output();

  }
  $dbh = null;


} catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";  //DEBUG FIXME: Disable before production!!!
  error_log("Error!: " . $e->getMessage() . "<br/>");
  die();
}

$page = new Template($GLOBALS['templates_dir'] . $page_template);
$page->set("items", $items);
$page->set("latest_post_timestamp_rfc_2822", $latest_post_timestamp_rfc_2822);
echo $page->output();


function starts_with($haystack, $needle) {
    return strpos($haystack, $needle) === 0;
}

function ensure_protocol($link_url) {
  if ( !starts_with($link_url, 'https://') && !starts_with($link_url, 'http://') ) {
    $link_url = 'http://' . $link_url;
  }
  return $link_url;
}

?>
