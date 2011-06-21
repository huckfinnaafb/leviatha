<div class="line">
    <?php if (file_exists(__SITE_PATH . "/application/views/lootdirectorycache.html")) { 
        include (__SITE_PATH . "/application/views/lootdirectorycache.html"); 
    } else { ?>
        <?php foreach($this->kingdoms as $k=>$kingdom) { ?>
            <div class="unit size1of4">
                <div class="mod">
                    <h1 class="h-side style-gradient"><?php echo $kingdom; ?></h1>
                    <?php foreach ($this->types as $type) { 
                        if ($type['kingdom'] == $k) { ?>
                            <ul class="list-directory">
                                <li><h2><?php echo ucwords($type['type']); ?></h2></li>
                                <?php foreach($this->items as $item) { 
                                    if ($item['type'] == $type['code']) { ?>
                                        <li><a href="/loot/<?php echo $item['urlname']; ?>"><?php echo $item['name']; ?></a></li>
                                    <?php }
                                } ?>
                            </ul>
                        <?php } 
                    } ?>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
</div>