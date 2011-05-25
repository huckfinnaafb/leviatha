<div class="mod mod-header">
    <div class="line">
        <div class="unit size1of2">
            <div class="hgroup">
                <a class="link-title" title="Return Home" href="/"></a>
            </div>
            <div class="nav">
                <?php
                
                    // Navigation
                    foreach($this->navigation as $k => $link) {
                        if ($link['enabled']) {
                            echo "<a href=\"{$k}\" class=\"link-homepage\">" . $link['text'] . "</a>\n\t";
                        }
                    }
                ?>
            </div>
        </div>
        <div class="unit size1of2 lastUnit">
            <form action="/search" method="get" class="form-search">
                <fieldset>
                    <input type="text" class="input-text" name="q"/>
                    <input type="submit" class="input-submit" value="Search"/>
                </fieldset>
            </form>
        </div>
    </div>
</div>