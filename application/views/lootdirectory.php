<div class="mod mod-notify mod-tip js-fadein" style="display:none">
    <ul id="js-filters">
        <li><a href="#" data-filter="*">All</a></li>
        <li><a href="#" data-filter=".armor">Armor</a></li>
        <li><a href="#" data-filter=".wep">Weapons</a></li>
        <li><a href="#" data-filter=".acc">Accessories</a></li>
        <li><a href="#" data-filter=".misc">Miscellaneous</a></li>
    </ul>
</div>
<div id="js-filter-container">
    <div class="line">
        <?php foreach($this->kingdoms as $k=>$kingdom) { ?>
            <div class="unit size1of4">
                <div class="mod <?php echo $k; ?>">
                    <h1 class="h-side style-gradient"><?php echo $kingdom; ?></h1>
                    <?php foreach ($this->types as $type) { 
                        if ($type['kingdom'] == $k) { ?>
                            <ul style="padding:12px;background-color:#f9f9f9;margin-bottom:12px;margin-top:12px;">
                                <li><h2><?php echo ucwords($type['type']); ?></h2></li>
                                <?php foreach($this->items as $item) { 
                                    if ($item['type'] == $type['code']) { ?>
                                        <li class="<?php echo $item['rarity']; ?>"><a href="/loot/<?php echo $item['urlname']; ?>"><?php echo $item['name']; ?></a></li>
                                    <?php }
                                } ?>
                            </ul>
                        <?php } 
                    } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>