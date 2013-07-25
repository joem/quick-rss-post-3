<?php

require_once __DIR__.'/nonpublicstuff/config.inc';
include(__DIR__."/nonpublicstuff/classes/template.class.php");

$templates_dir    = "nonpublicstuff/templates/";

$bookmarklet = new Template($GLOBALS['templates_dir']."bookmarklet.tpl.js");
$bookmarklet->set('root_address', $config['root_address']);
//echo $bookmarklet->output();

$page = new Template($GLOBALS['templates_dir']."bookmarklet_page.tpl.html");
//$page->set('root_address', $config['root_address']);
$page->set('bookmarklet', $bookmarklet->output());
echo $page->output();

?>
