<?php
define("MYSQL_HOST", "leviathan.db");
define("MYSQL_USER", "huckfinnaafb");
define("MYSQL_PASSWORD", "tw!sty_512");
define("MYSQL_DB", "Leviathan");

$con = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD); 
$database = mysql_select_db(MYSQL_DB);

$query = "
	SELECT *
	FROM loot_unique_properties_dirty
";
$results = mysql_query($query);

while ($row = mysql_fetch_array($results)) {
	$i = 1;
	$name = $row['name'];
	while ($row['prop' . $i]) {
		$prop           = $row['prop' . $i];
		$param          = $row['par'. $i];
		$min            = $row['min' . $i];
		$max            = $row['max' . $i];
		echo $name . ',' . $prop . ',' . $param . ',' . $min . ',' . $max . '<br>';
		$i++;
	}
}