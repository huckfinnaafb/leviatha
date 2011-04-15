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
                "check_exists" => "SELECT urlname FROM loot WHERE urlname = '{@urlname}'",
                "get_common" => "
                    SELECT name, urlname, level, levelreq, rarity, magic, class, division
                    FROM loot
                    LEFT JOIN relate_loot
                    ON loot.name = relate_loot.magic
                    WHERE (urlname = {@urlname})
                ",
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
                "get_family" => "SELECT `set_family` FROM relate_loot_set WHERE `set_item` = {@classEx}"
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
            
            // Check if item exists
            if (!$this->check_exists($item)) {
                return false;
            }
            
            // Property Collection
            $this->db_item["common"] = $this->get_common($item);
            
            // Magic Properties
            if ($this->db_item["common"]["rarity"] != "normal") {
                $this->db_item["props"]["normal"] = $this->get_normal($this->db_item["common"]["class"]);
                $this->db_item["props"]["magic"] = $this->get_magic($this->db_item["common"]["name"]);
                $this->db_similar = $this->get_similar(addslashes($this->db_item["common"]["name"]), addslashes($this->db_item["common"]["division"]), $this->db_item["common"]["level"]);
                
                // Set Properties
                if ($this->db_item["common"]["rarity"] == "set") {
                    $this->db_item["props"]["set"] = $this->get_set($this->db_item["common"]["name"]);
                    $this->db_family["family"] = $this->get_family($this->db_item["common"]["name"]);
                    $this->db_family["members"] = $this->get_family_members(addslashes($this->db_family["family"]));
                    $this->db_item["props"]["family"] = $this->get_family_props(addslashes($this->db_family["family"]));
                }
            // Normal Properties
            } else {
                $this->db_item["props"]["normal"] = $this->get_normal($this->db_item["common"]["name"]);
                $this->db_variants = $this->get_variants(addslashes($this->db_item["common"]["name"]));
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
        public function get_common($urlname = null) {
        
            // Set global var for use in framework query class
            F3::set('urlname', F3::get('DB.pdo')->quote($urlname));
            
            // Retrieve MySQL resource
            F3::sql($this->query["get_common"]);
            
            return F3::get("DB.result.0");
        }
        
        /* 
            Function: Get ["flags"] properties
            Returns: mysql resource
        */
        public function get_flags($item) {
            $query = "SELECT loot, flag, value FROM loot_flags WHERE `loot` = '$item'";
            return F3::sql($query);
        }
        
        /*
            Function: Get array of items similar in item level, within range
            Returns: mysql resource
        */
        public function get_similar($item, $relationship, $level) {
            $query = "
                SELECT loot.name, loot.urlname, loot.level, relate_loot.class
                FROM relate_loot
                    JOIN loot
                        ON loot.name = relate_loot.magic
                WHERE (relate_loot.division = '$relationship') AND (loot.name != '$item') AND (loot.level >= $level)
                ORDER BY loot.level ASC
                LIMIT 6
            ";
            return F3::sql($query);
        }
        
        /*
            Function: Get magical equivalents to normal items
            Returns: mysql resource
        */
        public function get_variants($item) {
            $query = "
                SELECT relate_loot_magic.magic AS name, loot.level, loot.urlname, relate_loot_magic.class AS parent
                FROM relate_loot_magic
                    JOIN loot
                        ON relate_loot_magic.magic = loot.name
                WHERE relate_loot_magic.class = '$item'
            ";
            return F3::sql($query);
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
        
        /*
            Function: Get all members of family
            Returns: mysql resource
        */
        public function get_family_members($family) {
            $query = "
                SELECT loot.name, loot.urlname, loot.level, loot.levelreq, relate_loot_magic.class
                FROM loot
                JOIN relate_loot_set
                    ON loot.name = relate_loot_set.set_item
                LEFT JOIN relate_loot_magic
                    ON loot.name = relate_loot_magic.magic
                WHERE `set_family` = '$family'
            ";
            return F3::sql($query);
        }
        
        /*
            Function: Get full and partial set bonuses
            Returns: mysql resource
        */
        public function get_family_props($family) {
            $query = "
                SELECT 
                    loot_properties_family.property, 
                    loot_properties_family.parameter, 
                    loot_properties_family.min, 
                    loot_properties_family.max,
                    loot_properties_family.req_equip,
                    translate_loot_properties.translation,
                    translate_loot_properties.translation_varies
                FROM loot_properties_family
                    JOIN translate_loot_properties
                        ON loot_properties_family.property = translate_loot_properties.property
                WHERE (loot_properties_family.set_family = '$family') AND (translate_loot_properties.display = 1) AND (req_equip > 0)
            ";
            $results = F3::sql($query);
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
