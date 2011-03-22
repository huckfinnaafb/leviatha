<div class="module mod-info">
    <h1 class="h-info">Normal Properties</h1>
    <table class="table-info">
        <tbody>
            <?php
                try {
                    if (isset($this->db_item["prop_normal"]) && (!empty($this->db_item["prop_normal"]))) {
                        foreach($this->db_item["prop_normal"] as $row) {
                            echo "<tr>";
                                echo "<td>{$row['translation']}: {$row['value']}</td>";
                            echo "</tr>";
                        }
                    } else {
                        throw new Exception("No normal properties found.");
                    }
                } catch (Exception $e) {
                    echo "<p>" . $e->getMessage(), "</p>\n";
                }
            ?>
        </tbody>
    </table>
</div>
