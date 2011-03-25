<div class="module mod-info">    
    <h1 class="h-info">Individual Set Bonuses</h1>
    <table class="table-info">
        <tbody>
            <?php
                try {
                    if (isset($this->db_item["prop_set"]) && (!empty($this->db_item["prop_set"]))) {
                        foreach($this->db_item["prop_set"] as $row) {
                            echo "<tr class='set'>";
                                echo "<td>{$row['translation']}</td>";
                                if (isset($row["param"])) 
                                    echo "<td>{$row['param']}</td>";
                                else
                                    echo "<td></td>";
                                
                                if (isset($row["min"]) && isset($row["max"])) {
                                    if ($row["min"] == $row["max"]) {
                                        echo "<td>{$row["min"]}</td>";
                                    } else {
                                        echo "<td>{$row["min"]} - {$row["max"]}</td>";
                                    }
                                } else {
                                    echo "<td></td>";
                                }
                                
                                echo "<td>( " . ($row["req_equip"] + 1) . " Equipped )</td>";
                                
                            echo "</tr>";
                        }
                    } else {
                        throw new Exception("No set bonuses found.");
                    }
                } catch (Exception $e) {
                    echo "<p class='module mod-notify mod-warning'>" . $e->getMessage(), "</p>\n";
                }
            ?>
        </tbody>
    </table>
</div>
