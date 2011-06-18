<div class="line">
    <div class="unit" style="width:70%">
        <div class="mod mod-item style-shadow">
            <h1 class="h-item mod-fit style-gradient <?php echo $this->item->rarity; ?>"><?php echo $this->item->name; ?></h1>

            <div class="mod mod-fit">
                <div class="line">
                    <div class="unit size1of4">
                        <div class="node node-button"><p class="text-data"><?php echo $this->item->parent; ?></p></div>
                    </div>
                    <div class="unit size1of4">
                        <div class="node node-button"><p>Level: <span class="text-data"><?php echo $this->item->level; ?></span></p></div>
                    </div>
                    <div class="unit size1of4">
                        <div class="node node-button"><p>Level Required: <span class="text-data"><?php echo $this->item->levelreq; ?></span></p></div>
                    </div>
                    <div class="unit size1of4 lastUnit">
                        <div class="node node-button"><p>Rarity: <span style="font-weight:bold" class="<?php echo $this->item->rarity; ?>"><?php echo ucwords($this->item->rarity); ?></span></p></div>
                    </div>
                </div>
            </div>
            
            <?php
            
                // Normal Properties
                if ($this->item->properties->normal) { ?>
                    <div class="node node-info">
                        <h3>Normal Properties</h3>
                        <ul>
                        <?php 
                            foreach($this->item->properties->normal as $key => $value) { ?>
                                <li><?php echo $this->item->properties->normal[$key]->translation . ": " . "<span class=\"text-data\">" . $this->item->properties->normal[$key]->min . "</span>"; ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php }
            
                // Magic Properties
                if ($this->item->properties->magic) { ?>
                    <div class="node node-info">
                        <h3>Magic Properties</h3>
                        <ul class="magic">
                        <?php 
                            foreach($this->item->properties->magic as $key => $value) { ?>
                                <li><?php echo $this->item->properties->magic[$key]->translation; ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php }
            
                // Set Properties
                if ($this->item->properties->set) { ?>
                    <div class="node node-info">
                        <h3>Set Properties</h3>
                        <ul class="set">
                        <?php 
                            foreach($this->item->properties->set as $key => $value) { ?>
                                <li><?php echo $this->item->properties->set[$key]->translation; ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php }
                
                // Family Properties
                if ($this->item->properties->family) { ?>
                    <div class="node node-info">
                        <h3>Family Properties</h3>
                        <ul class="family">
                        <?php 
                            foreach($this->item->properties->family as $key => $value) { ?>
                                <li><?php echo $this->item->properties->family[$key]->translation . " (" . $this->item->properties->family[$key]->req_equip . " Equipped)"; ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php }
            ?>
        </div>
    </div>
    <div class="unit lastUnit" style="width:30%">
        <?php
        
            // Family Members
            if (isset($this->siblings) && ($this->siblings)) { ?>
                <div class="mod mod-side style-shadow">
                    <h1 class="h-side h-box style-gradient"><?php echo $this->item->family; ?> Family</h1>
                    <?php
                        
                        foreach ($this->siblings as $sibling) { ?>
                            <a class="link-block" href="/loot/<?php echo $sibling->urlname; ?>">
                                <div class="node node-item">
                                    <img class="img-itemthumb" src="/img/stormshield32.png">
                                    <p><?php echo $sibling->name; ?></p>
                                    <p class="text-info"><?php echo $sibling->level . " " . $sibling->parent; ?></p>
                                </div>
                            </a>
                        <?php }
                    ?>
                </div>
            <?php }
        
            // Variants
            if (isset($this->variants) && ($this->variants)) { ?>
                <div class="mod mod-side style-shadow">
                    <h1 class="h-side h-box style-gradient">Variants</h1>
                    
                    <?php
                        foreach($this->variants as $variant) { ?>
                            <a class="link-block" href="/loot/<?php echo $variant->urlname; ?>">
                                <div class="node node-item">
                                    <img class="img-itemthumb" src="/img/stormshield32.png">
                                    <p><?php echo $variant->name; ?></p>
                                    <p class="text-info"><?php echo $variant->level . " " . $variant->parent; ?></p>
                                </div>
                            </a>
                        <?php }
                    ?>
                </div>
            <?php }
            
            // Similar
            if (isset($this->similar) && ($this->similar)) { ?>
                <div class="mod mod-side style-shadow">
                    <h1 class="h-side h-box style-gradient">Similar</h1>
                    
                    <?php
                        foreach($this->similar as $similar) { ?>
                            <a class="link-block" href="/loot/<?php echo $similar->urlname; ?>">
                                <div class="node node-item">
                                    <img class="img-itemthumb" src="/img/stormshield32.png">
                                    <p><?php echo $similar->name; ?></p>
                                    <p class="text-info"><?php echo $similar->level . " " . $similar->parent; ?></p>
                                </div>
                            </a>
                        <?php }
                    ?>
                </div>
            <?php } else if ($this->item->rarity != "normal") { ?>
                <div class="mod mod-notify mod-tip">
                    <p>Highest level item of its type</p>
                </div>
            <?php }
        ?>
        
    </div>
</div>