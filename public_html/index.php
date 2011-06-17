<?php

// Application Configuration
include "../config.php";

// PHP Fat Free Framework (http://fatfree.sourceforge.net/)
require_once (__SITE_PATH . "/library/F3/F3/F3.php");

// Framework Configuration
F3::config(__SITE_PATH . "/f3config.cfg");

// Autoload Assets
F3::set('AUTOLOAD',
    __SITE_PATH . "/application/controllers/|" .
    __SITE_PATH . "/application/models/|" .
    __SITE_PATH . "/library/F3/autoload/"
);

// Framework Variables
F3::set('GUI', __SITE_PATH . "/application/views/");

// Application Routes
F3::route('GET /', array(new RootController, 'get'));
F3::route('GET /loot', array(new LootDirectoryController, 'get'));
F3::route('GET /loot/@item', array(new LootController, 'get'));
F3::route('GET /search', array(new SearchController, 'get'));
F3::route('GET /admin', array(new AdminController, 'get'));
F3::route('GET /sandbox', array(new SandboxController, 'get'));

// Let's Roll Out, Autobots!
F3::run();
