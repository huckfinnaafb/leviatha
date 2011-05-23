<?php
class LootModel extends RootModel {
    
    public $urltoken;
    public $exists;
    
    public $id;
    public $name;
    public $urlname;
    public $level;
    public $levelreq;
    public $rarity;
    public $itemclass;
    public $division;
    public $kingdom;
    public $domain = "loot";
    
    public $family;
    public $family_members = array();
    
    public $flags = array();
    public $props_magic = array();
    public $props_normal = array();
    public $props_set = array();
    public $props_set_family = array();
    
    public $similar = array();
    public $children = array();
    
    public $query = array();
}