<?php include(F3::get('GUI') . "/includes/header.php") ?>
<div class="line">
    <div class="unit size2of3">
        <div class="mod mod-item">
            <h1 class="h-item" style="color:#00c400"><?php echo $this->db_item["common"]["name"] ?></h1>
            <div class="node">
                <img class="img-item" src="/img/stormshield.png" style="float:left;margin:0 12px 12px 0">
                <p>Level <?php echo $this->db_item["common"]["level"] . " " . ucwords($this->db_item["common"]["rarity"]) . " " . ucwords($this->db_item["common"]["class"]) ?></p>
                <p>Required Level: <?php echo $this->db_item["common"]["levelreq"]; ?></p>
            </div>
            <div class="node node-info js-itemnode">
                <!-- Normal Properties -->
                <?php include(F3::get('GUI') . "loot/modules/props_normal.php") ?>
            </div>
            <div class="node node-info js-itemnode">
                <!-- Magic Properties -->
                <?php include(F3::get('GUI') . "loot/modules/props_magic.php") ?>
            </div>
            <div class="node node-info js-itemnode">
                <?php include(F3::get('GUI') . "loot/modules/props_set.php") ?>
            </div>
            <div class="node node-info js-itemnode">
                <?php include(F3::get('GUI') . "loot/modules/props_set_family.php") ?>
            </div>
            <div class="action action-expand"></div>
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
