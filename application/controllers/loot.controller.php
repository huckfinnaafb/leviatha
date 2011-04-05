<?php
    /*
        Author: Samuel Ferrell
        Purpose: Query and Collate DB Results for Loot
        GUI Include(s): /gui/loot.php, /gui/loot_magic_unique.php
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
            
            /* If Error Thrown */
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
        
            /* Check whether item exists */
            if (!$this->check_isExists($item)) {
                return false;
            }
            
            /* Property Collection */
            $this->db_item["common"] = $this->get_common($item);
            
            if ($this->db_item["common"]["rarity"] != "normal") {
                $parent = strtolower(str_replace(" ", "-", str_replace("'", "", $this->db_item["common"]["relationship"])));
                $this->db_item["props"]["normal"] = $this->get_normal($parent);
                $this->db_item["props"]["magic"] = $this->get_magic($item);
                $this->db_item["common"]["ancestors"] = $this->get_ancestors(addslashes($this->db_item["common"]["relationship"]));
                $this->db_similar = $this->get_similar($item, addslashes($this->db_item["common"]["ancestors"]["division"]), $this->db_item["common"]["level"]);
                
                if ($this->db_item["common"]["rarity"] == "set") {
                    $this->db_item["props"]["set"] = $this->get_set($item);
                    $this->db_family["family"] = $this->get_family($item);
                    $this->db_family["members"] = $this->get_family_members(addslashes($this->db_family["family"]));
                    $family = strtolower(str_replace(" ", "-", str_replace("'", "", $this->db_family["family"])));
                    $this->db_item["props"]["family"] = $this->get_family_props($family);
                }
            } else {
                $this->db_item["props"]["normal"] = $this->get_normal($item);
                $this->db_item["variants"] = $this->get_variants(addslashes($this->db_item["common"]["name"]));
                $this->db_item["common"]["ancestors"] = $this->get_ancestors(addslashes($this->db_item["common"]["name"]));
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
                SELECT name, urlname, relationship, level, levelreq, rarity
                FROM loot
                WHERE `urlname` = '$urlname'
            ";
            F3::sql($query);
            return F3::get("DB.result.0");
        }
        
        /* 
            Function: Get item ancestry
            Returns: mysql resource
        */
        public function get_ancestors($class) {
            $query = "
                SELECT 
                    loot.relationship AS division,
                    relate_division.kingdom
                FROM loot
                    JOIN relate_division
                        ON loot.relationship = relate_division.division
                WHERE name = '$class'
            ";
            F3::sql($query);
            return F3::get('DB.result.0');
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
            
        }
        
        /*
            Function: Get magical equivalents to normal items
            Returns: mysql resource
        */
        public function get_variants($item) {
            $query = "
                SELECT loot.name, loot.urlname, loot.level, loot.levelreq, loot.relationship, loot.rarity
                FROM loot
                WHERE loot.relationship = '$item'
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
                WHERE (loot_properties.name = '$item') AND (translate_loot_properties.display = 1) AND (req_equip IS NULL)
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
                SELECT loot.name, loot.urlname, loot.level, loot.levelreq, loot.relationship
                FROM loot
                JOIN relate_loot_set
                    ON loot.urlname = relate_loot_set.set_item
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
                WHERE (loot_properties_family.name = '$family') AND (translate_loot_properties.display = 1) AND (req_equip > 0)
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
