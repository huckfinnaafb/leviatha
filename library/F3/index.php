<?php

require __DIR__.'/lib/base.php';

F3::route('GET /',
	function() {
		F3::set('modules',
			array(
				'apc'=>
					'Cache engine',
				'gd'=>
					'Graphics plugin',
				'hash'=>
					'Framework core',
				'json'=>
					'Various plugins',
				'memcache'=>
					'Cache engine',
				'mongo'=>
					'M2 MongoDB mapper',
				'pcre'=>
					'Framework core',
				'pdo_mssql'=>
					'SQL handler/Axon ORM',
				'pdo_mysql'=>
					'SQL handler/Axon ORM',
				'pdo_pgsql'=>
					'SQL handler/Axon ORM',
				'pdo_sqlite'=>
					'SQL handler/Axon ORM',
				'session'=>
					'Framework core',
				'sockets'=>
					'Network plugin',
				'xcache'=>
					'Cache engine'
			)
		);
		echo Template::serve('welcome.htm');
	}
);

F3::run();

?>
