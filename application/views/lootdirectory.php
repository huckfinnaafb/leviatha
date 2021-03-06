<div class="line">
    <?php foreach($this->kingdoms as $k=>$kingdom) { ?>
        <div class="unit size1of4">
            <div class="mod">
                <h1 class="h-column pattern-gradient"><?php echo $kingdom; ?></h1>
                <?php foreach ($this->types as $type) { 
                    if ($type['kingdom'] == $k) { ?>
                        <ul class="bullets list-column">
                            <h2 style="font-size:18px"><?php echo ucwords($type['type']); ?></h2>
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