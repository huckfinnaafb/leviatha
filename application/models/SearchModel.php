<?php
/**
    Author: Samuel Ferrell (huckfinnaafb@gmail.com)
    Purpose: Search the Database and return results
**/
class SearchModel extends RootModel {
    
    public $query = array(
        "items" => "
            SELECT 
                loot.name, 
                loot.urlname, 
                loot.rarity, 
                loot.level, 
                loot.levelreq, 
                loot_types.type AS relationship
            FROM loot
                JOIN loot_types ON (loot_types.code = loot.type)
            WHERE urlname LIKE :term
        "
    );
    
    public function items($term) {
    
        if ($term == '')
            return false;
        
        $term = $this->clean($term);
        
        // Query
        return DB::sql($this->query['items'], array('term' => $term)) ?: false;
    }
    
    public function clean($string) {
        return "%" . Web::slug(str_replace('\'', '', str_replace(' ', '-', $string))) . "%";
    }
}