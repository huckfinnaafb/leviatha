<?php
class ItemModel extends LootModel {
    
    // Item Characteristics
    public $id;
    public $name;
    public $urlname;
    public $level;
    public $levelreq;
    public $rarity;
    public $grade;
    public $parent;
    public $family;
    public $base;
    public $class = null;
    public $type;
    
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