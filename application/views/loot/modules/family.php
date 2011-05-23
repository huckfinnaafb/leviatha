<div class="mod mod-side mod-itemlist">
    <h1 class="h-side h-box"><?php echo $this->family ?></h1>
    <?php
        try {
            if (!empty($this->family)) {
                foreach($this->family_members as $row) {
                    echo "
                        <a class='link-block' href='/loot/{$row['urlname']}'>
                            <div class='node node-item'>
                                <img class='img-itemthumb' src='/img/lena32.png'>
                                <p>{$row['name']}</p>
                                <p class='text-info'>Level {$row['level']} {$row['class']}</p>
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
