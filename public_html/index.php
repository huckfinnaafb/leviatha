<?php
define ('__SITE_PATH', realpath(dirname(__DIR__)));
// PHP Fat Free Framework (http://fatfree.sourceforge.net/)
require_once (__SITE_PATH . "/library/F3/F3/F3.php");
F3::set('AUTOLOAD',
    __SITE_PATH . "/application/controllers/|" .
    __SITE_PATH . "/library/F3/autoload/"
);
F3::config(__SITE_PATH . "/application/config/config.cfg");
F3::set('RELEASE', true);
F3::set('E404', "warning/e404.html");
F3::set('GUI', __SITE_PATH . '/gui/');
F3::set('GET',F3::scrub($_GET));
F3::route('GET /', array(new base, 'homepage'));
F3::route('GET /search', array(new base, 'search'));
F3::route('GET /loot', array(new base, 'loot_central'), 86400);
F3::route('GET /loot/directory', array(new base, 'loot_directory'), 86400);
F3::route('GET /loot/@item', array(new base, 'loot'), 3600);
// F3::route('GET /almanac', function() { include (F3::get('GUI') . "almanac.html"); });
// F3::route('GET /test', array(new script, 'reassign'));
// F3::route('GET /sitemap', function() { F3::sitemap(); });
// F3::route('GET /profile', function () { echo "<pre>"; var_dump(F3::profile()); echo "</pre>"; });
F3::run();