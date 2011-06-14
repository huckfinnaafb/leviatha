<?php
class RootController {
    
    // Meta Information
    public $author      = "Samuel Ferrell";
    public $copyright   = "Copyright Samuel Ferrell 2011. All Rights Reserved.";
    public $description = "It's a database. For Diablo 2! And it does a bunch of other stuff, too.";
    public $direction   = "ltr";
    public $keywords    = "diablo 2 database, diablo 2, diablo 2 builds, diablo 2 classes, diablo 2 loot";
    public $language    = "en";
    public $title       = "Leviatha - Diablo 2 Database: Loot, Builds, News, Monsters, and more!";
    
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
            "enabled" => true,
            "selected" => false,
            "text" => "Monsters",
			"title" => "Monsters, oh my!"
        ),
        "/world/" => array(
            "enabled" => true,
            "selected" => false,
            "text" => "World",
			"title" => "The World of Sanctuary"
        ),
        "/builds/" => array(
            "enabled" => true,
            "selected" => false,
            "text" => "Builds",
			"title" => "Class Builds and Skills"
        ),
        "/community/" => array(
            "enabled" => true,
            "selected" => false,
            "text" => "Community",
			"title" => "Forums and Discussion"
        )
    );
    
    // Feature Switch
    public $flag = array(
        "exceptions" => true,
        "search" => true
    );
    
    public function get() {
        $this->render('home.php');
    }
    
    /**
        Global render method - all visible content goes through hurr
    **/
    public function render($file, $layout = true) {
        if ($layout) {
            include (F3::get('GUI') . "layout.php");
        } else {
            include (F3::get('GUI') . "nolayout.php");
        }
    }
    
}
