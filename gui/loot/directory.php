<?php include(F3::get('GUI') . "/includes/header.php") ?>
<div class="mod">
    <h1 class="h-mod h-page">Master Loot Directory</h1>
</div>
<div class="line">
    <div class="unit size1of3">
        <div class="mod">
            <ul class="list-directory">
                <li>Weapons
                <?php
                    $query = "
                        SELECT division
                        FROM relate_division
                        WHERE kingdom = 'weapon'
                    ";
                    ?><ul class="list-directory"><?php
                    foreach(F3::sql($query) as $row) {
                        echo "<li> " . ucwords($row['division']);
                        $division = $row['division'];
                        $query = "
                            SELECT name, urlname, relationship
                            FROM loot
                            WHERE relationship = '$division'
                        ";
                        ?><ul class="list-directory"><?php
                            foreach(F3::sql($query) as $row) {
                                echo "<li><a href='/loot?item={$row['urlname']}'>" . ucwords($row['name']) . "</a>";
                                $class = addslashes($row['name']);
                                $query = "
                                    SELECT name, urlname
                                    FROM loot
                                    WHERE relationship = '$class'
                                ";
                                ?><ul class="list-directory"><?php
                                    foreach(F3::sql($query) as $row) {
                                        echo "<li><a href='/loot?item={$row['urlname']}'>" . ucwords($row['name']) . "</a></li>";
                                    }
                                ?></ul><?php
                                echo "</li>";
                            }
                        ?></ul><?php
                        echo "</li>";
                    }
                    ?></ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="unit size1of3">
        <div class="mod">
            <ul class="list-directory">
                <li>Armor
                <?php
                    $query = "
                        SELECT division
                        FROM relate_division
                        WHERE kingdom = 'armor'
                    ";
                    ?><ul class="list-directory"><?php
                    foreach(F3::sql($query) as $row) {
                        echo "<li> " . ucwords($row['division']);
                        $division = $row['division'];
                        $query = "
                            SELECT name, urlname, relationship
                            FROM loot
                            WHERE relationship = '$division'
                        ";
                        ?><ul class="list-directory"><?php
                            foreach(F3::sql($query) as $row) {
                                echo "<li><a href='/loot?item={$row['urlname']}'>" . ucwords($row['name']) . "</a>";
                                $class = addslashes($row['name']);
                                $query = "
                                    SELECT name, urlname
                                    FROM loot
                                    WHERE relationship = '$class'
                                ";
                                ?><ul class="list-directory"><?php
                                    foreach(F3::sql($query) as $row) {
                                        echo "<li><a href='/loot?item={$row['urlname']}'>" . ucwords($row['name']) . "</a></li>";
                                    }
                                ?></ul><?php
                                echo "</li>";
                            }
                        ?></ul><?php
                        echo "</li>";
                    }
                    ?></ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="unit size1of3">
        <div class="mod">
            <ul class="list-directory">
                <li>Accessories
                <?php
                    $query = "
                        SELECT division
                        FROM relate_division
                        WHERE kingdom = 'accessory'
                    ";
                    ?><ul class="list-directory"><?php
                    foreach(F3::sql($query) as $row) {
                        echo "<li> " . ucwords($row['division']);
                        $division = $row['division'];
                        $query = "
                            SELECT name, urlname, relationship
                            FROM loot
                            WHERE relationship = '$division'
                        ";
                        ?><ul class="list-directory"><?php
                            foreach(F3::sql($query) as $row) {
                                echo "<li><a href='/loot?item={$row['urlname']}'>" . ucwords($row['name']) . "</a>";
                                $class = addslashes($row['name']);
                                $query = "
                                    SELECT name, urlname
                                    FROM loot
                                    WHERE relationship = '$class'
                                ";
                                ?><ul class="list-directory"><?php
                                    foreach(F3::sql($query) as $row) {
                                        echo "<li><a href='/loot?item={$row['urlname']}'>" . ucwords($row['name']) . "</a></li>";
                                    }
                                ?></ul><?php
                                echo "</li>";
                            }
                        ?></ul><?php
                        echo "</li>";
                    }
                    ?></ul>
                </li>
            </ul>
        </div>
    </div>
</div>

    
<?php include(F3::get('GUI') . "/includes/footer.php") ?>
