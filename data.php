	<?php

	$tcom = (($_GET['tcommand']));
	$temp = (($_GET['temp']));
	$humidity = (($_GET['humidity']));

	date_default_timezone_set('America/Indiana/Indianapolis'); 

	$time = date('H:i');
	$month = date(m);
	$date = date(j);
	$year = date(Y);

	$db="DBNAME";

	$link = mysql_connect('', 'ADMIN', 'PASS');

	if (! $link) die(mysql_error());
	mysql_select_db($db , $link) or die("Couldn't open $db: ".mysql_error());

	$queryResult = mysql_query("INSERT INTO master (tcommand, temp, time, humidity, month, date, year) VALUES ('$tcom', '$temp', '$time', '$humidity', '$month', '$date', '$year')");

?>