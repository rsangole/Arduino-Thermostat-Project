	<?php
	
	$tcom = $_POST['tcom'];
	$fp = fopen('DOMAIN/tdata.html', 'w');
	$savestring =  '<<' . $tcom;
	fwrite($fp, $savestring);
	fclose($fp);
	echo 'Temperature Updated to ';
	echo $tcom;
	echo ' deg F';

	date_default_timezone_set('America/Indiana/Indianapolis'); 

	$time = date('H:i');
	$month = date(m);
	$date = date(j);
	$year = date(Y);

	$db="DB_NAME";
	$link = mysql_connect("", "ADMIN", "PASS");
	if (! $link) die(mysql_error());
	mysql_select_db($db, $link) or die("Couldn't open $db: ".mysql_error());

	$result = mysql_query("SELECT temp FROM master ORDER BY id DESC LIMIT 1");
	$lasttemp = mysql_result($result,0);
	$result = mysql_query("SELECT humidity FROM master ORDER BY id DESC LIMIT 1");
	$lasthumidity= mysql_result($result,0);

	$queryResult = mysql_query("INSERT INTO master (tcommand, temp, time, humidity, month, date, year) VALUES ('$tcom', '$lasttemp', '$time', '$lasthumidity', '$month', '$date', '$year')");

	$queryResult = mysql_query("INSERT INTO tcomchange (tcom, time, month, date) VALUES ('$tcom', '$time','$month','$date')");
	
?>

	<meta http-equiv="refresh" content="3;url=http://DOMAIN.com"> //Wait for 3 sec, go back to dashboard
