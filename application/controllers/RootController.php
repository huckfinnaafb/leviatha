<?php
class RootController {
    
    // Meta Information
    public $author      = "Samuel Ferrell";
    public $description = "Find everything you want to know about Diablo 2";
    public $direction   = "ltr";
    public $keywords    = "diablo 2 database, diablo 2, diablo 2 builds, diablo 2 classes, diablo 2 loot";
    public $language    = "en";
    public $title       = "Leviatha - Diablo 2 Database: Loot, Builds, Monsters, and more!";
    
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
            "text" => "Home"
        ),
        "/loot/" => array(
            "enabled" => true,
            "selected" => false,
            "text" => "Loot"
        )
    );
    
    // Feature Switch
    public $flag = array(
        "exceptions" => true,
        "login" => true,
        "register" => true,
        "search" => true
    );
    
    public function get() {
        include (F3::get('GUI') . "home.php");
    }
    
    /**
        Global Render Method - Everything eventually passes through here
    **/
    public function render($file) {
        include (F3::get('GUI') . "layout.php");
    }
    
}