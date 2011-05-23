<h3>Normal Properties</h3>
<ul>
    <?php
        try {
            if (isset($this->props_normal) && (!empty($this->props_normal))) {
                foreach($this->props_normal as $row) {
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
