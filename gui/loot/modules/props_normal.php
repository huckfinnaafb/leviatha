<h3>Normal Properties</h3>
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
            echo "<div class='mod mod-notify mod-warning' style='width:200px'>" . $e->getMessage(), "</div>\n";
        }
    ?>
</ul>
