<?php include(F3::get('GUI') . "/includes/header.php") ?>

<!-- Notifications -->
<div class="line">
    <div class="unit size1of2">
        <div class="module mod-notify mod-success">
            <p><span class="text-data"><?php echo count($this->db_result) ?></span> matches found for "<span class="text-data"><?php echo $this->input_raw ?></span>". Horray!</p>
        </div>
    </div>
    <div class="unit size1of2 lastUnit">
        <div class="module mod-notify mod-tip">
            <p>Browse the library of items using the <a href="/loot" class="link">Master Loot Directory</a>.</p>
        </div>
    </div>
</div>
<?php
    /* Pagination Pseduo 
        
        FIRST PAGE STATES
            Results <= Limit
            Results > Limit
        N PAGE STATES
            Limit * (Page - 1) = Offset
        
    */
?>
<a href="/search?q=<?php echo $this->input_clean; ?>&page=<?php echo ($this->page + 1); ?>">Next Page</a><br>
<a href="/search?q=<?php echo $this->input_clean; ?>&page=<?php echo ($this->page - 1); ?>">Previous Page</a>
<!-- Search Results -->
<div class="module mod-search">
    <table class="table-search">
        <thead class="thead-search">
            <tr>
                <th class="theading-search">Name</th>
                <th class="theading-search">Parent</th>
                <th class="theading-search">Rarity</th>
                <th class="theading-search">Level Req.</th>
                <th class="theading-search">Item Level</th>
            </tr>
        </thead>
        <tbody class="tbody-search"><?php
            foreach ($this->db_result as $row) {
                echo ("
                <tr>
                    <td><a href='loot?item={$row['urlname']}'>{$row['name']}</a></td>
                    <td>". ucwords($row['parent']) ."</td>
                    <td>". ucwords($row['rarity']) ."</td>
                    <td>{$row['levelreq']}</td>
                    <td>{$row['level']}</td>
                </tr>");
            }
            ?>

        </tbody>
    </table>
</div>

<?php include(F3::get('GUI') . "/includes/footer.php") ?>
