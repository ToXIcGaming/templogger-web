<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
ini_set( 'html_errors', 'On' );

header('Content-Type: application/json');
header("Cache-Control: max-age=300"); // Cache API for 5 minutes

// host, user and password settings
$host = "localhost";
$user = "";
$password = "";
$database = "temperatures";
$APIpass = "";

$con = mysqli_connect($host,$user,$password,$database);

if(isset($_GET['c'])) {
if ($_GET['c'] == 'add') {

if(isset($_GET['pass']) && $_GET['pass'] == $APIpass) {

if (isset($_GET['dt']) && isset($_GET['sens']) && isset($_GET['temp']) && isset($_GET['hum'])) {

$_GET['dt'] = mysqli_real_escape_string($con, $_GET['dt']);
$_GET['sens'] = mysqli_real_escape_string($con, $_GET['sens']);
$_GET['temp'] = mysqli_real_escape_string($con, $_GET['temp']);
$_GET['hum'] = mysqli_real_escape_string($con, $_GET['hum']);

$sql="INSERT INTO temperaturedata (dateandtime, sensor, temperature, humidity) 
VALUES ('{$_GET['dt']}', '{$_GET['sens']}', '{$_GET['temp']}', '{$_GET['hum']}')";

$stats = mysqli_query($con, $sql);

if ($stats === TRUE) {
    echo "New record created successfully";
} else {
    
}

mysqli_close ($con);

} else {
	echo 'Invalid parameters';
}

} else {
	echo 'Invalid Pass';
}

} elseif  ($_GET['c'] == 'display') {

if(isset($_GET['t'])) {
if ($_GET['t'] == 'list') {

$sql = "SELECT * FROM temperaturedata";

if (isset($_GET['sens'])) {
	$sql .= " WHERE sensor LIKE '%{$_GET['sens']}%'";
} else {

}

if (isset($_GET['hours'])) {
	$hours = $_GET['hours'];
	$sql .= " WHERE dateandtime >= (NOW() - INTERVAL $hours HOUR)";
} else {
}

if (isset($_GET['hours']) & isset($_GET['sens'])) {
	$hours = $_GET['hours'];
	$sql = "SELECT * FROM temperaturedata WHERE sensor LIKE '%{$_GET['sens']}%' AND dateandtime >= (NOW() - INTERVAL $hours HOUR)";
} else {
}

//$sql="SELECT * FROM temperaturedata ORDER BY dateandtime DESC";

//NOTE: If you want to show all entries from current date in web page uncomment line below by removing //
//$sql="select * from temperaturedata where date(dateandtime) = curdate();";

$temperatures = mysqli_query($con, $sql);

if (!$temperatures) {
    printf("Error: %s\n", mysqli_error($con));
    exit();
}

while($temperature=mysqli_fetch_assoc($temperatures)){
	$results[] = array(
	'datetime' => $temperature['dateandtime'],
	'sensor' => $temperature['sensor'],
	'temperature' => $temperature['temperature'],
	'humidity' => $temperature['humidity']
	);
}

echo json_encode($results);
		
mysqli_close ($con);

} elseif  ($_GET['t'] == 'stats') {

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

	$sqllast="select * from temperaturedata WHERE sensor LIKE '%{$row['sensor']}%' order by dateandtime desc limit 1";
	
	$last = mysqli_query($con, $sqllast);
	
	while($las=mysqli_fetch_assoc($last)){
		$lasts = array(
		'latest_datetime' => $las['dateandtime'],
		'latest_temperature' => $las['temperature'],
		'latest_humidity' => $las['humidity']
		);
	}
	
	$result = array_merge($statsAR, $first, $lasts);
	array_push($masterA, $result);
}
				
echo json_encode($masterA);
	
mysqli_close ($con);
	
} else {
	echo 'Invalid type specified';
}

} else {
	echo 'No type specified';
}

} else {
	echo 'Invalid command specified';
}

} else {
	echo 'No command specified';
}

?>