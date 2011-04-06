<?php include(F3::get('GUI') . "/includes/header.php") ?>
<div class="mod">
    <h1 class="h-mod h-page">Master Loot Directory</h1>
</div>
<div class="mod">
    <ul class="list-directory">
        <?php
            $query = "
                SELECT name, urlname
                FROM loot
            ";
            foreach(F3::sql($query) as $row) {
                echo "<li><a href='/loot?item={$row['urlname']}'>{$row['name']}</a></li>\n";
            }
        ?>
    </ul>
</div>
<?php include(F3::get('GUI') . "/includes/footer.php") ?>
