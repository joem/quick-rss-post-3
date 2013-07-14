<?php
if(empty($_GET)) {
  header ("location:http://google.com");
  exit;
}
require_once __DIR__.'/nonpublicstuff/config.inc';

$id = $_GET['id'];

try {
  $dbh = new PDO("mysql:host=".$config['db']['host'].";dbname=".$config['db']['dbname'], $config['db']['username'], $config['db']['password']);
  $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

  $params = array(':id' => $id);

  $sth = $dbh->prepare("SELECT * FROM posts WHERE id = :id LIMIT 1");
  $sth->execute($params);
  $row = $sth->fetch(); // just get one row, but that's all i want anyway.

  //TODO: Make this way nicer and prettier... Maybe some sort of 
  //        typographically-pretty grid based design? And use a template! That'll be
  //        way easier!

  echo "<html>";
  echo '<head>';
  echo " <title>" . $row["post_timestamp"] . " - permalink</title>";
  echo '</head>';
  echo '<body>';

  //TODO: Make a nice, simple, but ok-looking format for all this.
  echo "<p>id: " . $row['id'] . "<br />";
  echo "timestamp: " . $row['post_timestamp'] . "</p>";
  //TODO: Need to handle URLs better! For instance, maryhola.com doesn't link right...

  if ( $row['url'] ) {
    $display_url = $row['url'];
    if ( !starts_with($row['url'], 'https://') && !starts_with($row['url'], 'http://') ) {
      $display_url = 'http://' . $row['url'];
    }
    echo "<p>url: <a href=\"" . $display_url . "\">" . $row['url'] . "</a></p>";
  } else {
    echo "<p>url: </p>";
  }

  //echo "<p>url: <a href=\"" . $row['url'] . "\">" . $row['url'] . "</a></p>";
  echo "<p>note: " . $row['note'] . "</p>";

  echo '</body>';
  echo '</html>';

  $dbh = null;
} catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";  //DEBUG FIXME: Disable before production!!!
  error_log("Error!: " . $e->getMessage() . "<br/>");
  die();
}

function starts_with($haystack, $needle) {
    return strpos($haystack, $needle) === 0;
}

?>
