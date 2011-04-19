<?php include(F3::get('GUI') . "/includes/header.php") ?>
<div class="line">
    <div class="unit size2of3">
        <div class="mod mod-item">
            <h1 class="h-item"><?php echo $this->name ?></h1>
            <div class="node">
                <img class="img-item" src="/img/stormshield.png" style="float:left;margin:0 12px 12px 0">
                <p>Level <?php 
                    $parent = (is_null($this->itemclass)) ? $this->division : $this->itemclass;
                    echo $this->level . " " . ucwords($this->rarity) . " " . ucwords($parent); ?></p>
                <p>Required Level: <?php echo $this->levelreq; ?></p>
            </div>
            <div class="node node-info js-itemnode">
                <!-- Normal Properties -->
                <?php include(F3::get('GUI') . "loot/modules/props_normal.php") ?>
            </div>
            <div class="action action-expand"></div>
        </div>
    </div>
    <div class="unit size1of3">
        <!-- Magic Variants -->
        <?php include(F3::get('GUI') . "loot/modules/variants.php") ?>
    </div>
<!-- Footer -->
<?php include(F3::get('GUI') . "/includes/footer.php") ?>
