<?php
    class script {
        public function integrity() {
        
            $query = "
                SELECT loot_properties.name
                FROM loot_properties
                    LEFT JOIN loot
                        ON loot_properties.name = loot.name
                WHERE loot.name IS NULL
            ";
            
            F3::sql($query);
            foreach(F3::get('DB.result') as $k => $v) {
                echo $v["name"] . "<br>";
            }
        }
        public function magic_parse() {
            $query = "
                SELECT *
                FROM raw_loot_properties_set_full
            ";
            F3::sql($query);
            $result = F3::get('DB.result');
            
            foreach($result as $k => $v) {
                
                $full = 0;
            
                for ($i = 1; $i <= 11; $i++) {
                    if (!empty($v['PCode' . $i . 'a'])) {
                        echo "\"" . $v["index"] . "\"\t\"" . $v['PCode' . $i . 'a'] . "\"\t\"" . $v['PParam' . $i . 'a'] . "\"\t" . $v['PMin' . $i . 'a'] . "\t" . $v['PMax' . $i . 'a'] . "\t" . $i . "<br>";
                        $full++;
                    }
                }
                for ($i = 1; $i <= 11; $i++) {
                    if (!empty($v['PCode' . $i . 'b'])) {
                        echo "\"" . $v["index"] . "\"\t\"" . $v['PCode' . $i . 'b'] . "\"\t\"" . $v['PParam' . $i . 'b'] . "\"\t" . $v['PMin' . $i . 'b'] . "\t" . $v['PMax' . $i . 'b'] . "\t" . $i . "<br>";
                        $full++;
                    }
                }
                for ($i = 1; $i <= 11; $i++) {
                    if (!empty($v['FCode' . $i])) {
                        echo "\"" . $v["index"] . "\"\t\"" . $v['FCode' . $i] . "\"\t\"" . $v['FParam' . $i] . "\"\t" . $v['FMin' . $i] . "\t" . $v['FMax' . $i] . "\t1337<br>";
                    }
                }
            }
        }
        public function relations() {
           
            // Select magic + class + division
            $division = "
                SELECT class, division
                FROM relate_loot_normal
            ";
            
            foreach(F3::sql($division) as $row) { 
                
                F3::set('class', F3::get('DB.pdo')->quote($row['class']));
                F3::set('division', F3::get('DB.pdo')->quote($row['division']));
                
                $query = "
                    UPDATE loot
                    SET division = {@division}
                    WHERE class = {@class}
                ";
                
                F3::sql($query);
            }
        }
        public function jsontest(){ 
            $leviatha = array (
                "name" => "Samuel Ferrell",
                "date" => "April 17th, 2009",
                "place" => "Tampa, FL"
            );
            $json = json_encode($leviatha);
            var_dump($json);
        }
    }