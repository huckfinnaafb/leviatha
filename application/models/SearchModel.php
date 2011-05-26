<?php
/**
    Author: Samuel Ferrell
    Purpose: Search the Database and return results
**/
class SearchModel extends RootModel {
    
    public $query = array(
        "items" => "
            SELECT name, urlname, level, levelreq, rarity, COALESCE (class, division) AS relationship
            FROM loot
            WHERE urlname LIKE :term
        "
    );
    
    /**
        Loot Search - Leave pagination up to Javascript/JQuery
            @return mysql resource
            @param $query string
    **/
    public function items($term) {
    
        if ($term == '')
            return false;
        
        // URL Friendly Item Name
        $term = F3::slug($term);
        
        // Strip illegal characters
        $term = str_replace(array("'"), "", $term);
        
        // Replace spaces
        $term = str_replace(" ", "-", $term);
        
        // Wildcards
        $term = "%" . $term . "%";
        
        // Query
        return F3::sqlBind($this->query['items'], array('term' => $term)) ?: false;
    }
}