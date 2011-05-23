<?php include(F3::get('GUI') . "/includes/header.php") ?>
<?php
    // Slice this bitch up
    $this->db_result_slice = array_slice($this->db_result, $this->offset, $this->limit);
?>
<!-- Notifications -->
<div class="line">
    <div class="unit size1of2">
        <div class="mod mod-notify mod-success">
            <p><span class="text-data"><?php echo count($this->db_result) ?></span> matches found for "<span class="text-data"><?php echo $this->input_raw ?></span>". Horray!</p>
        </div>
    </div>
    <div class="unit size1of2 lastUnit">
        <div class="mod mod-notify mod-tip">
            <p>Results sorted by <span class="text-data"><?php echo $this->order; ?></span>, descending.</p>
        </div>
    </div>
</div>

<!-- Search Results -->
<div class="mod">
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
            foreach ($this->db_result_slice as $row) {
                echo ("
                <tr>
                    <td><a href='loot/{$row['urlname']}'>{$row['name']}</a></td>
                    <td>". ucwords($row['relationship']) ."</td>
                    <td>". ucwords($row['rarity']) ."</td>
                    <td>{$row['levelreq']}</td>
                    <td>{$row['level']}</td>
                </tr>");
            }
            ?>

        </tbody>
    </table>
</div>
<div class="mod" style="text-align:right">
    <?php
        $numPages = ceil($this->total / $this->limit);
        $radius = 5;
        $lowPos = $this->page - $radius;
        if ($lowPos <= 0) { $lowPos = 1; }
        $uppPos = $this->page + $radius;
        if ($uppPos > $numPages) {
            $uppPos = $numPages;
        }
        
        while ($lowPos < $this->page) {
            echo "<a href='/search?q={$this->input_clean}&page={$lowPos}' class='link-pagination'>{$lowPos}</a>";
            $lowPos++;
        }
         
        // Current
        echo "<span class='link-pagination-selected link-pagination'>{$this->page}</span>";
        
        // After
        $i = $this->page + 1;
        while ($i <= $uppPos) {
            echo "<a href='/search?q={$this->input_clean}&page={$i}' class='link-pagination'>{$i}</a>";
            $i++;
        }
        
    ?>
</div>
<?php include(F3::get('GUI') . "/includes/footer.php") ?>
