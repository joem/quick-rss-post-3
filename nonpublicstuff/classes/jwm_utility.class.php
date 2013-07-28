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
  static public function filter_for_url($orig_string) {
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





  /**
    * Add or update a url parameter
    *
    * from:
    * http://stackoverflow.com/questions/4100538/php-add-update-a-parameter-in-a-url/4101638#4101638
   **/
  static public function addURLParameter($url, $paramName, $paramValue) {
    $url_data = parse_url($url);
    if(!isset($url_data["query"]))
      $url_data["query"]="";

    $params = array();
    parse_str($url_data['query'], $params);
    $params[$paramName] = $paramValue;
    $url_data['query'] = http_build_query($params);
    return self::build_url($url_data);
  }


  /**
    * Build a url, roughly the same as the build_url method from PECL
    *
    * from:
    * http://stackoverflow.com/questions/4100538/php-add-update-a-parameter-in-a-url/4101638#4101638
   **/
  static public function build_url($url_data) {
    $url="";
    if(isset($url_data['host']))
    {
      $url .= $url_data['scheme'] . '://';
      if (isset($url_data['user'])) {
        $url .= $url_data['user'];
        if (isset($url_data['pass'])) {
          $url .= ':' . $url_data['pass'];
        }
        $url .= '@';
      }
      $url .= $url_data['host'];
      if (isset($url_data['port'])) {
        $url .= ':' . $url_data['port'];
      }
    }
    $url .= $url_data['path'];
    if (isset($url_data['query'])) {
      $url .= '?' . $url_data['query'];
    }
    if (isset($url_data['fragment'])) {
      $url .= '#' . $url_data['fragment'];
    }
    return $url;
  }





  /**
   * A simple function to figure out whether there's a previous page, and if so, make a link
   *
   * @return string
   **/
  static public function get_prev_span($current_page_number) {
    if (($current_page_number - 1) < 1) {
      $prev_span = 'prev';
    } else {
      $prev_url = self::addURLParameter($_SERVER["REQUEST_URI"], 'page', ($current_page_number - 1));
      $prev_span = '<a href="' . $prev_url . '">prev</a>';
    }
    return $prev_span;
  }

  /**
   * A simple function to figure out whether there's a next page, and if so, make a link
   *
   * @return string
   **/
  static public function get_next_span($current_page_number, $num_rows, $page_lim) {
    if (($current_page_number * $page_lim) >  $num_rows) {
      $next_span = 'next';
    } else {
      $next_url = self::addURLParameter($_SERVER["REQUEST_URI"], 'page', ($current_page_number + 1));
      $next_span = '<a href="' . $next_url . '">next</a>';
    }
    return $next_span;

  }


}
?>
