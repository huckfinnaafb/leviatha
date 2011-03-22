<?php

$file = file('types.txt');
$a = array();

foreach($file as $f)
{
	++$a[strtolower(trim($f))];
}

foreach($a as $key => $value)
{
	if($value > 1)
	{
		echo $key . PHP_EOL;
	}
}