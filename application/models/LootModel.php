<?php
/**
    Author: Samuel Ferrell (huckfinnaafb@gmail.com)
    Purpose: Generate a JSON object of any item, including, but not limited to,
        item properties, related items, and statistical information.
**/
class LootModel extends RootModel {

    public $name;
    public $id;
    
    public $query = array(
        "all" => "SELECT name, urlname, level, levelreq, rarity, class, division FROM loot ORDER BY rarity DESC",
        "divisions" => "SELECT division, kingdom FROM relate_division"
    );
    
    public function item($identifier) {
        
    }
    
    public function all() {
        return F3::sqlBind($this->query['all']);
    }
    
    public function relations() {
        return F3::sqlBind($this->query['divisions']);
    }
}