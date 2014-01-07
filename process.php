<?php
$temp = $_POST['myTemp'];
$fp = fopen('/YOURSERVER/tdata.html', 'w');
$savestring =  '<<' . $temp;
fwrite($fp, $savestring);
fclose($fp);
echo 'Temperature Updated to ';
echo $temp;
echo ' deg F';
?>

