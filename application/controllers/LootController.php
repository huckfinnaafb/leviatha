<?php
class LootController extends RootController {
    
    public $title = "Loot - Diablo 2 Database";
    
    // Item Objects
    public $item;
    public $similar = array();
    public $variants = array();
    public $siblings = array();
    
    // URL Option
    public $json = false;
    
    public function get() {
        
        $loot = new LootModel;
        
        // Check for JSON
        if (isset($_GET['format'])) {
            if ($_GET['format'] == "json") {
                $this->json = true;
            }
        }
        
        // URL Item Token
        $urlname = F3::get('PARAMS.item');
    
        // Fetch Item Data
        $this->item = ($this->json) ? $loot->item($urlname) : json_decode($loot->item($urlname));
        
        // Fetch Similar
        if(!empty($this->item->similar)) {
            foreach($this->item->similar as $similar) {
                $this->similar[] = json_decode($loot->item($similar->urlname, array("verbose" => false)));
            }
        }
        
        // Fetch Variants
        if(!empty($this->item->variants)) {
            foreach($this->item->variants as $variant) {
                $this->variants[] = json_decode($loot->item($variant->urlname, array("verbose" => false)));
            }
        }
        
        // Fetch Siblings
        if(!empty($this->item->siblings)) {
            foreach($this->item->siblings as $sibling) {
                $this->siblings[] = json_decode($loot->item($sibling->urlname, array("verbose" => false)));
            }
        }
        
        $this->navigation['/loot/']['selected'] = true;
        
        if ($this->item) {
            if ($this->json) {
                $this->title = $this->item['name'] . " - Diablo 2 Database";
                $this->render('lootjson.php', false);
            } else {
                $this->title = $this->item->name . " - Diablo 2 Database";
                $this->render('loot.php');
            }
        } else {
            F3::set('NOTIFY.error', "Doh! This item doesn't seem to be in our database.");
            $this->render('blank.php');
        }
    }
}
