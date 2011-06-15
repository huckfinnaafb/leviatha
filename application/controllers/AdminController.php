<?php
class AdminController extends RootController {
    
    public $title = "Admin Scripts - Diablo 2 Database";
    public $heading = "Admin Scripts";

    public function get() {
        $this->render('admin.php');
    }
}