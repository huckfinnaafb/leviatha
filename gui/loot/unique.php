<?php include(F3::get('GUI') . "/includes/header.php") ?>
<div class="line">
    <div class="unit size2of3">
        <!-- Common Properties -->
        <div class="module mod-info">
            <h1 class="h-page"><?php echo $this->db_item["common"]["name"] ?></h1>
            <img src="/img/lena128.png" style="float: left; margin: 0 10px 10px 0;">
            <p class="text-info">Level <?php echo $this->db_item["common"]["level"] . " <a href='/loot?item=" . $this->db_item["common"]["classurl"] . "'>" . ucwords($this->db_item["common"]["class"]) ?></a></p>
            <p class="text-info">Required Level: <?php echo $this->db_item["common"]["levelreq"]; ?></p>
        </div>
        
        <div class="line">
            <div class="unit size1of2">
                <!-- Normal Properties -->
                <?php include(F3::get('GUI') . "loot/modules/props_normal.php") ?>
            </div>
            
            <div class="unit size1of2 lastUnit">
                <!-- Magic Properties -->
                <?php include(F3::get('GUI') . "loot/modules/props_magic.php") ?>
            </div>
        </div>
    </div>

    <div class="unit size2of3 lastUnit">
        <!-- Similar Items -->
        <?php include(F3::get('GUI') . "loot/modules/similar.php") ?>
    </div>
</div>

<!-- Footer -->
<?php include(F3::get('GUI') . "/includes/footer.php") ?>
