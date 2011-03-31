<?php include(F3::get('GUI') . "/includes/header.php") ?>
<div class="line">
    <div class="unit size2of3">
        <div class="mod mod-item">
            <div class="node">
                <h1 class="h-item set"><?php echo $this->db_item["common"]["name"] ?></h1>
                <p>Level <?php echo $this->db_item["common"]["level"] . " " . ucwords($this->db_item["common"]["rarity"]) . " <a href='/loot?item=" . $this->db_item["common"]["classurl"] . "'>" . ucwords($this->db_item["common"]["class"]) ?></a></p>
                <p>Required Level: <?php echo $this->db_item["common"]["levelreq"]; ?></p>
                <img class="img-item" src="/img/lena64.png">
            </div>
            <div class="node node-info">
                <!-- Normal Properties -->
                <?php include(F3::get('GUI') . "loot/modules/props_normal.php") ?>
            </div>
            <div class="node node-info">
                <!-- Magic Properties -->
                <?php include(F3::get('GUI') . "loot/modules/props_magic.php") ?>
            </div>
            <div class="node node-info">
                <?php include(F3::get('GUI') . "loot/modules/props_set.php") ?>
            </div>
            <div class="node node-info">
                <?php include(F3::get('GUI') . "loot/modules/props_set_family.php") ?>
            </div>
        </div>
    </div>
    
    <div class="unit size1of3 lastUnit">
        <!-- Set Family Members -->
        <?php include(F3::get('GUI') . "loot/modules/family.php") ?>
        <!-- Similar Items -->
        <?php include(F3::get('GUI') . "loot/modules/similar.php") ?>
    </div>
</div>

<!-- Footer -->
<?php include(F3::get('GUI') . "/includes/footer.php") ?>
