<?php

require_once __DIR__.'/nonpublicstuff/config.inc';
include(__DIR__."/nonpublicstuff/classes/template.class.php");

$templates_dir    = "nonpublicstuff/templates/";

$page = new Template($GLOBALS['templates_dir']."bookmarklet_page.tpl.html");
$page->set('root_address', $config['root_address']);
echo $page->output();

?>
