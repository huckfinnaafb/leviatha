<?php
class LootDirectoryController extends RootController {
    
    public $title = "Loot Directory - Diablo 2 Database";
    public $heading = "Loot Directory";
    
    public $items = array();
    public $relations = array();
    public $kingdoms = array("weapon", "armor", "accessory");
    
    public function get() {
        
        $loot = new LootModel;
        
        // Fetch All Items
        $this->items = $loot->all();
        
        // Fetch Division -> Kingdom Relationships
        $this->relations = $loot->relations();
        
        F3::set('NOTIFY.tip', "Items are sorted alphabetically by their parent, then by descending rarity and level.");
        $this->navigation['/loot/']['selected'] = true;
        $this->render('lootdirectory.php');
    }
}