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


  static public function starts_with($haystack, $needle) {
    return strpos($haystack, $needle) === 0;
  }

  static public function ensure_protocol($link_url) {
    if ( !self::starts_with($link_url, 'https://') && !self::starts_with($link_url, 'http://') ) {
      $link_url = 'http://' . $link_url;
    }
    return $link_url;
  }


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


}
?>
