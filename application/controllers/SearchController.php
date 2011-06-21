<?php
class SearchController extends RootController {
    
    public $title = "Search - Diablo 2 Database";
    public $scripts = array("jquery", "jquery.tablesorter.min", "jquery.tablesorter.pager", "leviatha");
    public $redirect = true;
    
    public function get() {
        
        if ($this->flag['search']) {
            
            if (isset($_GET['q']) && ($_GET['q'] != '')) { 
                
                // Fetch Search Query
                $query = F3::scrub($_GET['q']);
                $search = new SearchModel;
                
                // Fetch Search Results
                if ($this->results = $search->items($query)) {
                
                    // Reroute if one match
                    if (count($this->results) == 1 && $this->redirect) {
                        F3::reroute('/loot/' . $this->results[0]['urlname']);
                    }
                    
                    $this->title = "Search: \"" . $query . "\" - Diablo 2 Database";
                    $this->heading = "Search: \"" . $query . "\"";
                    $this->render('search.php');
                } else {
                    $this->heading = "Search: \"" . $query . "\"";
                    F3::set('NOTIFY.warning', "Nothing found in the database. Try the <a href=/loot/>Loot Directory</a>.");
                    $this->render('blank.php');
                }
            } else {
                F3::reroute('/');
            }
        } else {
            F3::set('NOTIFY.warning', "Search is temporarily disabled. Please try again later.");
            $this->render('blank.php');
        }
    }
}