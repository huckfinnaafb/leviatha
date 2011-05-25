<?php
class SearchController extends RootController {
    
    public $title = "Search - Leviatha";
    
    public $scripts = array("jquery", "jquery.tablesorter.min", "leviatha");
    
    public function get() {
        
        if ($this->flag['search']) {
            
            // Fetch Search Query
            $query = $_GET['q'];
            $search = new SearchModel;
            
            // Fetch Search Results
            if ($this->results = $search->items($query)) {
                $this->render('search.php');
            } else {
                F3::set('EXCEPTION.warning', "Nothing found in the database. Try the <a href=\"/loot/directory/\">Loot Directory</a>.");
                $this->render('blank.php');
            }
        } else {
            F3::set('EXCEPTION.warning', "Search is temporarily disabled. Please try again later.");
        }
    }
}