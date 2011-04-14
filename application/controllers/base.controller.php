<?php
    /*
        Author: Samuel Ferrell
        Purpose: Central Controller for Routing
    */
    
    class base {
        public $title;
        public $error;
        public $warning;
        public $success;
    
        public function homepage() {
            include (F3::get('GUI') . "default.php");
        }
        public function search() {
            $search = new search;
            $search->init();
        }
        public function loot() {
            $loot = new loot;
            $loot->init();
        }
        public function loot_central() {
            $this->title = "Loot Central";
            include (F3::get('GUI') . "loot.php");
        }
        public function loot_directory() {
            $this->title = "Loot Directory";
            include (F3::get('GUI') . "loot/directory.php");
        }
    }