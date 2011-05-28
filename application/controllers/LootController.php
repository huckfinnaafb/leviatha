<?php
class LootController extends RootController {
    
    public $title = "Loot Directory - Leviatha";
    public $item;
    
    public function get() {
        
        // URL Item Token
        $urlname = F3::get('PARAMS.item');
    
        // Collect Item Data (JSON)
        $loot = new LootModel;
        $this->item = json_decode($loot->item($urlname));
        
        // Determine rarity
        $this->render('loot.php');
    }
}
