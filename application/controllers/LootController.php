<?php
class LootController extends RootController {
    
    public $title = "Loot Directory - Leviatha";
    public $item;
    public $json = false;
    
    public function get() {
        
        $loot = new LootModel;
        
        // JSON
        if (isset($_GET['format'])) {
            if ($_GET['format'] == "json") {
                $this->json = true;
            }
        }
        
        // URL Item Token
        $urlname = F3::get('PARAMS.item');
    
        // Collect Item Data
        $this->item = ($this->json) ? $loot->item($urlname) : json_decode($loot->item($urlname));
        
        if ($this->item) {
            if ($this->json) {
                $this->render('lootjson.php', false);
            } else {
                $this->title = $this->item->name . " - Diablo 2 Database";
                $this->render('loot.php');
            }
        } else {
            F3::set('EXCEPTION.error', "Doh! This item doesn't seem to be in our database.");
            $this->render('blank.php');
        }
    }
}
