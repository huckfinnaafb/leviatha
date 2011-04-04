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
    
    /* Search Config */
    public $limit = 50;
    public $page;
    public $offset;
    public $distance;
    public $lastpage;
    public $total;
    public $order = "level";
    
    /* User Input */
    public $input_raw;
    public $input_clean;
    
    /* Database Results */
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
            $this->db_result = $this->search_loot($this->input_clean, $this->order, $this->limit, $this->offset);
        }
        
        /* Pagination */
        if (isset($_GET["page"])) {
            if (!is_numeric($_GET["page"])) {
                $this->error = "Page data is not numeric.";
            }
            $this->page = $_GET["page"];
            if ($this->page > (ceil(count($this->db_result) / $this->limit))) {
                $this->error = "Bad page.";
            }
        } else {
            $this->page = 1;
        }
        $this->total = count($this->db_result);
        $this->offset = ($this->page - 1) * $this->limit;
        $this->distance = ($this->total - $this->offset);
        
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
    
    public function search_loot($term, $order = "level", $limit = 30, $offset = 0) {
        $query = "
            SELECT name, urlname, level, relationship, levelreq, rarity
            FROM loot
            WHERE urlname
            LIKE '%$this->input_clean%'
            ORDER BY `$order` DESC
        ";
        return F3::sql($query);
    }
}
