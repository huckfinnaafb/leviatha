<?php
class LootController extends RootController {
    
    public $title = "Loot Directory - Leviatha";
    public $item;
    
    public function get() {
        
        // URL Item Token
        $urlname = F3::get('PARAMS.item');
    
        // Collect Item Data (JSON)
        $loot = new LootModel;
        if (!$this->item = $loot->item($urlname)) {
            F3::set('EXCEPTION.error', "This item doesn't seem to exist in our database. Please make sure the URL is correct.");
        }
        
        // Determine rarity
        $this->render('loot.php');
    }
}
