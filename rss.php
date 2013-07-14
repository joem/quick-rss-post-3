<?php
require_once __DIR__.'/nonpublicstuff/config.inc';

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

  default:
    // RSS 2.0
    header("Content-Type: application/rss+xml; charset=ISO-8859-1");
    break;
}


date_default_timezone_set('America/New_York');
// if this doesn't work on the server, see:
// http://stackoverflow.com/questions/5535514/how-to-fix-warning-from-date-in-php


echo '<?xml version="1.0"?>
<rss version="2.0">
<channel>
<title>Joes Notes</title>
<link>http://llawn.com/</link>
<description>random notes, utter crap</description>
<docs>http://blogs.law.harvard.edu/tech/rss</docs>
';

try {
  $dbh = new PDO("mysql:host=".$config['db']['host'].";dbname=".$config['db']['dbname'], $config['db']['username'], $config['db']['password']);
  $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

  $sth = $dbh->query('SELECT * FROM posts ORDER BY post_timestamp DESC LIMIT 1');
  $sth->setFetchMode(PDO::FETCH_ASSOC);
  $first_row = $sth->fetch();
  echo '<pubDate>' .  date("r", strtotime($first_row['post_timestamp'])) . "</pubDate>\n";
  echo '<lastBuildDate>' .  date("r", strtotime($first_row['post_timestamp'])) . "</lastBuildDate>\n";

  foreach($dbh->query('SELECT * FROM posts ORDER BY post_timestamp DESC LIMIT 20') as $row) {
    echo "<item>\n";
    echo " <title>" . $row["post_timestamp"] . " - permalink</title>\n";
    echo " <link>http://127.0.0.1/~joe/quick-rss-post3/viewpost.php?id=" . $row["id"] . "</link>\n";
    if ($row["url"]) {
      //TODO: Make URLs into proper links!


      $link_url = $row['url'];
      if ( !starts_with($row['url'], 'https://') && !starts_with($row['url'], 'http://') ) {
        $link_url = 'http://' . $row['url'];
      }
      $link = "&lt;p&gt;&lt;a href=&quot;" . $link_url . "&quot;&gt;" . $row['url'] . "&lt;/a&gt;&lt;/p&gt;";

      echo " <description>" . $link . "</description>\n";
      //echo " <description>" . $row["url"] . "</description>\n";
    } else {
      echo " <description>&lt;p&gt;" . $row["note"] . "&lt;/p&gt;</description>\n";
    }
    echo ' <pubDate>' .  date("r", strtotime($row['post_timestamp'])) . "</pubDate>\n";
    echo " <guid>http://127.0.0.1/~joe/quick-rss-post3/viewpost.php?id=" . $row["id"] . "</guid>\n";
    echo "</item>\n";
  }
  $dbh = null;
} catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";  //DEBUG FIXME: Disable before production!!!
  error_log("Error!: " . $e->getMessage() . "<br/>");
  die();
}

echo '</channel>
</rss>';


function starts_with($haystack, $needle) {
    return strpos($haystack, $needle) === 0;
}

?>
