<?php
	
	# GLOBAL DEFINITIONS
	define(MYSQL_HOST, "leviathan.db");
	define(MYSQL_USER, "guest");
	define(MYSQL_PASS, "0fbca43b41fd55d9ea0de0eb88f243babff225f7");
	define(MYSQL_DB, "leviathan");
	
	# CONNECT DB
	function connect() {
		$con = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS); 
		$database = mysql_select_db(MYSQL_DB); 
		return $con;
	}
	
	# PROCESS SEARCH QUERY
	function processSearch($term) {
		$term = mysql_real_escape_string(htmlspecialchars($term));
		if ($term == "") return 0;
		$results = results($term);
		if (mysql_num_rows($results) == 0) return 2;
		else return $results;
	}
	
	# RESULTS QUERY
	function results($term) {
		$query = "
			(SELECT name, type, levelreq, level AS level
				FROM loot_normal 
				WHERE name
				LIKE '%$term%')
			UNION
			(SELECT name, type, levelreq, level AS level
				FROM loot_unique 
				WHERE name 
				LIKE '%$term%')
			ORDER BY level DESC
		";
		return mysql_query($query);
	}
	
	# GET OBJECT PROPERTIES
	function getObject ($term) {
		$query = "
			(SELECT *
			FROM weapons
			WHERE name = 'Short Sword')
			UNION
			(SELECT *
			FROM armor
			WHERE name = 'Short Sword')
		";
		return mysql_query($query);
	}
	
	# OBJECT URL STRIPPING
	function createurl($term) {
		return strtolower(str_replace('\'', '', str_replace(' ', '', $term)));
	}