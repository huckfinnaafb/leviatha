<div class="mod mod-side mod-itemlist">
    <h1 class="h-side h-box"><?php echo $this->db_family["family"] ?></h1>
    <?php
        try {
            if (!empty($this->db_family)) {
                foreach($this->db_family["members"] as $row) {
                    echo "
                        <a class='link-block' href='/loot?item={$row['urlname']}'>
                            <div class='node node-item'>
                                <img class='img-itemthumb' src='/img/lena32.png'>
                                <p>{$row['name']}</p>
                                <p class='text-info'>Level {$row['level']} {$row['relationship']}</p>
                            </div>
                        </a>
                    ";
                }
            } else {
                throw new Exception("No Family Members Found");
            }
        } catch (Exception $e) {
            echo "<p class='mod mod-notify mod-warning'>" . $e->getMessage(), "</p>\n";
        }
    ?>
</div>
