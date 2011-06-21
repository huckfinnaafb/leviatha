<div class="mod">
<div class="line">
<?php
foreach($this->kingdoms as $kingdom) { ?>
<div class="unit size1of3">
<ul class="list-directory">

<?php
foreach($this->relations as $relationship) { 
if ($relationship['kingdom'] == $kingdom) { ?>
<li><h3 id="<?php echo strtolower($relationship['division']); ?>"><?php echo $relationship['division'] ?></h3>
<ul>
<?php
foreach($this->items as $item) {
if ($item['division'] == $relationship['division']) { ?>
<li class="<?php echo $item['rarity']; ?>"><a class="<?php echo $item['rarity']; ?>" href="/loot/<?php echo $item['urlname']; ?>"><?php echo $item['name']; ?></a></li>
<?php } else {
continue;
}
}
?>
</ul>
</li>
<?php }
}
?>
</ul>
</div>
<?php }
?>
</div>
</div>
