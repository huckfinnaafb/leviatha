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
        
        /* Boolean Properties */
        public $isMagic;
        
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
                throw new Exception("Item name cannot include integers.");
            }
            
            /* Check if query is empty */
            if (!strlen($this->urlname)) {
                throw new Exception("Item String Empty");
            }
            
            /* Collect item info through get_item() and include appropriate template */
            if ($this->db_item = $this->get_item($this->urlname)) {
                $this->title = $this->db_item["common"]["name"];
                if ($this->isMagic) {
                    /* Open Appropriate File Depending on Rarity of Item */
                    switch ($this->db_item["common"]["rarity"]) {
                        case ("unique"): include (F3::get('GUI') . "loot/unique.php"); break;
                        case ("set"): include (F3::get('GUI') . "loot/set.php"); break;
                        case ("runeword"): include (F3::get('GUI') . "loot/runeword.php"); break;
                    }
                } else {
                    /* Base Item File */
                    include (F3::get('GUI') . "loot/normal.php");
                }
            } else {
                throw new Exception("Item data not found.");
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
            
            /* Check whether item is magic or not */
            $this->isMagic = $this->check_isMagic($item);
            
            /* Property & Flag Collection */
            if ($this->isMagic) {
                $this->db_item["common"] = $this->get_common($item, true);
                $this->db_item["common"]["classurl"] = strtolower(str_replace(" ", "-", str_replace("'", "", $this->db_item["common"]["class"])));
                $this->db_item["prop_normal"] = $this->get_normal(str_replace("'", "\'", $this->db_item["common"]["class"]));
                $this->db_item["prop_magic"] = $this->get_magic(str_replace("'", "\'", $this->db_item["common"]["name"]));
                $this->db_item["flags"] = $this->get_flags(str_replace("'", "\'", $this->db_item["common"]["name"]));
                
                $this->db_similar = $this->get_similar(str_replace("'", "\'", $this->db_item["common"]["name"]), str_replace("'", "\'", $this->db_item["common"]["division"]), $this->db_item["common"]["level"], $this->range);
                
                /* Set Item Bonus Collection */
                if ($this->db_item["common"]["rarity"] == "set") {
                    $this->db_item["prop_set"] = $this->get_set(str_replace("'", "\'", $this->db_item["common"]["name"]));
                    $this->db_family["family"] = $this->get_family(str_replace("'", "\'", $this->db_item["common"]["name"]));
                    $this->db_family["members"] = $this->get_family_members(str_replace("'", "\'", $this->db_family["family"]));
                    $this->db_family["props"] = $this->get_family_props(str_replace("'", "\'", $this->db_family["family"]));
                }
            } else {
                $this->db_item["common"] = $this->get_common($item, false);
                $this->db_item["prop_normal"] = $this->get_normal(str_replace("'", "\'", $this->db_item["common"]["name"]));
                $this->db_item["flags"] = $this->get_flags(str_replace("'", "\'", $this->db_item["common"]["name"]));
                $this->db_variants = $this->get_variants(str_replace("'", "\'", $this->db_item["common"]["name"]));
            }
            return $this->db_item;
        }
        
        /* 
            Function: Check if item exists in the database
            Returns: boolean
        */
        public function check_isExists($item) {
            $query = "
                (SELECT urlname FROM loot WHERE urlname = '$item')
                UNION
                (SELECT urlname FROM loot_magic WHERE urlname = '$item')
            ";
            if (F3::sql($query)) {
                return true;
            } else {
                return false;
            }
        }
        
        /* 
            Function: Check if item is magic
            Returns: boolean
        */
        public function check_isMagic($urlname) {
            $query = "SELECT urlname FROM loot_magic WHERE loot_magic.urlname = '$urlname'";
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
        public function get_common($urlname, $isMagic = null) {
            
            /* if isMagic is null, attempt to grab class property */
            if (($isMagic === null) && (isset($this->isMagic))) {
                $isMagic = $this->isMagic;
            }
            
            if ($isMagic) {
                $query = "
                    SELECT loot_magic.name, loot_magic.urlname, loot_magic.level, loot_magic.levelreq, loot_magic.class, loot_magic.rarity, loot.division, relate_division.kingdom, relate_kingdom.domain
                    FROM loot
                        JOIN loot_magic 
                            ON loot_magic.class = loot.name
                        JOIN relate_division 
                            ON loot.division = relate_division.division
                        JOIN relate_kingdom 
                            ON relate_division.kingdom = relate_kingdom.kingdom
                        JOIN relate_domain 
                            ON relate_kingdom.domain = relate_domain.domain
                    WHERE loot_magic.urlname = '$urlname'
                ";
                F3::sql($query);
                return F3::get('DB.result.0');
            } else {
                $query = "
                    SELECT loot.name, loot.urlname, loot.level, loot.levelreq, loot.division, relate_division.kingdom, relate_kingdom.domain
                    FROM loot
                        JOIN relate_division 
                            ON loot.division = relate_division.division
                        JOIN relate_kingdom 
                            ON relate_division.kingdom = relate_kingdom.kingdom
                        JOIN relate_domain 
                            ON relate_kingdom.domain = relate_domain.domain
                    WHERE loot.urlname = '$urlname'
                ";
                F3::sql($query);
                return F3::get('DB.result.0');
            }
        }
    
        /* 
            Function: Get ["prop_norm"] properties
            Returns: mysql resource
        */
        public function get_normal($item) {
            $query = "
                SELECT 
                    translate_loot_properties.translation, 
                    loot_properties.value
                FROM loot_properties 
                    JOIN translate_loot_properties
                        ON loot_properties.property = translate_loot_properties.property
                WHERE (`name` = '$item') AND (translate_loot_properties.display = 1)
            ";
            return F3::sql($query);
        }
        
        /* 
            Function: Get ["prop_magic"] properties
            Returns: mysql resource
        */
        public function get_magic($item) {
            $query = "
                SELECT 
                    loot_properties_magic.loot, 
                    loot_properties_magic.property, 
                    loot_properties_magic.parameter, 
                    loot_properties_magic.min, 
                    loot_properties_magic.max,
                    translate_loot_properties.translation,
                    translate_loot_properties.translation_varies
                FROM loot_properties_magic 
                    JOIN translate_loot_properties
                        ON loot_properties_magic.property = translate_loot_properties.property
                WHERE (`loot` = '$item') AND (translate_loot_properties.display = 1)
            ";
            $results = F3::sql($query);
            $i = 0;
            foreach($results as $row) {
                if (true) {
                    $results[$i]["translation"] = $this->translate_property($results[$i]["translation"], $results[$i]["parameter"], $results[$i]["min"], $results[$i]["max"]);
                } else {
                    $results[$i]["translation"] = $this->translate_property($results[$i]["translation_varies"], $results[$i]["parameter"], $results[$i]["min"], $results[$i]["max"]);
                }
                $i++;
            }
            return $results;
        }
        
        public function translate_property($string, $parameter = null, $min = null, $max = null) {
            $string = str_replace("@min", $min, $string);
            $string = str_replace("@max", $max, $string);
            $string = str_replace("@parameter", $parameter, $string);
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
                SELECT loot_magic.name, loot_magic.urlname, loot_magic.class, loot_magic.level, loot_magic.rarity
                FROM loot_magic
                    JOIN relate_loot_set
                        ON loot_magic.name = relate_loot_set.set_item
                WHERE `set_family` = '$family'";
            return F3::sql($query);
        }
        
        /*
            Function: Get full and partial set bonuses
            Returns: mysql resource
        */
        public function get_family_props($family) {
            $query = "
                SELECT 
                    loot_properties_set_full.parameter, 
                    loot_properties_set_full.min, 
                    loot_properties_set_full.max,
                    loot_properties_set_full.req_equip,
                    translate_loot_properties.translation
                FROM loot_properties_set_full 
                    JOIN translate_loot_properties
                        ON loot_properties_set_full.property = translate_loot_properties.property
                WHERE (set_family = '$family') AND (translate_loot_properties.display = 1)
                ORDER BY req_equip ASC
            ";
            return F3::sql($query);
        }
        
        /* 
            Function: Get ["prop_magic_set"] properties
            Returns: mysql resource
        */
        public function get_set($item) {
            $query = "
                SELECT 
                    loot_properties_set.set_item, 
                    loot_properties_set.parameter, 
                    loot_properties_set.min, 
                    loot_properties_set.max,
                    loot_properties_set.req_equip,
                    translate_loot_properties.translation
                FROM loot_properties_set 
                    JOIN translate_loot_properties
                        ON loot_properties_set.property = translate_loot_properties.property
                WHERE (`set_item` = '$item') AND (translate_loot_properties.display = 1)
                ORDER BY req_equip ASC
            ";
            return F3::sql($query);
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
        public function get_similar($item, $division, $level) {
            $query = "
                SELECT 
                    loot_magic.name,
                    loot_magic.urlname,
                    loot_magic.level,
                    loot_magic.levelreq,
                    loot_magic.rarity,
                    loot.name AS classname
                FROM loot_magic
                    JOIN loot
                        ON loot_magic.class = loot.name
                WHERE (loot_magic.level > '$level') 
                    AND (loot.division = '$division')
                    NOT IN (loot_magic.name = '$item')
                ORDER BY loot_magic.level ASC
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
                SELECT name, urlname, level, class, rarity
                FROM loot_magic
                WHERE `class` = '$item'
            ";
            return F3::sql($query);
        }
    }
