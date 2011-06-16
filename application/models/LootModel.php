<?php
/**
    Author: Samuel Ferrell (huckfinnaafb@gmail.com)
    Purpose: Generate a JSON object of any item, including, but not limited to,
        item properties, related items, and statistical information.
        
    Options:
        Verbose
            Boolean
            Default: True
            Toggles expensive property fetching
        
        Spread
            Integer
            Default: 25
            Number of levels above and below item to fetch for similar
            
        Count
            Integer
            Default: 6
            Number of items to fetch for similar
**/
class LootModel extends RootModel {
    
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
    
    // Loot Options
    public $options = array(
        "verbose" => true,
        "spread" => 25,
        "count" => 6
    );
    
    // SQL Query Array
    public $query = array(
        "item"              => "SELECT * FROM loot WHERE urlname = :item",
        "properties"        => "SELECT * FROM loot_properties JOIN translate_loot_properties ON translate_loot_properties.property = loot_properties.property WHERE name = :item AND req_equip = 0",
        "properties_set"    => "SELECT * FROM loot_properties JOIN translate_loot_properties ON translate_loot_properties.property = loot_properties.property WHERE name = :item AND req_equip > 0",
        "properties_family" => "SELECT * FROM loot_properties_family JOIN translate_loot_properties ON translate_loot_properties.property = loot_properties_family.property WHERE set_family = :family",
        "family"            => "SELECT * FROM relate_loot_set WHERE set_item = :item",
        "members"           => "SELECT loot.id, loot.urlname FROM relate_loot_set JOIN loot ON loot.name = relate_loot_set.set_item WHERE set_family = :family",
        "all"               => "SELECT * FROM loot ORDER BY rarity DESC",
        "similar"           => "SELECT loot.id, loot.urlname FROM loot WHERE (level > :level) AND (division = :division) AND (rarity != 'normal') NOT IN (name = :item) ORDER BY level DESC LIMIT 7",
        "variants"          => "SELECT loot.id, loot.urlname FROM loot WHERE (class = :item) AND (rarity != 'normal') ORDER BY level DESC LIMIT 7",
        "divisions"         => "SELECT * FROM relate_division"
    );
    
    /**
        Fetch All Relevant Item Data
            @param $identifier string
    **/
    public function item($identifier) {
        
        // Fetch Shared Item Data
        F3::sqlBind($this->query['item'], array("item" => $identifier));
        $shared = F3::get('DB.result.0');
        
        // Presumably, no item data found
        if (empty($shared)) {
            return false;
        }
        
        // Assign Class Attributes
        foreach($shared as $key => $attribute) {
            $this->$key = $attribute;
        }
        
        // Determine Item Parent
        $this->parent = (is_null($this->class)) ? $this->division : $this->class;
        
         // Fetch and translate item properties
        if ($this->options['verbose']) {
            switch ($this->rarity) {
                case "normal" :
                    $this->properties['normal'] = F3::sqlBind($this->query['properties'], array("item" => $this->name));
                    
                    break;
                case "unique" : 
                    $this->properties['normal'] = F3::sqlBind($this->query['properties'], array("item" => $this->class));
                    $this->properties['magic']  = F3::sqlBind($this->query['properties'], array("item" => $this->name));
                
                    break;
                case "set" : 
                    $this->properties['normal'] = F3::sqlBind($this->query['properties'], array("item" => $this->class));
                    $this->properties['magic']  = F3::sqlBind($this->query['properties'], array("item" => $this->name));
                    $this->properties['set']    = F3::sqlBind($this->query['properties_set'], array("item" => $this->name));
                    
                    $this->family = F3::sqlBind($this->query['family'], array("item" => $this->name));
                    
                    break;
            }
            
            // Translate Item Properties
            foreach($this->properties as $key => $property) {
                
                if (!empty($property)) {
                    foreach($property as $rowKey => $row) {
                    
                        if ($row['min'] == $row['max']) {
                            $this->properties[$key][$rowKey]['translation'] = $this->translate($row['translation'], $row['parameter'], $row['min'], $row['max']);
                        } else {
                            $this->properties[$key][$rowKey]['translation'] = $this->translate($row['translation_varies'], $row['parameter'], $row['min'], $row['max']);
                        }
                        
                        // Remove unused translation_varies
                        unset($this->properties[$key][$rowKey]['translation_varies']);
                    }
                }
                
            }
        }
        
        // Remove Query Property
        unset($this->query);
        
        // Return JSON string
        return (json_encode($this));
    }
    
    /**
        Translate item properties
            @return $translation string
            @param $translation string
            @param $parameter string
            @param $minimum int
            @param $maximum int
            @public
    **/
    public function translate($translation, $parameter, $minimum, $maximum) {
        $translation = str_replace("@param", $parameter, $translation);
        $translation = str_replace("@min", $minimum, $translation);
        $translation = str_replace("@max", $maximum, $translation);
        
        return $translation;
    }
    
    /**
        Fetch all items and their shared properties
            @public
    **/
    public function all() {
        return F3::sqlBind($this->query['all']);
    }
    
    /**
        Fetch division -> kingdom relationships
            @public
    **/
    public function relations() {
        return F3::sqlBind($this->query['divisions']);
    }
}