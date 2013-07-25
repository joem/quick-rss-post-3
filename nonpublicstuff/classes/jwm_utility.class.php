<?php

// This is just a utility class to hold random stuff that doesn't have anywhere else, yet.

class JwmUtility {

  //static public $Name = "Foo Bar";            //example: static property, call JwmUtility::$Name

  //static public function helloWorld() {       //example: static method, call JwmUtility::helloWorld()
  //  print "Hello world from " . self::$Name;
  //}

  static public function fake404() {
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
    header("Status: 404 Not Found");
    $_SERVER['REDIRECT_STATUS'] = 404;
    echo "<h1>404 Not Found</h1>";
    //readfile('404missing.html');
    echo "The page that you have requested could not be found.";
    exit();
  }

}
?>
