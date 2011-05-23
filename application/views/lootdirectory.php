<?php
$query_loot = "
    SELECT name, urlname, rarity, class, division
    FROM loot
    ORDER BY rarity DESC, level DESC
";
$query_relate = "
    SELECT division, kingdom
    FROM relate_division
";
$query_family = "
    SELECT name, urlname, rarity, set_family
    FROM relate_loot_set
    JOIN loot
    ON set_item = name
";
$query_family_strict = "
    SELECT DISTINCT set_family
    FROM relate_loot_set
";
$relate = F3::sqlBind($query_relate);
$loot = F3::sqlBind($query_loot);
$families = F3::sqlBind($query_family);
$families_strict = F3::sqlBind($query_family_strict);
?>
<div class="mod">
    <img src="/img/dungeon.jpg" style="width:100%">
</div>
<div class="mod">
    <h1 class="h-mod h-page">Master Loot Directory</h1>
</div>
<div class="mod mod-notify mod-tip">
    <p>Items are first sorted by their <span class="text-data">parent</span>, alphabetically, and then individually by their <span class="text-data">rarity</span> and finally their <span class="text-data">level</span>.</p>
</div>
<div class="mod mod-directory-header">
    <div class="line">
        <div class="unit size1of4"><h1 class="h-tab" style="margin-left:0">Weapons</h1></div>
        <div class="unit size1of4"><h1 class="h-tab">Armor</h1></div>
        <div class="unit size1of4"><h1 class="h-tab">Accessories</h1></div>
        <div class="unit size1of4 lastUnit"><h1 class="h-tab" style="margin-right:0">Families</h1></div>
    </div>
</div>
<div class="mod mod-directory">
    <div class="line">
        <div class="unit size1of4">
            <ul class="list-directory"><?php
                foreach ($relate as $division) {
                    if ($division['kingdom'] == 'weapon') {
                        echo "<li><h3>{$division['division']}</h3>\n";
                        echo "<ul class='list-directory'>\n";
                        foreach ($loot as $item) {
                            if ($item['division'] == $division['division']) {
                                echo "<li><span class='{$item['rarity']}'>&#9679;</span> <a class='link-lighter' href='/loot/{$item['urlname']}'>" . $item['name'] . "</a></li>\n";
                            }
                        }
                        echo "</ul></li>\n";
                    }
                }
                ?></ul>
        </div>
        <div class="unit size1of4">
            <ul class="list-directory"><?php
                foreach ($relate as $division) {
                    if ($division['kingdom'] == 'armor') {
                        echo "<li><h3>{$division['division']}</h3>\n";
                        echo "<ul class='list-directory'>\n";
                        foreach ($loot as $item) {
                            if ($item['division'] == $division['division']) {
                                echo "<li><span class='{$item['rarity']}'>&#9679;</span> <a class='link-lighter' href='/loot/{$item['urlname']}'>" . $item['name'] . "</a></li>\n";
                            }
                        }
                        echo "</ul></li>\n";
                    }
                }
                ?></ul>
        </div>
        <div class="unit size1of4">
            <ul class="list-directory"><?php
                foreach ($relate as $division) {
                    if ($division['kingdom'] == 'accessory') {
                        echo "<li><h3>{$division['division']}</h3>\n";
                        echo "<ul class='list-directory'>\n";
                        foreach ($loot as $item) {
                            if ($item['division'] == $division['division']) {
                                echo "<li><span class='{$item['rarity']}'>&#9679;</span> <a class='link-lighter' href='/loot/{$item['urlname']}'>" . $item['name'] . "</a></li>\n";
                            }
                        }
                        echo "</ul></li>\n";
                    }
                }
                ?></ul>
        </div>
        <div class="unit size1of4 lastUnit">
            <ul class="list-directory"><?php
                foreach($families_strict as $family) {
                    echo "<li><h3>{$family['set_family']}</h3>\n";
                    echo "<ul class='list-directory'>\n";
                    foreach($families as $set) {
                        if ($set['set_family'] == $family['set_family']) {
                            echo "<li><span class='{$set['rarity']}'>&#9679;</span> <a class='link-lighter' href='/loot/{$set['urlname']}'>" . $set['name'] . "</a></li>\n";
                        }
                    }
                    echo "</ul></li>\n";
                }
                ?></ul>
        </div>
    </div>
</div>