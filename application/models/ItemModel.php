<?php
class ItemModel extends LootModel {
    
    // Item Characteristics
    public $id;
    public $name;
    public $urlname;
    public $level;
    public $levelreq;
    public $rarity;
    public $family;
    public $parent;
    public $class;
    public $division;
    public $kingdom;
    public $domain = "loot";
    
    // Boolean Key=>Value pairs
    public $flags = array();
    
    // Item Properties
    public $properties = array(
        "normal" => array(),
        "magic" => array(),
        "set" => array(),
        "family" => array()
    );
    
    // Contain arrays of identifiers
    public $variants = array();
    public $similar = array();
    public $siblings = array();
    
}