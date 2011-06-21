<?php
class LootDirectoryController extends RootController {
    
    public $title = "Loot Directory - Diablo 2 Database";
    public $heading = "Loot Directory";
    
    public $items = array();
    public $types = array();
    public $kingdoms = array(
        "wep" => "Weapons", 
        "armor" => "Armor", 
        "acc" => "Accessories", 
        "misc" => "Miscellaneous"
    );
    
    public function get() {
        
        $loot = new LootModel;
        
        // Fetch All Items
        $this->items = $loot->all();
        
        // Fetch Types
        $this->types = $loot->types();
        
        F3::set('NOTIFY.tip', "Items are sorted alphabetically by their parent, then by descending rarity and level.");
        $this->navigation['/loot/']['selected'] = true;
        $this->render('lootdirectory.php');
    }
}