<?php
    /*
        Author: Samuel Ferrell
        Purpose: Query Database for Relevant Matches to Global Search Field
        GUI Include(s): /gui/search.php
        
        Todo:   
            Improve search relevancy
            Suggestions on search fail
                Search by division
                Search by kingdom
                Return google matches
    */
    class search {
        public $title;
        public $error;
        public $warning;
        
        /* User Input */
        public $input_raw;
        public $input_clean;
        
        /* Database Results & Properties */
        public $db_limit = 30;
        public $db_offset = 0;
        public $db_result;
        
        public function init() {
            
            /* Check if query is set */
            if (!isset($_GET["q"])) {
                $this->error = "Ack, nothing set!";
            } else {
                $this->input_raw = $_GET["q"];
            }
            
            /* Scrub user input. Assign result to input_clean */
            $this->input_clean = F3::scrub($this->input_raw);
            
            /* Check against integers in input */
            if (strcspn($this->input_clean, '0123456789') != strlen($this->input_clean)) {
                $this->error = "No integers allowed. Woops.";
            }
            
            /* Check if query is empty */
            if (!strlen($this->input_clean)) {
                $this->error = "You should type something in.";
            }
            
            /* Prepare query for DB use */
            $this->input_clean = $this->url_prep($this->input_clean);
            
            
            /* Query Loot */
            if (!isset($this->error)) {
                $this->db_result = $this->search_loot();
            }
            
            /* If Nothing Found & No Other Error Thrown */
            if (!count($this->db_result) && (!isset($this->error))) {
                $this->error = "Nothing was found. We're sorry!";
            }
            
            /* If Error Thrown */
            if (isset($this->error)) {
                $this->title = "Error";
                include (F3::get('GUI') . "warning/error.php");
                return false;
            }
            
            /* Single Result Redirect */
            if (count($this->db_result) == 1) {
                F3::reroute("/loot?item=" . $this->db_result[0]["urlname"]);
            }
            
            /* If Everything Goes Smoothly */
            $this->title = "Search: " . $this->input_raw;
            include (F3::get('GUI') . "search.php");
        }
        
        public function url_prep($string) {
            $string = htmlspecialchars($string);        /* Strip html chars */
            $string = mysql_escape_string($string);     /* Escape mysql chars */
            $string = str_replace(' ', '-', $string);   /* Replace whitespace with dashes */
            $string = str_replace("'", '', $string);    /* Remove single quotes */
            
            return $string;
        }
        
        public function search_loot() {
            $query = "
                (SELECT name, urlname, level, division AS parent, levelreq, rarity
                FROM loot
                WHERE urlname
                LIKE '%$this->input_clean%')
                UNION
                (SELECT name, urlname, level, class AS parent, levelreq, rarity
                FROM loot_magic
                WHERE urlname
                LIKE '%$this->input_clean%')
                ORDER BY level DESC
                    
            ";
            return F3::sql($query);
        }
    }
