<?php include(F3::get('GUI') . "/includes/header.php") ?>
<div class="mod">
    <img src="/img/dungeon.jpg" style="width:100%">
</div>
<div class="mod">
    <h1 class="h-mod h-page">Master Loot Directory</h1>
</div>
<div class="mod" style="text-align:justify">
    <?php
        $query = "
            SELECT name, urlname
            FROM loot
            ORDER BY name
        ";
        foreach(F3::sql($query) as $row) {
            echo " <a href='/loot/{$row['urlname']}'>{$row['name']}</a> -\n";
        }
    ?>
</div>
<?php include(F3::get('GUI') . "/includes/footer.php") ?>
