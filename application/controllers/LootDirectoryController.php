<?php
class LootDirectoryController extends RootController {
    
    public $title = "Loot Directory - Leviatha";
    
    public $items = array();
    public $relations = array();
    public $kingdoms = array("weapon", "armor", "accessory");
    
    public function get() {
        
        $loot = new LootModel;
        
        // Fetch All Items
        $this->items = $loot->all();
        
        // Fetch Division -> Kingdom Relationships
        $this->relations = $loot->relations();
        $this->render('lootdirectory.php');
    }
}