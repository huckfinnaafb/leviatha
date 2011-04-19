<?php
    /*
        Author: Samuel Ferrell
        Purpose: Query and Collate DB Results for Loot
    */
    class loot extends base {
        
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
        public $variants = array();
        
        public $query;
        
        /**
            Query array initialization
        **/
        public function __construct() {
            $this->query = array(
                "check_exists" => "SELECT urlname FROM loot WHERE urlname = :urlname",
                "get_common" => "
                    SELECT id, name, urlname, level, levelreq, rarity, class, division
                    FROM loot
                    WHERE urlname = :urlname
                ",
                "get_flags" => "SELECT loot, flag, value FROM loot_flags WHERE `loot` = :item",
                "get_normal" => "
                    SELECT loot_properties.property, loot_properties.min, translate_loot_properties.translation
                    FROM loot_properties 
                    JOIN translate_loot_properties
                    ON loot_properties.property = translate_loot_properties.property
                    WHERE (loot_properties.name = :class) AND (translate_loot_properties.display = 1)
                ",
                "get_magic" => "
                    SELECT loot_properties.property, loot_properties.parameter, loot_properties.min, loot_properties.max, translate_loot_properties.translation, translate_loot_properties.translation_varies
                    FROM loot_properties 
                    JOIN translate_loot_properties
                    ON loot_properties.property = translate_loot_properties.property
                    WHERE (loot_properties.name = :classEx) AND (translate_loot_properties.display = 1) AND (req_equip = 0)
                ",
                "get_set" => "
                    SELECT loot_properties.property, loot_properties.parameter, loot_properties.min, loot_properties.max, loot_properties.req_equip, translate_loot_properties.translation, translate_loot_properties.translation_varies 
                    FROM loot_properties 
                    JOIN translate_loot_properties
                    ON loot_properties.property = translate_loot_properties.property
                    WHERE (loot_properties.name = :classEx) AND (translate_loot_properties.display = 1) AND (req_equip > 0)
                ",
                "get_family" => "SELECT `set_family` FROM relate_loot_set WHERE `set_item` = :classEx",
                "get_family_members" => "
                    SELECT name, urlname, level, class, division
                    FROM loot
                    JOIN relate_loot_set
                    ON loot.name = relate_loot_set.set_item
                    WHERE `set_family` = :family
                ",
                "get_family_props" => "
                    SELECT loot_properties_family.property, loot_properties_family.parameter, loot_properties_family.min, loot_properties_family.max, loot_properties_family.req_equip, translate_loot_properties.translation, translate_loot_properties.translation_varies 
                    FROM loot_properties_family
                    JOIN translate_loot_properties
                    ON loot_properties_family.property = translate_loot_properties.property
                    WHERE (loot_properties_family.set_family = :family) AND (translate_loot_properties.display = 1) AND (req_equip > 0)
                ",
                "get_similar" => "
                    SELECT name, urlname, level, class, division
                    FROM loot
                    WHERE (division = :relationship) AND (name != :item) AND (level >= :level) AND (rarity != 'normal')
                    ORDER BY loot.level ASC
                    LIMIT 4
                ",
                "get_variants" => "
                    SELECT name, urlname, level, class, division
                    FROM loot
                    WHERE loot.class = :item
                "
            );
        }
        
        /**
            Item landing page procedure
                @return boolean
                @param $PARAM['item'] mixed
                @public
        **/
        public function init() {
            
            // Collect route token
            $this->urltoken = F3::scrub(F3::get('PARAMS.item'));
            
            // Check for integers
            if (strcspn($this->urltoken, '0123456789') != strlen($this->urltoken)) {
                $this->error = "Item name cannot include integers.";
            }
            
            // Check if empty
            if (!strlen($this->urltoken)) {
                $this->error = "Item String Empty";
            }
            
            // If the token checks out, assume it's a valid item name
            if (!isset($this->error)) {
                $this->urlname = $this->urltoken;
            }
            
            // Collect Item Data
            $this->item($this->urlname, true);
            
            // If Item Doesn't Exist
            if (!$this->exists) {
                $this->error = "This item doesn't seem to exist.";
            }
            
            // Include relevant item page template
            if (isset($this->error)) {
                F3::http404();
            } else {
                $this->title = $this->name;
                
                switch($this->rarity) {
                    case ("unique"):
                        include (F3::get('GUI') . "loot/unique.php");
                        break;
                    case ("set"):
                        include (F3::get('GUI') . "loot/set.php");
                        break;
                    case ("normal"):
                        include (F3::get('GUI') . "loot/normal.php");
                        break;
                }
            }
        }
        
        /**
            Calls setter methods for item data collection
                @param $item mixed, $is_url boolean
                @public
        **/
        public function item($item, $is_url = true) {
            
            if (!$this->check_exists($item)) {return false;}
            
            // Set Common and Flags
            $this->set_common($item);
            $this->set_flags($this->name);
            
            // Set Properties and Meta Data
            if ($this->rarity != "normal") {
                $this->set_normal($this->itemclass);
                $this->set_magic($this->name);
                $this->set_similar($this->name, $this->division, $this->level);
                if ($this->rarity == "set") {
                    $this->set_set($this->name);
                    $this->set_family($this->name);
                    $this->set_family_members($this->family);
                    $this->set_props_family($this->family);
                }
            } elseif ($this->rarity == "normal") {
                $this->set_normal($this->name);
                $this->set_variants($this->name);
            }
        }
        
        /**
            Check whether item exists in the database
                @public
        **/
        public function check_exists($urlname) {
            return $this->exists = (F3::sqlBind($this->query["check_exists"], array('urlname' => $urlname))) ? true : false;
        }
        
        /**
            Sets Common Properties
                @param $urlname
                @public
        **/
        public function set_common($urlname) {
            F3::sqlBind($this->query["get_common"], array('urlname' => $urlname));
            $result = F3::get("DB.result.0");
            $this->id = $result["id"];
            $this->name = $result["name"];
            $this->urlname = $result["urlname"];
            $this->level = $result["level"];
            $this->levelreq = $result["levelreq"];
            $this->itemclass = $result["class"];
            $this->rarity = $result["rarity"];
            $this->division = $result["division"];
        }
        
        /**
            Set Item Flags
                @param $item string
                @public
        **/
        public function set_flags($item) {
            $this->flags = F3::sqlBind($this->query["get_flags"], array('item' => $item));
        }
        
        /**
            Set Similar Items (close in proximity to level)
                @param $item string, $relationship string, $level integer
        **/
        public function set_similar($item, $relationship, $level) {
            $this->similar = F3::sqlBind($this->query["get_similar"], array('relationship' => $relationship, 'item' => $item, 'level' => $level));
        }
        
        /**
            Set Variant Items (items that extend from base, or class, items
                @return mysql array
                @param $item string
                @public
        **/
        public function set_variants($item) {
            $this->variants = F3::sqlBind($this->query["get_variants"], array('item' => $item));
        }
        
        /**
            Set Normal Properties (class item properties)
                @param $class string
                @public
        **/
        public function set_normal($class) {
            
            // Grab MySQL resource
            $results = F3::sqlBind($this->query["get_normal"], array('class' => $class));
            
            // Translations
            $i = 0;
            foreach($results as $row) {
                $results[$i]["translation"] = $this->translate_property($results[$i]["translation"]);
                $i++;
            }
            
            $this->props_normal = $results;
        }
        
        /**
            Set Item Magic Properties
                @param $classEx string
                @public
        **/
        public function set_magic($classEx) {
            $results = F3::sqlBind($this->query["get_magic"], array('classEx' => $classEx));
            
            // Translations
            $i = 0;
            foreach($results as $row) {
                if (($results[$i]["min"]) == ($results[$i]["max"])) {
                    $results[$i]["translation"] = $this->translate_property($results[$i]["translation"], $results[$i]["parameter"], $results[$i]["min"], $results[$i]["max"]);
                } else {
                    $results[$i]["translation"] = $this->translate_property($results[$i]["translation_varies"], $results[$i]["parameter"], $results[$i]["min"], $results[$i]["max"]);
                }
                $i++;
            }
            
            $this->props_magic = $results;
        }
        
        /**
            Set Item Set Properties
                @param $classEx string
                @public
        **/
        public function set_set($classEx) {
            $results = F3::sqlBind($this->query["get_set"], array('classEx' => $classEx));
            
            // Translations
            $i = 0;
            foreach($results as $row) {
                if (($results[$i]["min"]) == ($results[$i]["max"])) {
                    $results[$i]["translation"] = $this->translate_property($results[$i]["translation"], $results[$i]["parameter"], $results[$i]["min"], $results[$i]["max"]);
                } else {
                    $results[$i]["translation"] = $this->translate_property($results[$i]["translation_varies"], $results[$i]["parameter"], $results[$i]["min"], $results[$i]["max"]);
                }
                $i++;
            }
            
            $this->props_set = $results;
        }
        
        /**
            Translate Database Property
                @return $string string
                @param $string string, $parameter mixed, $min integer, $max integer
                @public
        **/
        public function translate_property($string, $parameter = null, $min = null, $max = null) {
            $string = str_replace("@min", $min, $string);
            $string = str_replace("@max", $max, $string);
            $string = str_replace("@param", $parameter, $string);
            return $string;
        }
        
        /**
            Set Item Family Properties
                @param $classEx string
                @public
        **/
        public function set_family($classEx) {
            F3::sqlBind($this->query["get_family"], array('classEx' => $classEx));
            $this->family = (F3::get('DB.result.0.set_family'));
        }
        
        /**
            Set Family Members
                @param $family string
                @public
        **/
        public function set_family_members($family) {
            $this->family_members = F3::sqlBind($this->query["get_family_members"], array('family' => $family));
        }
        
        /**
            Set Family Properties
                @param $family string
                @public
        **/
        public function set_props_family($family) {
            $results = F3::sqlBind($this->query["get_family_props"], array('family' => $family));
            
            // Translations
            $i = 0;
            foreach($results as $row) {
                if (($results[$i]["min"]) == ($results[$i]["max"])) {
                    $results[$i]["translation"] = $this->translate_property($results[$i]["translation"], $results[$i]["parameter"], $results[$i]["min"], $results[$i]["max"]);
                } else {
                    $results[$i]["translation"] = $this->translate_property($results[$i]["translation_varies"], $results[$i]["parameter"], $results[$i]["min"], $results[$i]["max"]);
                }
                $i++;
            }
            
            $this->props_family = $results;
        }
    }
