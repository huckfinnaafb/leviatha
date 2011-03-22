<?php
    /*
        Author: Samuel Ferrell
        Purpose: Central Controller for Routing
    */
    
    class base {
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
        
        public function loot_directory() {
            include (F3::get('GUI') . "loot/directory.php");
        }
    }