<?php
    /*
        Item Array
        $this->db_item = array(
            ["common"] => array(
                ["name"] => string
                ["urlname"] => string
                ["level"] => integer
                ["levelreq"] => integer
                ["class"] => string
                ["division"] => string
            ),
            ["props"] => array(
                ["normal"] => array of properties and their translations
                ["magic"] => ...
                ["set"] => ...
                ["family"] => ...
            ),
            ["flags"] => array of item flags
        );
        
        $this->db_family = array(
            ["family"] => string
            ["members"] => array of strings
        );
    */
    
    /*
        Author: Samuel Ferrell
        Purpose: Query and Collate DB Results for Loot
    */
    class loot extends base {
        
        // Class Properties
        public $urlname;
        public $range = 10;
        
        // Query Array
        public $query = array();
        
        // Database Results
        public $db_item = array();
        public $db_variants = array();
        public $db_family = array();
        public $db_similar = array();
        
        /**
            Query array initialization
        **/
        public function __construct() {
            $this->query = array(
                "check_exists" => "SELECT urlname FROM loot WHERE urlname = {@urlname}",
                "get_common" => "
                    SELECT name, urlname, level, levelreq, rarity, class, division
                    FROM loot
                    WHERE urlname = {@urlname}
                ",
                "get_flags" => "SELECT loot, flag, value FROM loot_flags WHERE `loot` = {@item}",
                "get_normal" => "
                    SELECT loot_properties.property, loot_properties.min, translate_loot_properties.translation
                    FROM loot_properties 
                    JOIN translate_loot_properties
                    ON loot_properties.property = translate_loot_properties.property
                    WHERE (loot_properties.name = {@class}) AND (translate_loot_properties.display = 1)
                ",
                "get_magic" => "
                    SELECT loot_properties.property, loot_properties.parameter, loot_properties.min, loot_properties.max, translate_loot_properties.translation, translate_loot_properties.translation_varies
                    FROM loot_properties 
                    JOIN translate_loot_properties
                    ON loot_properties.property = translate_loot_properties.property
                    WHERE (loot_properties.name = {@classEx}) AND (translate_loot_properties.display = 1) AND (req_equip = 0)
                ",
                "get_set" => "
                    SELECT loot_properties.property, loot_properties.parameter, loot_properties.min, loot_properties.max, loot_properties.req_equip, translate_loot_properties.translation, translate_loot_properties.translation_varies 
                    FROM loot_properties 
                    JOIN translate_loot_properties
                    ON loot_properties.property = translate_loot_properties.property
                    WHERE (loot_properties.name = {@classEx}) AND (translate_loot_properties.display = 1) AND (req_equip > 0)
                ",
                "get_family" => "SELECT `set_family` FROM relate_loot_set WHERE `set_item` = {@classEx}",
                "get_family_members" => "
                    SELECT loot.name, loot.urlname, loot.level, loot.levelreq, relate_loot_magic.class
                    FROM loot
                    JOIN relate_loot_set
                    ON loot.name = relate_loot_set.set_item
                    LEFT JOIN relate_loot_magic
                    ON loot.name = relate_loot_magic.magic
                    WHERE `set_family` = {@family}
                ",
                "get_family_props" => "
                    SELECT loot_properties_family.property, loot_properties_family.parameter, loot_properties_family.min, loot_properties_family.max, loot_properties_family.req_equip, translate_loot_properties.translation, translate_loot_properties.translation_varies 
                    FROM loot_properties_family
                    JOIN translate_loot_properties
                    ON loot_properties_family.property = translate_loot_properties.property
                    WHERE (loot_properties_family.set_family = {@family}) AND (translate_loot_properties.display = 1) AND (req_equip > 0)
                ",
                "get_similar" => "
                    SELECT name, urlname, level, class, division
                    FROM loot
                    WHERE (division = {@relationship}) AND (name != {@item}) AND (level >= {@level}) AND (rarity != 'normal')
                    ORDER BY loot.level ASC
                    LIMIT 4
                ",
                "get_variants" => "
                    SELECT relate_loot_magic.magic AS name, loot.level, loot.urlname, relate_loot_magic.class AS parent
                    FROM relate_loot_magic
                    JOIN loot
                    ON relate_loot_magic.magic = loot.name
                    WHERE relate_loot_magic.class = {@item}
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
            $this->urlname = F3::scrub(F3::get('PARAMS.item'));
            
            // Check for integers
            if (strcspn($this->urlname, '0123456789') != strlen($this->urlname)) {
                $this->error = "Item name cannot include integers.";
            }
            
            // Check if empty
            if (!strlen($this->urlname)) {
                $this->error = "Item String Empty";
            }
            
            // Grab item data
            if (!$this->db_item = $this->get_item($this->urlname, true)) {
                $this->error = "No item found.";
            }
            
            // Include relevant item page template
            if (isset($this->error)) {
                F3::http404();
            } else {
                $this->title = $this->db_item["common"]["name"];
                if ($this->db_item["common"]["rarity"] == "unique") {
                    include (F3::get('GUI') . "loot/unique.php");
                    return true;
                } elseif ($this->db_item["common"]["rarity"] == "set") {
                    include (F3::get('GUI') . "loot/set.php");
                    return true;
                } elseif ($this->db_item["common"]["rarity"] == "normal") {
                    include (F3::get('GUI') . "loot/normal.php");
                    return true;
                }
            }
        }
        
        /**
            Collect relevant item data into $db_item
                @return $this->db_item, false on failure
                @param $item mixed, $is_url boolean
                @public
        **/
        public function get_item($item, $is_url = true) {
            
            // Initialize Framework PDO with blank query
            F3::sql('');
            
            // Check if Item Exists
            if (!$this->check_exists($item)) {
                return false;
            }
            
            // Property Collection
            $this->db_item["common"] = $this->get_common($item);
            
            // Magic Item
            if ($this->db_item["common"]["rarity"] != "normal") {
                $this->db_item["props"]["normal"] = $this->get_normal($this->db_item["common"]["class"]);
                $this->db_item["props"]["magic"] = $this->get_magic($this->db_item["common"]["name"]);
                
                // Retrieve Similar Items
                $this->db_similar = $this->get_similar($this->db_item["common"]["name"], $this->db_item["common"]["division"], $this->db_item["common"]["level"]);
                
                // Set Item
                if ($this->db_item["common"]["rarity"] == "set") {
                    $this->db_item["props"]["set"] = $this->get_set($this->db_item["common"]["name"]);
                    $this->db_family["family"] = $this->get_family($this->db_item["common"]["name"]);
                    $this->db_family["members"] = $this->get_family_members($this->db_family["family"]);
                    $this->db_item["props"]["family"] = $this->get_family_props($this->db_family["family"]);
                }
            // Normal Normal
            } else {
                $this->db_item["props"]["normal"] = $this->get_normal($this->db_item["common"]["name"]);
                
                // Retrieve Variant Items
                $this->db_variants = $this->get_variants($this->db_item["common"]["name"]);
            }
            
            return $this->db_item;
        }
        
        /**
            Check whether item exists in the database
                @returns boolean
                @public
        **/
        public function check_exists($urlname) {
            
            // Set global var for use in framework query class
            F3::set('urlname', F3::get('DB.pdo')->quote($urlname));
            
            return $i = (F3::sql($this->query["check_exists"])) ? true : false;
        }
        
        /**
            Retrieve Common Properties
                @returns array
                @param @url
        **/
        public function get_common($urlname) {
        
            // Set global var for use in framework query class
            F3::set('urlname', F3::get('DB.pdo')->quote($urlname));
            
            // Retrieve MySQL resource
            F3::sql($this->query["get_common"]);
            
            return F3::get("DB.result.0");
        }
        
        /**
            Retrieve Item Flags
                @return mysql array
                @param $item string
                @public
        **/
        public function get_flags($item) {
        
            // Set global var for use in framework query class
            F3::set('item', addslashes($item));
            
            return F3::sql($this->query["get_flags"]);
        }
        
        /**
            Retrieve Similar Items (close in proximity to level)
                @return mysql array
                @param $item string, $relationship string, $level integer
        **/
        public function get_similar($item, $relationship, $level) {
            
            // Set global var for use in framework query class
            F3::set('relationship', F3::get('DB.pdo')->quote($relationship));
            F3::set('item', F3::get('DB.pdo')->quote($item));
            F3::set('level', F3::get('DB.pdo')->quote($level));
            
            return F3::sql($this->query["get_similar"]);
        }
        
        /**
            Retrieve Variant Items (items that extend from base, or class, items
                @return mysql array
                @param $item string
                @public
        **/
        public function get_variants($item) {
            
            // Set global var for use in framework query class
            F3::set('item', F3::get('DB.pdo')->quote($item));
            
            return F3::sql($this->query["get_variants"]);
        }
        
        /**
            Retrieves Normal Properties (class item properties)
                @returns mysql array
                @param $class string
                @public
        **/
        public function get_normal($class) {
            
            // Set global var for use in framework query class
            F3::set('class', F3::get('DB.pdo')->quote($class));
            
            // Grab MySQL resource
            $results = F3::sql($this->query["get_normal"]);
            
            // Translations
            $i = 0;
            foreach($results as $row) {
                $results[$i]["translation"] = $this->translate_property($results[$i]["translation"]);
                $i++;
            }
            
            return $results;
        }
        
        /**
            Retrieve Item Magic Properties
                @return mysql array (translated)
                @param $classEx string
                @public
        **/
        public function get_magic($classEx) {
            
            // Set global var for use in framework query class
            F3::set('classEx', F3::get('DB.pdo')->quote($classEx));
            
            // Retrieve MySQL resource
            $results = F3::sql($this->query["get_magic"]);
            
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
            
            return $results;
        }
        
        /**
            Retrieve Item Set Properties
                @return mysql array (translated)
                @param $classEx string
                @public
        **/
        public function get_set($classEx) {
        
            // Set global var for use in framework query class
            F3::set('classEx', F3::get('DB.pdo')->quote($classEx));
            
            // Retrieve MySQL resource
            $results = F3::sql($this->query["get_set"]);
            
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
            
            return $results;
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
            Get Item Family Properties
                @return mysql array
                @param $classEx string
                @public
        **/
        public function get_family($classEx) {
            
            // Set global var for use in framework query class
            F3::set('classEx', F3::get('DB.pdo')->quote($classEx));
            
            // Retrieve MySQL resource
            F3::sql($this->query["get_family"]);
            
            return (F3::get('DB.result.0.set_family'));
        }
        
        /**
            Retrieve Family Members
                @return mysql array
                @param $family string
                @public
        **/
        public function get_family_members($family) {
        
            // Set global var for use in framework query class
            F3::set('family', F3::get('DB.pdo')->quote($family));
            
            // Retrieve MySQL resource
            return F3::sql($this->query["get_family_members"]);
        }
        
        /**
            Retrieve Family Properties
                @return mysql array
                @param $family string
                @public
        **/
        public function get_family_props($family) {
        
            // Set global var for use in framework query class
            F3::set('family', F3::get('DB.pdo')->quote($family));
            
            // Retrieve MySQL resource
            $results = F3::sql($this->query["get_family_props"]);
            
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
            
            return $results;
        }
    }
