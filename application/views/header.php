<div class="mod">
    <img width=348 height=50 src="/img/logosmall.png">
</div>
<nav class="mod mod-nav style-gradient style-shadow">
    <div style="float:left">
        <?php
        
            // Home Navigation
            foreach($this->navigation as $k => $link) {
                if ($link['enabled']) {
                    echo "<a title=\"{$link['title']}\" href=\"{$k}\" class=\"link-nav\">" . $link['text'] . "</a>\n\t";
                }
            }
        ?>
    </div>
    <div style="float:right">
        <form action="/search" method="get" class="form-search">
            <fieldset>
                <input type="text" class="input-text" name="q"/>
                <input type="submit" class="input-submit" value="Search"/>
            </fieldset>
        </form>
    </div>
</nav>