<h3>Magic Properties</h3>
<ul class="magic">
    <?php
        try {
            if (isset($this->props_magic) && (!empty($this->props_magic))) {
                foreach($this->props_magic as $row) {
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