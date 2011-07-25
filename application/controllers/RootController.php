<?php
/**
    2011 Samuel Ferrell
    Leviatha.org
    The Leviatha Project
**/
class RootController {
    
    // Meta Information
    public $author      = "Samuel Ferrell";
    public $copyright   = "Copyright Samuel Ferrell 2011. All Rights Reserved.";
    public $description = "It's a database. For Diablo 2! And it does a bunch of other stuff, too.";
    public $direction   = "ltr";
    public $keywords    = "diablo 2 database, diablo 2, diablo 2 builds, diablo 2 classes, diablo 2 loot";
    public $language    = "en";
    public $title       = "Leviatha - Diablo 2 Database: Loot, Builds, News, Monsters, and more!";
    
    public $heading     = null;
    
    // Page Assets
    public $scripts = array('jquery', 'leviatha');
    public $styles = array('style');
    
    // Common Properties
    public $dateform = "l\, F jS Y";
    
    // Site Navigation
    public $navigation = array(
        "/" => array(
            "enabled" => true,
            "selected" => false,
            "text" => "Home",
			"title" => "Return Home"
        ),
        "/loot/" => array(
            "enabled" => true,
            "selected" => false,
            "text" => "Loot",
			"title" => "Loot Directory"
        ),
        "/monsters/" => array(
            "enabled" => false,
            "selected" => false,
            "text" => "Monsters",
			"title" => "Monsters, oh my!"
        ),
        "/world/" => array(
            "enabled" => false,
            "selected" => false,
            "text" => "World",
			"title" => "The World of Sanctuary"
        ),
        "/classes/" => array(
            "enabled" => false,
            "selected" => false,
            "text" => "Classes",
			"title" => "Class Builds and Skills"
        ),
        "/community/" => array(
            "enabled" => false,
            "selected" => false,
            "text" => "Community",
			"title" => "Forums and Discussion"
        )
    );
    
    public $flag = array(
        "notifications" => true,
        "search" => true,
        "headings" => true
    );
    
    public function get() {
        F3::reroute('/loot');
    }
    
    public function about() {
        $this->navigation['/']['selected'] = true;
        $this->render('about.php');
    }
    
    public function render($file, $layout = true) {
        include ($layout) ? F3::get('GUI') . "layout.php" : F3::get('GUI') . "nolayout.php";
    }
    
}
