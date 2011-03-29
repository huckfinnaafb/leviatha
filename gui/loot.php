<?php include(F3::get('GUI') . "/includes/header.php") ?>
<div class="line">
    
    <!-- Main Content -->
    <div class="unit size3of4">
        <div class="module" style="padding-bottom:0">
            <h1 class="h-page">Loot</h1>
        </div>
        
        <div class="line">
            <div class="unit size1of2">
                <div class="module mod-primary">
                    <h1 class="h-section">Directory</h1>
                    <p class="text-content">Visit the <a href="/loot/directory">Loot Directory</a> for a complete listing of all Diablo 2 items.</p>
                </div>
            </div>
            <div class="unit size1of2 lastUnit">
                <div class="module mod-primary">
                    <h1 class="h-section">Magic Prefixes & Suffixes</h1>
                    <p class="text-content">Leviatha doesn't have everything, yet. In the meantime, check out <a href="http://www.diablowiki.com/Portal:Items_(Diablo_II)">Diablo Wiki</a> <span class="text-info">(External)</span>, a great resource for all things Diablo 2.</p>
                </div>
            </div>
        </div>
        
        <!--
        <div class="module">
            <h1 class="h-section">The Taxonomy of Loot</h1>
            <p class="text-content">Every game uses structures to order and classify data and objects. Diablo 2 is no different. To build the database, a flexible hierarchy was required to, in essence, have a place for everything and everything in its place.</p>
            <p class="text-content">Grabbing inspiration from biological classification, the study of the structures of life, I was able to develop a good naming schema and hierarchy in which to build the database. It looks a little something like this.</p>
            <ol class="list-taxonomy">
                <li>Domain - Loot, Monsters, World, Classes, ...</li>
                <li>Kingdom - Weapon, Armor, Accessory, ...</li>
                <li>Division - Sword, Boots, Charm, ...</li>
                <li>Class - Gladius, Falchion, Aegis, ...</li>
                <li>Extended Class - Unique and set items based on their <i>class</i> parent (retain base properties, but enhanced with magical properties)</li>
            </ol>
            <p class="text-content">This essentially covers every object in the game, and more expansion of structures is allowed. Having this hierarchy allows me to query the database in smart ways which are efficient and flexible. You can see this hierarchy in action at the <a href="/loot/directory">loot directory</a>.</p>
        </div>
        
        <div class="module">
            <h1 class="h-section">Magical Properties - Y U NO EASY</h1>
            <p class="text-content">Getting the raw data into the database was relatively easy. Making that data understandable to humans is proving to me more challenging (you know, for an amatuer). I'm convinced other sites did it by hand.</p>
            <h2>But I have a plan.</h2>
            <p class="text-content">In the database, there's a master table with 293 rows of properties. Various fields tell the application whether to display it, which classes benefits most from this property (if any in particular), and so forth. There's also a field that acts as a 'translation' for the property - turning "dmg" into "Enhanced Damage". But what I haven't done yet is sprinkle the "min", "max", and "parameter" variables inside that string, allowing the application to parse it so it is more readable by users.</p>
        </div>
        -->
        
    </div>
    
    <!-- Sidebar -->
    <div class="unit size1of4 lastUnit">
    
        <div class="module mod-info">
            <h1 class="h-info">Latest News</h1>
            <p class="text-date">March 11, 2011</time>
            <p>Starting today, Sam is the best.</p>
        </div>
        
        <div class="module mod-info">
            <h1 class="h-info">Patch Changes</h1>
            <p class="text-date"><a href="" title="Patch Notes">v. 1.4.7.0</a></time>
            <p>Blizzard nerfed something and now everyone is complaining.</p>
        </div>
    </div>
    
</div>
<?php include(F3::get('GUI') . "/includes/footer.php") ?>