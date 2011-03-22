<?php
/**
 * Development scripts
 *
 * @author Sam
 */
class scripts extends base {

    // Strips `name` of whitespace and apostrophes
    public function tersename($table, $field) {
        $query = "
            SELECT *
            FROM '$table'.'$field'
        ";
        F3::sql($query);
        foreach (F3::get('DB.result')  as $row) {
            $row['name'] = strtolower($row['name']);
            $row['name'] = str_replace(" ", "-", $row['name']);
            $row['name'] = str_replace("'", "", $row['name']);
            
            foreach ($row as $value) {
                echo $value . ",";
            }
            echo "<br/>";
        }
    }

    public function listnames($table, $field) {
        $query = "
            SELECT name
            FROM loot_normal
        ";
        F3::sql($query);
        foreach (F3::get('DB.result')  as $row) {
            echo $row['name'] . "</br>";
        }
    }
}