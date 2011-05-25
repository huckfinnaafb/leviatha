<?php
define ('__SITE_PATH', "/home/huckfinnaafb/www.leviatha.org/");
// PHP Fat Free Framework (http://fatfree.sourceforge.net/)
require_once (__SITE_PATH . "/library/F3/F3/F3.php");
F3::set('AUTOLOAD',
    __SITE_PATH . "/application/controllers/|" .
    __SITE_PATH . "/library/F3/autoload/"
);
F3::config(__SITE_PATH . "/application/config/config.cfg");
$leviatha = array();
foreach(F3::sql("SELECT name FROM loot") as $row) {
    $leviatha[] = $row["name"];
}
echo json_encode($leviatha);