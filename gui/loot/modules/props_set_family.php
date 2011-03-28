<div class="module mod-info">
    <h1 class="h-info">Family Set Bonuses</h1>
    <table class="table-info family">
        <tbody>
            <?php
                try {
                    if (isset($this->db_family["props"]) && (!empty($this->db_family["props"]))) {
                        foreach($this->db_family["props"] as $row) {
                            echo "<tr>";
                                echo "<td>{$row['translation']}</td>";
                                if (isset($row["parameter"])) 
                                    echo "<td>{$row['parameter']}</td>";
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
                                
                                if (count($this->db_family["members"]) == $row["req_equip"]) {
                                    echo "<td>( Full Set Bonus )</td>";
                                } else {
                                    echo "<td>( " . ($row["req_equip"]) . " Equipped )</td>";
                                }
                                
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
