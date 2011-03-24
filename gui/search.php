<?php include(F3::get('GUI') . "/includes/header.php") ?>
<?php
    // Slice this bitch up
    $this->db_result_slice = array_slice($this->db_result, $this->offset, $this->limit);
?>
<!-- Notifications -->
<div class="module mod-notify mod-success">
    <p>Displaying (<span class="text-data"><?php echo $this->offset . " - " . ($this->offset + count($this->db_result_slice)); ?></span>) of a total of <span class="text-data"><?php echo count($this->db_result) ?></span> matches found for "<span class="text-data"><?php echo $this->input_raw ?></span>". Horray!</p>
</div>

<?php if (($this->page > 1) || ($this->distance > $this->limit)) { ?>
<!-- Pagination -->
<div class="module mod-pagination">
    <div class="line">
        <div class="unit size1of2">
            <?php if ($this->page > 1) { ?>
                <div style='text-align:left'><a href="/search?q=<?php echo $this->input_clean . "&page=" . ($this->page - 1) ?>">Previous Page</a></div>
            <?php } ?>
        </div>
        <div class="units size1of2 lastUnit">
            <?php if ($this->distance > $this->limit) { ?>
                <div style='text-align:right'><a href="/search?q=<?php echo $this->input_clean . "&page=" . ($this->page + 1) ?>">Next Page</a></div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>

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
            foreach ($this->db_result_slice as $row) {
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
