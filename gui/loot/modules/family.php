<div class="module mod-itemlist">
    <h1 class="h-itemlist"><?php echo $this->db_family["family"] ?></h1>
    <?php
        try {
            if (!empty($this->db_family)) {
                foreach($this->db_family["members"] as $row) {
                    echo "
                        <a class='link-block' href='/loot?item={$row['urlname']}'>
                        <div class='node node-item node-{$row['rarity']}'>
                            <img class='img-itemthumb' src='/img/lena32.png'>
                            <h2 class='h-item'>{$row['name']}</h2>
                            <p class='text-info'>Level {$row['level']} {$row['class']}</p>
                        </div>
                    </a>
                    ";
                }
            } else {
                throw new Exception("No Family Members Found");
            }
        } catch (Exception $e) {
            echo "<p class='module mod-notify mod-warning'>" . $e->getMessage(), "</p>\n";
        }
    ?>
</div>
