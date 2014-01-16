<!DOCTYPE html>
<html>
	<title>ArduDash, by Rahul Sangole</title>
<head>
<script type='text/javascript' src='https://www.google.com/jsapi'></script>
<script type='text/javascript'>
google.load('visualization', '1', {packages: ['gauge']});
google.setOnLoadCallback(drawChart);
function drawChart(){
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Parameter');
	data.addColumn('number', 'Value');
	data.addRows([
	<?php
	$db="DB_NAME";
	$link = mysql_connect("", "ADMIN", "PASS");
	mysql_query('SET NAMES utf8');
	mysql_select_db($db, $link) or die("Couldn't open $db: ".mysql_error());
	$result = mysql_query("SELECT temp FROM master ORDER BY id DESC LIMIT 1");
	if ($result !== false) {
				$temp=mysql_result($result,0);
				echo "['Temp F',";
				echo $temp;
				echo "]";
	}
	?>
	
	]);
	var options = {
	width: 250,
	height: 250,
	minorTicks:5,
	majorTicks:['50','60','70','80'],
	max:80, min:50,
	};
	var chart = new google.visualization.Gauge(document.getElementById('gage1'));
	chart.draw(data, options);
}
google.setOnLoadCallback(drawChart);
</script>

<script type='text/javascript' src='https://www.google.com/jsapi'></script>
<script type='text/javascript'>
google.load('visualization', '1', {packages: ['gauge']});
google.setOnLoadCallback(drawChart);
function drawChart(){
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Parameter');
	data.addColumn('number', 'Value');
	data.addRows([
	<?php
	$db="DB_NAME";
	$link = mysql_connect("", "ADMIN", "PASS");
	mysql_query('SET NAMES utf8');
	mysql_select_db($db, $link) or die("Couldn't open $db: ".mysql_error());
	$result = mysql_query("SELECT tcommand FROM master ORDER BY id DESC LIMIT 1");
	if ($result !== false) {
				$tcom=mysql_result($result,0);
				echo "['SetPt F',";
				echo $tcom;
				echo "]";
	}
	?>
	
	]);
	var options = {
	width: 180,
	height: 180,
	minorTicks:5,
	majorTicks:['50','60','70','80'],
	max:80, min:50,
	};
	var chart = new google.visualization.Gauge(document.getElementById('gage2'));
	chart.draw(data, options);
}
google.setOnLoadCallback(drawChart);
</script>

<script type='text/javascript' src='https://www.google.com/jsapi'></script>
<script type='text/javascript'>
google.load('visualization', '1', {packages: ['gauge']});
google.setOnLoadCallback(drawChart);
function drawChart(){
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Parameter');
	data.addColumn('number', 'Value');
	data.addRows([
	<?php
	$db="DB_NAME";
	$link = mysql_connect("", "ADMIN", "PASS");
	mysql_query('SET NAMES utf8');
	mysql_select_db($db, $link) or die("Couldn't open $db: ".mysql_error());
	$result = mysql_query("SELECT humidity FROM master ORDER BY id DESC LIMIT 1");
	if ($result !== false) {
				$hum=mysql_result($result,0);
				echo "['Humidity %',";
				echo $hum;
				echo "]";
	}
	?>
	
	]);
	var options = {
	width: 180,
	height: 180,
	minorTicks:5,
	max:100, min:0,
	};
	var chart = new google.visualization.Gauge(document.getElementById('gage3'));
	chart.draw(data, options);
}
google.setOnLoadCallback(drawChart);
</script>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);
function drawChart() {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Time');
    data.addColumn('number', 'Temp');
    data.addColumn('number', 'TCom');
    data.addColumn('number', 'Humidity');
    data.addRows([
                  <?php
				$db="DB_NAME";
				$link = mysql_connect("", "ADMIN", "PASS");
				mysql_query('SET NAMES utf8');
				mysql_select_db($db, $link) or die("Couldn't open $db: ".mysql_error());
				$result = mysql_query("SELECT * FROM master ORDER BY id DESC LIMIT 20");
                  if ($result !== false) {
                  $num=mysql_numrows($result);
                  $i=$num-1;
                  echo"";
                  while ($i > -1) {
                  $time=substr(mysql_result($result,$i,"time"),0,5);
                  $temp=mysql_result($result,$i,"temp");
                  $tcom=mysql_result($result,$i,"tcommand");
                  $humidity=mysql_result($result,$i,"humidity");
                  echo "['";
                  echo "$time";
                  echo "',";
                  echo "$temp";
                  echo ",";
                  echo "$tcom";
                  echo ",";
                  echo "$humidity";
                  echo "]";
                  	if ($i > 0)
                  	{
                  	echo ",";
                  	}
                  $i--;    
                  }
                }   
      ?>
    ]);
    
    var options = {
    width: 610, height: 300,
    hAxis: {title: 'Time'},
    vAxes: { 0: {title: 'Temp F', maxValue: 80, minValue: 40}, 
    1: {title: 'Humidity %', maxValue: 100, minValue: 0}},
    series:{
       0:{targetAxisIndex:0},
       1:{targetAxisIndex:0},
       2:{targetAxisIndex:1}},
	pointSize: 3

    };
    
    var chart = new google.visualization.LineChart(document.getElementById('chart_div1'));
    chart.draw(data, options);
}
</script>
</head>

<body>
	<table width="600" border="1">
		<tr>
			<td colspan="2">
				<form action="process.php" method="POST">
				</select>
				Select Temp (deg F) to Command:
					<select name="tcom" id="tcom" onchange="temp()">
					<option>58</option>
					<option>60</option>
					<option>62</option>
					<option>64</option>
					<option>66</option>
					<option>68</option>
					<option>70</option>
					</select>
				<button onclick="submit()">Change Temp Now!</button>
				</form>
			</td>
			<td>
				Last Update: 
				<?php
					$db="DB_NAME";
					$link = mysql_connect("", "ADMIN", "PASS");
					mysql_query('SET NAMES utf8');
					mysql_select_db($db, $link) or die("Couldn't open $db: ".mysql_error());
					$result = mysql_query("SELECT time FROM master ORDER BY id DESC LIMIT 1");
					if ($result !== false) {
								$time=substr(mysql_result($result,0),0,5);
								echo $time;
					}
					?>
				</td>			
		</tr>
	<tr>
		<td width="200"><div id="gage2" style="width: 180px; height: 250px;"></div></td>
		<td width="200"><div id="gage1" style="width: 250px; height: 250px;"></div></td>
		<td width="200"><div id="gage3" style="width: 180px; height: 250px;"></div></td>
	</tr>
	<tr>
		<td colspan="3"><div id="chart_div1" style="width: 400px; height: 300px;"></div></td>
	</tr>
	<tr><td><a href="https://xively.com/feeds/####" target="blank">Link to Ardudash on Xively</a>
	</td></tr>
</table>

</body>

</html>