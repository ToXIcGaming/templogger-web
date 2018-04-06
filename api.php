<?php
// settings
// host, user and password settings
$host = "localhost";
$user = "";
$password = "";
$database = "temperatures";

//if (isset($_POST['buttonoff']))
//{
//	shell_exec('sudo pkill -f clock.py');
//	shell_exec('sudo python3 /home/pi/scripts/clear_oled.py > /dev/null 2>&1 &');	
//	//header('Location: index.php');
//	}
//
//if (isset($_POST['buttonon']))
//{
//	shell_exec('sudo python /home/pi/scripts/clock.py > /dev/null 2>&1 &');
//	//header('Location: index.php');
//}

// make connection to database
$con = mysqli_connect($host,$user,$password,$database);

if(isset($_GET['t'])) {

$type = $_GET['t'];

if ($type == 'lt_temps') {

if (isset($_GET['sens'])) {
	$sql="SELECT * FROM temperaturedata WHERE sensor LIKE '%{$_GET['sens']}%'";
} else {

	$sql="SELECT * FROM temperaturedata";

}

//$sql="SELECT * FROM temperaturedata ORDER BY dateandtime DESC";

//NOTE: If you want to show all entries from current date in web page uncomment line below by removing //
//$sql="select * from temperaturedata where date(dateandtime) = curdate();";

// set query to variable
$temperatures = mysqli_query($con, $sql);

if (!$temperatures) {
    printf("Error: %s\n", mysqli_error($con));
    exit();
}

        // loop all the results that were read from database and "draw" to web page
        while($temperature=mysqli_fetch_assoc($temperatures)){
			$temp =	$temperature['temperature'];
			$humidity = $temperature['humidity'];
//			if ($_GET['dt'] == "uk") {
//			$date = explode(" ", $temperature['dateandtime'])[0];
//			$time = strstr($temperature['dateandtime'], ' ');	
//			$datetime = date("d-m-Y", strtotime($date))." ".$time;
//			} else {
				$datetime = $temperature['dateandtime'];
//			}
			$results[] = array(
			'datetime' => $datetime,
			'sensor' => $temperature['sensor'],
			'temperature' => $temp,
			'humidity' => $humidity
			);

        }

echo json_encode($results);
		
mysqli_close ($con);

} elseif  ($type == 'stats') {

$sql="SELECT sensor, COUNT(id) FROM temperaturedata GROUP BY sensor;";

$stats = mysqli_query($con, $sql);

$masterA = array();

while($row = mysqli_fetch_array($stats)){
	$sqlA="SELECT AVG(temperature), AVG(humidity), MAX(temperature), MAX(humidity), MIN(temperature), MIN(humidity) FROM temperaturedata WHERE sensor LIKE '%{$row['sensor']}%'";
    
	$statsA = mysqli_query($con, $sqlA);
	
	while($statA=mysqli_fetch_assoc($statsA)){
				$statsAR = array(
				'sensor' => $row['sensor'],
				'avg_temperature' => number_format((float)$statA['AVG(temperature)'], 1, '.', ''),
				'avg_humidity' => number_format((float)$statA['AVG(humidity)'], 1, '.', ''),
				'max_temperature' => number_format((float)$statA['MAX(temperature)'], 1, '.', ''),
				'max_humidity' => number_format((float)$statA['MAX(humidity)'], 1, '.', ''),
				'min_temperature' => number_format((float)$statA['MIN(temperature)'], 1, '.', ''),
				'min_humidity' => number_format((float)$statA['MIN(humidity)'], 1, '.', ''),
				);
	}
	
	$sqlfir="select dateandtime from temperaturedata WHERE sensor LIKE '%{$row['sensor']}%' order by dateandtime asc limit 1";
	
	$firs = mysqli_query($con, $sqlfir);
	
	while($fir=mysqli_fetch_assoc($firs)){
				$first = array(
				'first_datetime' => $fir['dateandtime'],
				);
	}
    
	$result = array_merge($statsAR, $first);
	array_push($masterA, $result);
}

echo json_encode($masterA);
	
mysqli_close ($con);
	
} elseif  ($type == 'cpu_stats') {
	
$cputemp = substr(shell_exec("/home/pi/temp"), 0, -1);
$uptime = substr(substr(shell_exec("uptime -p"), 3), 0, -1);

$cpuStats = array(
				'cpu_temp' => $cputemp,
				'uptime' => $uptime
				);

echo json_encode($cpuStats);
	
} else {
	echo 'Invalid type specified';
}

} else {
	echo 'No type specified';
}

?>