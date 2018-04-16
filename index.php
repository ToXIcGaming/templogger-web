<html>
<head>
    <title>Temperature Logger</title>
    <link rel="shortcut icon" href="favicon.ico" type="image/vnd.microsoft.icon" />
    <link rel="icon" href="favicon.ico" type="image/vnd.microsoft.icon" />
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="includes/css/bootstrap.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet" href="includes/css/superhero.bootstrap.min.css">
    <!--Custom Styling!-->
    <link rel="stylesheet" href="includes/css/customstyle.css">
    <script src="includes/js/jquery.min.js"></script>
    <script src="includes/js/jquery.dataTables.min.js"></script>
    <script src="includes/js/dataTables.bootstrap.js"></script>
    <link rel="stylesheet" href="includes/css/dataTables.bootstrap.css">
    <!-- Latest compiled and minified JavaScript -->
    <script src="includes/js/bootstrap.min.js"></script>
    <style>
        body {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            font-size: 17px!important;
        }
        
        table,
        .table {
            font-size: 17px!important;
        }
		
		#lineup {
			display: inline-block;
			vertical-align: top;
			padding-right: 40px;
		}
		
		#cpu {
			padding-top: 10px;
			position: relative;
			display: block;
		}
    </style>
	<script>
    $(document).ready(function() {
				
		var jsonData = $.ajax({
		url: './api.php?t=cpu_stats',
		dataType: "json",
		success: function (data) {

		$('#cpu').append("CPU Temp: " + data.cpu_temp + "°C - Uptime: " + data.uptime + "");
		
		}
		});
				
        $("#buttonon").click(function() {
            $.post("./api.php", {
                    buttonon: ""
                },
                function(data, status) {

                });
        });

        $("#buttonoff").click(function() {
            $.post("./api.php", {
                    buttonoff: ""
                },
                function(data, status) {

                });
        });
    });
        </script>
</head>

<body>
    <div class="navbar navbar-default">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">Temperature Logger</a>
            </div>
            <div class="navbar-collapse collapse navbar-responsive-collapse">
                <ul class="nav navbar-nav">
					<li><a href="?type=list">List</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
					<li id="cpu"></li>
				    <!--<li><a id="buttonon" href="">Turn on display</a></li>
                    <li><a id="buttonoff" href="">Turn off display</a></li>-->
                    <!--<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Welcome back</a>                    
						<ul class="dropdown-menu">
							<li>Info</li>
						</ul>
					</li>-->
                </ul>
            </div>
        </div>
    </div>
    <!--Alert container!-->
    <div id="adminAlerts" class="container">
    </div>
    <!--Main Body Container!-->
    <div class="container">
<?php
if (isset($_GET['type'])) {
if($_GET['type'] == 'list') { 
?>
        <script>
            $(document).ready(function() {
                $('#example').dataTable({
					ordering:  true,
                    "ajax": {
                        "url": './api.php?t=lt_temps',
                        "dataSrc": "",
                    },
                    "order": [
                        [0, "desc"]
                    ],
                    "columns": [{
                        "data": "datetime"
                    }, {
                        "data": "sensor"
                    }, {
                        "data": "temperature"
                    }, {
                        "data": "humidity"
                    }]
                });
            });
        </script>

        <table id="example" class="table table-striped table-hover" cellspacing="0" width="100%">
            <thead>
                <tr class="tableHeader">

                    <th class="col-xs-1">Time and Date</th>
                    <th class="col-xs-1">Sensor</th>
                    <th class="col-xs-1">Temperature (&deg;C)</th>
                    <th class="col-xs-1">Humidity (%)</th>
                </tr>
            </thead>
        </table>
    </div>

<?php 
} else {

}
} else { ?>
	
	<script type="text/javascript" src="includes/js/loader.js"></script>
	<script>
	$( document ).ready(function() {

		var jsonData = $.ajax({
		url: './api.php?t=stats',
		dataType: "json",
		success: function (data) {
			$.each(data,function(index,obj)
			{				
				$('#cont').append('<div align="center" id="stats' + obj.sensor + '"></div>');
				$('#cont').append('<div id="chart' + obj.sensor + '" style="width: 100%;"></div><br>');
				$('#stats' + obj.sensor + '').append("<h2>" + obj.sensor + " Stats</h2>");
				$('#stats' + obj.sensor + '').append("<p id='lineup'>Latest Temperature: " + obj.latest_temperature + "°C</p>");
				$('#stats' + obj.sensor + '').append("<p id='lineup'>Latest Humidity: " + obj.latest_humidity + "%</p><br>");
				$('#stats' + obj.sensor + '').append("<p id='lineup'>Average Temperature: " + obj.avg_temperature + "°C</p>");
				$('#stats' + obj.sensor + '').append("<p id='lineup'>Average Humidity: " + obj.avg_humidity + "%</p><br>");
				$('#stats' + obj.sensor + '').append("<p id='lineup'>Lowest Recorded Temperature: " + obj.min_temperature + "°C</p>");
				$('#stats' + obj.sensor + '').append("<p id='lineup'>Highest Recorded Temperature: " + obj.max_temperature + "°C</p><br>");
				$('#stats' + obj.sensor + '').append("<p id='lineup'>Lowest Recorded Humidity: " + obj.min_humidity + "%</p>");
				$('#stats' + obj.sensor + '').append("<p id='lineup'>Highest Recorded Humidity: " + obj.max_humidity + "%</p><br>");
				$('#stats' + obj.sensor + '').append("<p id='lineup'>Started Recording: " + obj.first_datetime + "</p>");
				$('#stats' + obj.sensor + '').append("<p id='lineup'>Latest Recording: " + obj.latest_datetime + "</p>");
				
				function drawChart() {
			
					// JSONP request
					var jsonData = $.ajax({
					url: './api.php?t=lt_temps&sens=' + obj.sensor + '&hours=48',
					dataType: "json",
					}).done(function (results) {
			
					var data = new google.visualization.DataTable();
			
					data.addColumn('datetime', 'Time');
					data.addColumn('number', 'Temp (°C)');
					data.addColumn('number', 'Humidity (%)');
			
					$.each(results, function (i, row) {
						data.addRow([
						(new Date(row.datetime)),
						parseFloat(row.temperature),
						parseFloat(row.humidity)
						]);
					});
			
					var chart = new google.visualization.LineChart($('#chart' + obj.sensor + '').get(0));
			
					chart.draw(data, {
						//width: 800,
						//height: 240,
						colors: ['#ff0000', '#0000cc'],
						//lineWidth: 5,
						chartArea: {  width: "75%" },
						title: '' + obj.sensor + ' - Last 48 Hours'
					});
			
					});
			
				}

				google.charts.load('current', {'packages':['corechart']});
				google.charts.setOnLoadCallback(drawChart);
			});
			
		}
		});
		
	});
	</script>
	
	<div id="cont"></div>
	
<?php } ?>
    <div id="bottompadding"></div>

</body>
</html>