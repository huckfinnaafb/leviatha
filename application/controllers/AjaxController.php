<?php
class AjaxController extends RootController {
    public function loot() {
        $loot = new LootModel;
        $this->loot = $loot->all();
        echo json_encode($this->loot);
    }
}