<div class="mod">
    <table id="searchresults" class="table-search">
        <colgroup>
            <col style="width: 300px">
            <col style="width: 200px">
            <col style="width: 150px">
            <col style="width: 100px">
            <col style="width: 100px">
        </colgroup>
        <thead class="thead-search">
            <tr>
                <th class="theading-search">Name</th>
                <th class="theading-search">Parent</th>
                <th class="theading-search">Rarity</th>
                <th class="theading-search">Level Req.</th>
                <th class="theading-search">Item Level</th>
            </tr>
        </thead>
        <tbody class="tbody-search">
            <?php
                foreach ($this->results as $result) { ?>
                    <tr>
                        <td><a href="/loot/<?php echo $result['urlname']; ?>"><?php echo $result['name']; ?></a></td>
                        <td><?php echo ucwords($result['relationship']); ?></td>
                        <td><?php echo ucwords($result['rarity']); ?></td>
                        <td><?php echo $result['levelreq']; ?></td>
                        <td><?php echo $result['level']; ?></td>
                    </tr>
                <?php } 
            ?>

        </tbody>
    </table>
    <div id="pager" class="mod">
        <form>
            <img src="/img/first.png" class="first"/>
            <img src="/img/prev.png" class="prev"/>
            <input type="text" class="pagedisplay"/>
            <img src="/img/next.png" class="next"/>
            <img src="/img/last.png" class="last"/>
            <select class="pagesize">
                <option selected="selected"  value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
                <option  value="40">40</option>
            </select>
        </form>
    </div>

</div>