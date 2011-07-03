<?php
/**
    Author: Samuel Ferrell (huckfinnaafb@gmail.com)
    Purpose: Generate a JSON object of any item, including, but not limited to,
        item properties, related items, and statistical information.
**/
class LootModel extends RootModel {
    
    private $options = array(
        "properties" => true,
        "flags" => true,
        "spread" => 25,
        "count" => 6
    );
    
    private $query = array(
        "item" => 
            "
                SELECT 
                    loot.id, 
                    loot.name, 
                    loot.urlname, 
                    loot.level, 
                    loot.levelreq, 
                    loot.rarity, 
                    loot.grade,
                    loot.type,
                    loot.base,
                    loot_types.type AS parent,
                    loot_types.class
                FROM 
                    loot 
                JOIN loot_types ON loot.type = loot_types.code 
                WHERE urlname = :item
            ",
        "parent" =>
            "
                SELECT 
                    loot.id
                FROM loot
                WHERE code = :code
            ",
        "flags" => 
        "
            SELECT flag, value
            FROM loot_flags
            WHERE name = :item
        ",
        "properties" => 
            "
                SELECT * 
                FROM loot_normal 
                JOIN translate_loot_properties ON translate_loot_properties.property = loot_normal.property 
                WHERE name = :item AND value AND display = 1
            ",
        "properties_magic" => 
            "
                SELECT * 
                FROM loot_magic 
                JOIN translate_loot_properties ON translate_loot_properties.property = loot_magic.property 
                WHERE name = :item
            ",
        "properties_set" =>
            "
                SELECT *
                FROM loot_set_props
                JOIN translate_loot_properties ON translate_loot_properties.property = loot_set_props.property
                WHERE name = :item
            ",
        "family" => 
            "
                SELECT * 
                FROM loot_sets 
                WHERE name = :item
            ",
        "siblings" => 
            "
                SELECT loot.id, loot.urlname
                FROM loot
                JOIN loot_sets ON loot_sets.name = loot.name
                WHERE loot_sets.set = :family
            ",
        "similar" => 
            "
                SELECT 
                    loot.id, 
                    loot.urlname 
                FROM loot 
                WHERE 
                    (level >= :level) AND 
                    (type = :type) AND 
                    (rarity != 'normal') NOT IN 
                    (name = :item) 
                ORDER BY level ASC 
                LIMIT :limit
            ",
        "variants" => 
            "
                SELECT 
                    loot.id, 
                    loot.urlname 
                FROM loot 
                WHERE 
                    (class = :item) AND 
                    (rarity != 'normal') 
                ORDER BY level DESC
            ",
        "all" => 
            "
                SELECT 
                    loot.id, 
                    loot.name,
                    loot.urlname, 
                    loot.rarity, 
                    loot.grade, 
                    loot.level, 
                    loot.levelreq, 
                    loot.code,
                    loot.type,
                    loot.base,
                    loot_types.type AS parent,
                    loot_types.class
                FROM loot 
                JOIN loot_types ON loot.type = loot_types.code 
                ORDER BY rarity DESC, level DESC
            ",
        "types" => 
            "
                SELECT DISTINCT 
                    loot_types.type, 
                    loot_types.code, 
                    loot_types.kingdom 
                FROM loot_types 
                JOIN loot ON loot.type = loot_types.code 
                ORDER BY type
            "
    );
    
    public function item($identifier, $options = array()) {
    
        try {
        
            // Configurations
            $this->options = array_merge($this->options, $options);
            
            // Initialize Item Object
            $item = new ItemModel;
            
            // Fetch Shared Item Data
            F3::sqlBind($this->query['item'], array("item" => $identifier));
            $shared = F3::get('DB.result.0');
            
            // Presumably, no item data found
            if (empty($shared)) {
                throw new Exception("No item found.");
            }
            
            // Assign Class Attributes
            foreach($shared as $key => $attribute) {
                $item->$key = $attribute;
            }
            
            // Flag Collection
            if ($this->options['flags']) {
                $flags = F3::sqlBind($this->query['flags'], array("item" => $item->name));
                foreach($flags as $flag) {
                    $item->flags[$flag['flag']] = $flag['value'];
                }
            }
            
            // Property Collection
            if ($this->options['properties']) {
                switch ($item->rarity) {
                    case "normal" :
                        $item->properties['normal'] = F3::sqlBind($this->query['properties'], array("item" => $item->name));
                        break;
                    
                    case "unique" : 
                        $item->properties['magic'] = F3::sqlBind($this->query['properties_magic'], array("item" => $item->name));
                        break;
                    
                    case "set" :
                        $item->properties['magic'] = F3::sqlBind($this->query['properties_magic'], array("item" => $item->name));
                        $item->properties['set'] = F3::sqlBind($this->query['properties_set'], array("item" => $item->name));
                        break;
                        
                    default : 
                        throw new Exception("Unknown rarity.");
                }
                
                // Translate Item Properties
                $tokens = array("@param", "@min", "@max");
                
                foreach($item->properties as $type_key => $type) {
                    if ($type_key != 'normal') {
                        foreach($type as $row_key => $row) {
                            
                            // Determine which string to use
                            $field = ($row['minimum'] == $row['maximum']) ? "translation" : "translation_varies";
                            
                            // Order must line up with $tokens
                            $values = array($row['parameter'], $row['minimum'], $row['maximum']);
                            
                            // Replace database tokens with values
                            $item->properties[$type_key][$row_key]['translation'] = str_replace($tokens, $values, $row[$field]);
                            
                            // Unset unecessary string
                            unset($item->properties[$type_key][$row_key]['translation_varies']);
                        }
                    }
                }
            }
            
        } catch (Excecption $e) {
            error_log($e->getMessage());
            return false;
        }
        
        // Return JSON string
        return (json_encode($item));
    }
    
    public function family($family) {
    
    }
    
    public function similar($urlname) {
    
    }
    
    public function variants($urlname) {
    
    }
    
    public function siblings($urlname) {
    
    }
    
    public function all() {
        return F3::sqlBind($this->query['all']);
    }
    
    public function types() {
        return F3::sqlBind($this->query['types']);
    }
}