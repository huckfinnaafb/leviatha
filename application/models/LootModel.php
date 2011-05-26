<?php
/**
    Author: Samuel Ferrell (huckfinnaafb@gmail.com)
    Purpose: Generate a JSON object of any item, including, but not limited to,
        item properties, related items, and statistical information.
**/
class LootModel extends RootModel {
    
    public $id;
    public $name;
    public $urlname;
    public $level;
    public $levelreq;
    public $class;
    public $division;
    
    public $flags = array();
    
    public $properties = array(
        "normal" => array(),
        "magic" => array(),
        "set" => array(),
        "family" => array()
    );
    
    public $variants = array();
    public $similar = array();
    
    public $family;
    public $familyMembers;
    
    public $query = array(
        "item" => "
            SELECT *
            FROM loot
            WHERE loot.urlname = :item
        ",
        "all" => "SELECT name, urlname, level, levelreq, rarity, class, division FROM loot ORDER BY rarity DESC",
        "divisions" => "SELECT division, kingdom FROM relate_division"
    );
    
    public function item($identifier) {
        return F3::sqlBind($this->query['item'], array('item' => $identifier));
    }
    
    public function all() {
        return F3::sqlBind($this->query['all']);
    }
    
    public function relations() {
        return F3::sqlBind($this->query['divisions']);
    }
}