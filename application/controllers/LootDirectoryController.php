<?php
class LootDirectoryController extends RootController {
    
    public $title = "Loot Directory - Leviatha";
    
    public function get() {
        $this->render('lootdirectory.php');
    }
    
    public function post() {
        
    }
}