<?php include(F3::get('GUI') . "/includes/header.php") ?>
<div class="mod">
    <h1 class="h-mod h-page">Master Loot Directory</h1>
</div>
<div class="line">
    <div class="unit size1of3">
        <div class="mod">
            <h2 class="h-section">Weapons</h2>
            <ul class="list-directory">
                <?php
                    $query = "
                        SELECT division
                        FROM relate_division
                        WHERE kingdom = 'weapon'
                    ";
                    F3::sql($query);
                    foreach(F3::get('DB.result') as $division) {
                        echo "<li>" . ucwords($division["division"]) . "<ul>";
                        $division = str_replace("'","\'", $division["division"]);
                        $query = "
                            SELECT name, urlname
                            FROM loot
                            WHERE division = '$division'
                            ORDER BY level DESC
                        ";
                        F3::sql($query);
                        foreach(F3::get('DB.result') as $class) {
                            echo "<li><a href='/loot?item={$class['urlname']}'>" . ucwords($class["name"]) . "</a><ul>";
                            $class = str_replace("'","\'", $class["name"]);
                            $query = "
                                SELECT name, urlname
                                FROM loot_magic
                                WHERE class = '$class'
                                ORDER BY level DESC
                            ";
                            F3::sql($query);
                            foreach(F3::get('DB.result') as $magic) {
                                echo "<li><a href='/loot?item={$magic['urlname']}'>" . ucwords($magic["name"]) . "</a></li>\n";
                            }
                            echo "</ul></li>";
                        }
                        echo "</ul></li>";
                    }
                ?>
            </ul>
        </div>
    </div>
    <div class="unit size1of3">
        <div class="mod">
            <h2 class="h-section">Armor</h2>
            <ul class="list-directory">
                <?php
                    $query = "
                        SELECT division
                        FROM relate_division
                        WHERE kingdom = 'armor'
                    ";
                    F3::sql($query);
                    foreach(F3::get('DB.result') as $division) {
                        echo "<li class='directory-list'>" . ucwords($division["division"]) . "<ul>";
                        $division = str_replace("'","\'", $division["division"]);
                        $query = "
                            SELECT name, urlname
                            FROM loot
                            WHERE division = '$division'
                            ORDER BY level DESC
                        ";
                        F3::sql($query);
                        foreach(F3::get('DB.result') as $class) {
                            echo "<li><a href='/loot?item={$class['urlname']}'>" . ucwords($class["name"]) . "</a><ul>";
                            $class = str_replace("'","\'", $class["name"]);
                            $query = "
                                SELECT name, urlname
                                FROM loot_magic
                                WHERE class = '$class'
                                ORDER BY level DESC
                            ";
                            F3::sql($query);
                            foreach(F3::get('DB.result') as $magic) {
                                echo "<li><a href='/loot?item={$magic['urlname']}'>" . ucwords($magic["name"]) . "</a></li>\n";
                            }
                            echo "</ul></li>";
                        }
                        echo "</ul></li>";
                    }
                ?>
            </ul>
        </div>
    </div>
    <div class="unit size1of3 lastUnit">
        <div class="mod">
            <h2 class="h-section">Accessories</h2>
            <ul class="list-directory" style="border-right: 0;">
                <?php
                    $query = "
                        SELECT division
                        FROM relate_division
                        WHERE kingdom = 'accessory'
                    ";
                    F3::sql($query);
                    foreach(F3::get('DB.result') as $division) {
                        echo "<li class='directory-list'>" . ucwords($division["division"]) . "<ul>";
                        $division = str_replace("'","\'", $division["division"]);
                        $query = "
                            SELECT name, urlname
                            FROM loot
                            WHERE division = '$division'
                            ORDER BY level DESC
                        ";
                        F3::sql($query);
                        foreach(F3::get('DB.result') as $class) {
                            echo "<li><a href='/loot?item={$class['urlname']}'>" . ucwords($class["name"]) . "</a><ul>";
                            $class = str_replace("'","\'", $class["name"]);
                            $query = "
                                SELECT name, urlname
                                FROM loot_magic
                                WHERE class = '$class'
                                ORDER BY level DESC
                            ";
                            F3::sql($query);
                            foreach(F3::get('DB.result') as $magic) {
                                echo "<li><a href='/loot?item={$magic['urlname']}'>" . ucwords($magic["name"]) . "</a></li>\n";
                            }
                            echo "</ul></li>";
                        }
                        echo "</ul></li>";
                    }
                ?>
            </ul>
        </div>
    </div>
</div>
    
<?php include(F3::get('GUI') . "/includes/footer.php") ?>
