<div class="module mod-info">
    <h1 class="h-info">Magical Properties</h1>
    <ul class="list-iteminfo magic">
        <?php
            try {
                if (isset($this->db_item["prop_magic"]) && (!empty($this->db_item["prop_magic"]))) {
                    foreach($this->db_item["prop_magic"] as $row) {
                        echo "<li>{$row["translation"]}</li>\n";
                    }
                } else {
                    throw new Exception("No magic properties found.");
                }
            } catch (Exception $e) {
                echo "<div class='module mod-notify mod-warning'>" . $e->getMessage(), "</div>\n";
            }
        ?>
    </ul>
</div>
