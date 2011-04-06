<?php
    /*
        Author: Samuel Ferrell
        Purpose: Query and Collate DB Results for Loot
        GUI Include(s): /gui/loot.php, /gui/loot_magic_unique.php
    */
    
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
    class loot {
        public $urlname;
        public $range = 10;
        
        /* Database Results */
        public $db_item = array();
        public $db_variants = array();
        public $db_family = array();
        public $db_similar = array();
        
        /*
            Function: /loot?item= landing page
            Returns: void
        */
        public function init() {
            
            /* Check if item url is set. If not set, default to loot.php */
            if (!isset($_GET["item"])) {
                $this->title = "Loot Central";
                include (F3::get('GUI') . "loot.php");
                return false;
            } else {
                $this->urlname = $_GET["item"];
            }
            
            /* Scrub urlname */
            $this->urlname = F3::scrub($this->urlname);
            
            /* Check urlname against integers */
            if (strcspn($this->urlname, '0123456789') != strlen($this->urlname)) {
                $this->error = "Item name cannot include integers.";
            }
            
            /* Check if query is empty */
            if (!strlen($this->urlname)) {
                $this->error = "Item String Empty";
            }
            
            /* Grab Item Data */
            if (!$this->db_item = $this->get_item($this->urlname)) {
                $this->error = "No item found.";
            }
            
            /* If Error Thrown, Abort */
            if (isset($this->error)) {
                $this->title = "Error";
                include (F3::get('GUI') . "warning/error.php");
                return false;
            } else {
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
        
        /* 
            Function: Quick access to all available loot info
            Returns: array (see class docs), false on failure
        */
        public function get_item($item) {
            $item = addslashes($item);

            /* Check whether item exists */
            if (!$this->check_isExists($item)) {
                return false;
            }
            
            /* Property Collection */
            $this->db_item["common"] = $this->get_common($item);
            
            if ($this->db_item["common"]["rarity"] != "normal") {
                $this->db_item["props"]["normal"] = $this->get_normal(addslashes($this->db_item["common"]["class"]));
                $this->db_item["props"]["magic"] = $this->get_magic(addslashes($this->db_item["common"]["name"]));
                $this->db_similar = $this->get_similar(addslashes($this->db_item["common"]["name"]), addslashes($this->db_item["common"]["division"]), $this->db_item["common"]["level"]);
                
                if ($this->db_item["common"]["rarity"] == "set") {
                    $this->db_item["props"]["set"] = $this->get_set(addslashes($this->db_item["common"]["name"]));
                    $this->db_family["family"] = $this->get_family(addslashes($this->db_item["common"]["name"]));
                    $this->db_family["members"] = $this->get_family_members(addslashes($this->db_family["family"]));
                    $this->db_item["props"]["family"] = $this->get_family_props(addslashes($this->db_family["family"]));
                }
            } else {
                $this->db_item["props"]["normal"] = $this->get_normal(addslashes($this->db_item["common"]["name"]));
                $this->db_variants = $this->get_variants(addslashes($this->db_item["common"]["name"]));
            }
            
            return $this->db_item;
        }
        
        /* 
            Function: Check if item exists in the database
            Returns: boolean
        */
        public function check_isExists($item) {
            $query = "SELECT urlname FROM loot WHERE urlname = '$item'";
            if (F3::sql($query)) {
                return true;
            } else {
                return false;
            }
        }
        
        /* 
            Function: Get ["common"] properties
            Returns: mysql resource
        */
        public function get_common($urlname) {
            $query = "
                SELECT 
                    loot.name, 
                    loot.urlname, 
                    loot.level, 
                    loot.levelreq, 
                    loot.rarity, 
                    relate_loot.magic,
                    relate_loot.class,
                    relate_loot.division
                FROM loot
                    LEFT JOIN relate_loot
                        ON loot.name = relate_loot.magic
                WHERE `urlname` = '$urlname'
            ";
            F3::sql($query);
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
        
        /* 
            Function: Get ["prop_norm"] properties
            Returns: mysql resource
        */
        public function get_normal($item) {
            $query = "
                SELECT 
                    loot_properties.property,
                    loot_properties.min,
                    translate_loot_properties.translation
                FROM loot_properties 
                    JOIN translate_loot_properties
                        ON loot_properties.property = translate_loot_properties.property
                WHERE (loot_properties.name = '$item') AND (translate_loot_properties.display = 1)
            ";
            $results = F3::sql($query);
            $i = 0;
            foreach($results as $row) {
                $results[$i]["translation"] = $this->translate_property($results[$i]["translation"]);
                $i++;
            }
            return $results;
        }
        
        /* 
            Function: Get ["prop_magic"] properties
            Returns: mysql resource
        */
        public function get_magic($item) {
            $query = "
                SELECT 
                    loot_properties.property, 
                    loot_properties.parameter, 
                    loot_properties.min, 
                    loot_properties.max,
                    translate_loot_properties.translation,
                    translate_loot_properties.translation_varies
                FROM loot_properties 
                    JOIN translate_loot_properties
                        ON loot_properties.property = translate_loot_properties.property
                WHERE (loot_properties.name = '$item') AND (translate_loot_properties.display = 1) AND (req_equip = 0)
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
        
        /* 
            Function: Get ["prop_magic_set"] properties
            Returns: mysql resource
        */
        public function get_set($item) {
            $query = "
                SELECT 
                    loot_properties.property, 
                    loot_properties.parameter, 
                    loot_properties.min, 
                    loot_properties.max,
                    loot_properties.req_equip,
                    translate_loot_properties.translation,
                    translate_loot_properties.translation_varies
                FROM loot_properties 
                    JOIN translate_loot_properties
                        ON loot_properties.property = translate_loot_properties.property
                WHERE (loot_properties.name = '$item') AND (translate_loot_properties.display = 1) AND (req_equip > 0)
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
        
        /*
            Function: Property translations
            Returns: string
        */
        public function translate_property($string, $parameter = null, $min = null, $max = null) {
            $string = str_replace("@min", $min, $string);
            $string = str_replace("@max", $max, $string);
            $string = str_replace("@param", $parameter, $string);
            return $string;
        }
        
        /* 
            Function: Get set item family name
            Returns: string
        */
        public function get_family($item) {
            $query = "SELECT `set_family` FROM relate_loot_set WHERE `set_item` = '$item'";
            F3::sql($query);
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
