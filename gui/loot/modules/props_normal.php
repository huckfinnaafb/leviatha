<ul>
    <?php
        try {
            if (isset($this->db_item["props"]["normal"]) && (!empty($this->db_item["props"]["normal"]))) {
                foreach($this->db_item["props"]["normal"] as $row) {
                    echo "<li>{$row['translation']}: {$row['min']}</li>\n";
                }
            } else {
                throw new Exception("No normal properties found.");
            }
        } catch (Exception $e) {
            echo "<div class='mod mod-notify mod-warning'>" . $e->getMessage(), "</div>\n";
        }
    ?>
</ul>
