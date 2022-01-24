<!DOCTYPE html>
<html lang="en">
<head>
    <title>Enbolt MySQL Tool</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css" rel="stylesheet" />
    <!--  Plugin for DataTables.net  -->
    <script src="{{asset('/adminTheme/js/jquery.datatables.js') }}"></script>
    <style type="text/css">
        .text-enbolt{
            color: #2D44AC;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row" style="margin-top:10px;">
            <div class="col-md-8">
                <h2 class="text-enbolt" style="margin-top:0px;"><span class="text-enbolt">E</span>nbolt <span class="text-enbolt">M</span>ySQL <span class="text-enbolt">T</span>ool</h2>
            </div>
            <div class="col-md-4">
                <div class="graph pull-right">
                    <a href="{{ route('emt.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <div class="row">
        	<div class="col-md-12">
    			<nav aria-label="breadcrumb">
				  	<ol class="breadcrumb">
				    	<li class="breadcrumb-item"><a href="{{ route('emt.index') }}">Home</a></li>
				    	<li class="breadcrumb-item active" aria-current="page">MySQL Statistics</li>
				  	</ol>
				</nav>
        	</div>
        </div>
        <div class="row">
        	<div class="col-md-12">
        		<div class="panel panel-default">
				  	<div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
				  		        <div id="chart_div" style="width: 100%; height: 100%;"></div>
                            </div>
                        </div>
				  	</div>
				</div>
        	</div>
        </div>
    </div>
</body>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['gauge']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

        var data = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['Memory', 80],
            ['CPU', 55],
            ['Network', 68]
        ]);

        var options = {
            width: 400, height: 120,
            redFrom: 90, redTo: 100,
            yellowFrom:75, yellowTo: 90,
            minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('chart_div'));

        chart.draw(data, options);

        setInterval(function() {
            data.setValue(0, 1, 40 + Math.round(60 * Math.random()));
            chart.draw(data, options);
        }, 13000);
        setInterval(function() {
            data.setValue(1, 1, 40 + Math.round(60 * Math.random()));
            chart.draw(data, options);
        }, 5000);
        setInterval(function() {
            data.setValue(2, 1, 60 + Math.round(20 * Math.random()));
            chart.draw(data, options);
        }, 26000);
    }
</script>
</html>
