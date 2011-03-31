<ul>
    <?php
        try {
            if (isset($this->db_item["prop_normal"]) && (!empty($this->db_item["prop_normal"]))) {
                foreach($this->db_item["prop_normal"] as $row) {
                    echo "<li>{$row['translation']}: {$row['value']}</li>\n";
                }
            } else {
                throw new Exception("No normal properties found.");
            }
        } catch (Exception $e) {
            echo "<div class='mod mod-notify mod-warning'>" . $e->getMessage(), "</div>\n";
        }
    ?>
</ul>
