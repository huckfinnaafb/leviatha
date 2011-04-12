<h3>Magic Properties</h3>
<ul class="magic">
    <?php
        try {
            if (isset($this->db_item["props"]["magic"]) && (!empty($this->db_item["props"]["magic"]))) {
                foreach($this->db_item["props"]["magic"] as $row) {
                    echo "<li>{$row["translation"]}</li>\n";
                }
            } else {
                throw new Exception("No magic properties found.");
            }
        } catch (Exception $e) {
            echo "<div class='mod mod-notify mod-warning' style='width:200px'>" . $e->getMessage(), "</div>\n";
        }
    ?>
</ul>