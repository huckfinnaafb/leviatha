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
    
    // Loot Options
    private $options = array(
        "verbose" => true,
        "spread" => 25,
        "count" => 6
    );
    
    // SQL Query Array
    protected $query = array(
        "item"              => "SELECT * FROM loot WHERE urlname = :item",
        "properties"        => "SELECT * FROM loot_properties JOIN translate_loot_properties ON translate_loot_properties.property = loot_properties.property WHERE name = :item AND req_equip = 0",
        "properties_set"    => "SELECT * FROM loot_properties JOIN translate_loot_properties ON translate_loot_properties.property = loot_properties.property WHERE name = :item AND req_equip > 0",
        "properties_family" => "SELECT * FROM loot_properties_family JOIN translate_loot_properties ON translate_loot_properties.property = loot_properties_family.property WHERE set_family = :family",
        "family"            => "SELECT * FROM relate_loot_set WHERE set_item = :item",
        "siblings"          => "SELECT loot.id, loot.urlname FROM relate_loot_set JOIN loot ON loot.name = relate_loot_set.set_item WHERE set_family = :family",
        "all"               => "SELECT * FROM loot ORDER BY rarity DESC",
        "similar"           => "SELECT loot.id, loot.urlname FROM loot WHERE (level > :level) AND (division = :division) AND (rarity != 'normal') NOT IN (name = :item) ORDER BY level DESC LIMIT 7",
        "variants"          => "SELECT loot.id, loot.urlname FROM loot WHERE (class = :item) AND (rarity != 'normal') ORDER BY level DESC LIMIT 7",
        "divisions"         => "SELECT * FROM relate_division"
    );
    
    public function __construct($options = array()) {
    
        // Set options
        $this->options = array_merge($this->options, $options);
    }
    
    /**
        Fetch All Relevant Item Data
            @return $this JSON Encoded Object
            @param $identifier string
            @public
    **/
    public function item($identifier, $options = array()) {
        
        // Set Options
        $this->options = array_merge($this->options, $options);
        
        // Item Object
        $item = new ItemModel;
        
        // Fetch Shared Item Data
        F3::sqlBind($this->query['item'], array("item" => $identifier));
        $shared = F3::get('DB.result.0');
        
        // Presumably, no item data found
        if (empty($shared)) {
            return false;
        }
        
        // Assign Class Attributes
        foreach($shared as $key => $attribute) {
            $item->$key = $attribute;
        }
        
        // Determine Item Parent
        $item->parent = (is_null($item->class)) ? $item->division : $item->class;
        
         // Fetch and translate item properties
        if ($this->options['verbose']) {
            switch ($item->rarity) {
                case "normal" :
                    $item->properties['normal'] = F3::sqlBind($this->query['properties'], array("item" => $item->name));
                    $item->variants = F3::sqlBind($this->query['variants'], array("item" => $item->name));
                    break;
                
                case "unique" : 
                    $item->properties['normal'] = F3::sqlBind($this->query['properties'], array("item" => $item->class));
                    $item->properties['magic']  = F3::sqlBind($this->query['properties'], array("item" => $item->name));
                    $item->similar = F3::sqlBind($this->query['similar'], array("item" => $item->name, "division" => $item->division, "level" => $item->level));
                    break;
                
                case "set" : 
                    $item->properties['normal'] = F3::sqlBind($this->query['properties'], array("item" => $item->class));
                    $item->properties['magic']  = F3::sqlBind($this->query['properties'], array("item" => $item->name));
                    $item->properties['set']    = F3::sqlBind($this->query['properties_set'], array("item" => $item->name));
                    
                    F3::sqlBind($this->query['family'], array("item" => $item->name));
                    $item->family = F3::get('DB.result.0.set_family');
                    
                    $item->siblings = F3::sqlBind($this->query['siblings'], array("family" => $item->family));
                    
                    break;
            }
            
            // Translate Item Properties
            foreach($item->properties as $key => $property) {
                
                if (!empty($property)) {
                    foreach($property as $rowKey => $row) {
                    
                        if ($row['min'] == $row['max']) {
                            $item->properties[$key][$rowKey]['translation'] = $this->translate($row['translation'], $row['parameter'], $row['min'], $row['max']);
                        } else {
                            $item->properties[$key][$rowKey]['translation'] = $this->translate($row['translation_varies'], $row['parameter'], $row['min'], $row['max']);
                        }
                        
                        // Remove unused translation_varies
                        unset($item->properties[$key][$rowKey]['translation_varies']);
                    }
                }
                
            }
        }
        
        // Return JSON string
        return (json_encode($item));
    }
    
    /**
        Translate item properties
            @return $translation string
            @param $translation string
            @param $parameter string
            @param $minimum int
            @param $maximum int
            @protected
    **/
    protected function translate($translation, $parameter, $minimum, $maximum) {
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