<?php
class SandboxController extends RootController {
    
    public $title = "Sandbox - Diablo 2 Database";
    public $heading = "Sandbox";

    public function get() {
        $this->render('sandbox.php');
    }
    
    public function post() {
        $this->normalize_properties_normal("loot_misc_flags", "name");
    }
    
    public function basetotype() {
        $magic = F3::sql("SELECT * FROM loot WHERE (base != '')");
        
        foreach($magic as $item) {
        
            // Base
            $base = $item['base'];
            $id = $item['id'];
            
            // Fetch type
            F3::sql("SELECT type FROM loot WHERE code = '$base'");
            $type = F3::get('DB.result.0.type');
            
            echo $type . "<br>";
            
            F3::sql("UPDATE loot SET type = '$type' WHERE id = $id");
        }
    }
    
    /**
        Normalize Our Tables!
    **/
    public function normalize_properties($table, $identifier, $column, $keys = array()) {
        $all = F3::sql("SELECT * FROM $table");
        foreach($all as $row) {
            for($i = 1; $i <= count($row); $i++) {
                if (isset($row[$column.$i])) {
                    if ($row[$column.$i] != '') {
                        echo "\"" . $row[$identifier] . "\"";
                        foreach($keys as $key) {
                            if (isset($row[$key.$i])) {
                                echo ",\"" . $row[$key.$i] . "\"";
                            } else {
                                echo ",\"\"";
                            }
                        }
                        echo "<br>";
                    }
                }
            }
        }
    }
    
    public function normalize_properties_normal($table, $identifier) {
    
        $all = F3::sql("SELECT * FROM $table");
        foreach($all as $row) {
            $name = $row[$identifier];
            
            foreach($row as $key => $column) {
                echo "{$name},{$key},{$column}<br>";
            }
        }
    }
}