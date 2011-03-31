<h1>Individual Set Bonuses</h1>
<ul class="list-iteminfo set">
    <?php
        try {
            if (isset($this->db_item["prop_set"]) && (!empty($this->db_item["prop_set"]))) {
                foreach($this->db_item["prop_set"] as $row) {
                    echo "<li>{$row["translation"]} (" . ($row["req_equip"] + 1) . " Equipped)</li>\n";
                }
            } else {
                throw new Exception("No set bonuses found.");
            }
        } catch (Exception $e) {
            echo "<div class='mod mod-notify mod-warning'>" . $e->getMessage(), "</div>\n";
        }
    ?>
</ul>