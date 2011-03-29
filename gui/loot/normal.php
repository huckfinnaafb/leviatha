<?php include(F3::get('GUI') . "/includes/header.php") ?>
<div class="line">
    <div class="unit size2of3">
        <!-- Common Properties -->
        <div class="module mod-info">
            <h1 class="h-page"><?php echo $this->db_item["common"]["name"] ?></h1>
            <img src="/img/lena64.png" style="float: left; margin: 0 10px 10px 0;">
            <p class="text-info">Level <?php echo $this->db_item["common"]["level"] . " " . ucwords($this->db_item["common"]["division"]) ?></p>
            <p class="text-info">Required Level: <?php echo $this->db_item["common"]["levelreq"]; ?></p>
        </div>
        <!-- Normal Properties -->
        <?php include(F3::get('GUI') . "loot/modules/props_normal.php") ?>
    </div>
    <div class="unit size1of3">
        <!-- Magic Variants -->
        <?php include(F3::get('GUI') . "loot/modules/variants.php") ?>
    </div>
<!-- Footer -->
<?php include(F3::get('GUI') . "/includes/footer.php") ?>
