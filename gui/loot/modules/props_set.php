<h3>Individual Set Bonuses</h3>
<ul class="list-iteminfo set">
    <?php
        try {
            if (isset($this->props_set) && (!empty($this->props_set))) {
                foreach($this->props_set as $row) {
                    echo "<li>{$row["translation"]} (" . ($row["req_equip"] + 1) . " Equipped)</li>\n";
                }
            } else {
                throw new Exception("No set bonuses found.");
            }
        } catch (Exception $e) {
            echo "<div class='mod mod-notify mod-warning' style='width:200px'>" . $e->getMessage(), "</div>\n";
        }
    ?>
</ul>