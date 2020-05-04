<!DOCTYPE HTML>
<html>
<head> 
<!-- Import graph util -->
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
<?php
// SET TIME-ZONE TO ANYWHERE
date_default_timezone_set('America/Los_Angeles');
// CREATE DATE DD-MM-YYYY
$_today = date('m\-d\-Y', time());
// PULL CSV FROM GITHUB
$lines =file('https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_daily_reports_us/'.$_today.'.csv');

//THIS WILL FAIL 99% TIME. NO DATA WILL BE PULLED
foreach($lines as $data)
{
list($State[],$Region[],$Update[],$Lat[], $Long[], $Confirmed[], $Deaths[], $Recovered[], $Active[], $FIPS[], $IncidentRate[], $PeopleTested[], $PeopleHospitalized[]) = explode(',',$data);
}

// IF IT ISN'T THE 0.0001%
if(strlen($State[0]) == null)
{
	$timeCache = time(); // CACHE INCASE IT IS MIDNIGHT
	$fileFound = false;  // WHILE LOOP VAR
	$abortMissionCount = 0; // YEAR 3000
	while(!$fileFound)
	{
		$timeCache = $timeCache - 60 * 60 * 24; // GO TO YESTERDAY
		$_today = date('m\-d\-Y', $timeCache);  // YESTERDAY'S DATE
		
		$lines =file('https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_daily_reports_us/'.$_today.'.csv');
		
		foreach($lines as $data)
		{
		list($State[],$Region[],$Update[],$Lat[], $Long[], $Confirmed[], $Deaths[], $Recovered[], $Active[], $FIPS[], $IncidentRate[], $PeopleTested[], $PeopleHospitalized[]) = explode(',',$data);
		}
		
		if(!($State[0] == null))
		{
				$fileFound = true;
		}
		
		$abortMissionCount++;
		if($abortMissionCount >= 10)
		{
				echo "DATA NOT FOUND!";
				die;
		}
		

	}
	
}
// USELESS, JUST USE $_today
$_date = $_today;
// INDEX OF CSV is 59 
$numram = 59;
?>
<style type="text/css">
        html, body {
            height: 100%;
            margin: 0;
			
        }

        #myDiv {
			position: fixed;
            min-height: 100%; 
			min-width: 100%; 
        }
    </style>
</head>
<body>
<div id="myDiv" ></div>
<span style="position: fixed; left: 5px; bottom: 5px;">Data is provided by <a href="https://coronavirus.jhu.edu/us-map">Johns Hopkins University</a>,</span>
</body>

<?php 
//CACHE STATE LIST INSTEAD OF OLD WAY OF RUNNING THIS 4X
//05-03-2020 WHY NOT ONE FOR LOOP?
$statelist = "";
$confirmedlist = "";
$recoveredlist = "";
$deathlist = "";
$testedlist = "";
for($offset=1; $offset < $numram; $offset++) 
{
	$statelist = $statelist."'".$State[$offset]."'";
	$confirmedlist = $confirmedlist."'".$Confirmed[$offset]."'";
	$recoveredlist = $recoveredlist."'".$Recovered[$offset]."'";
	$deathlist = $deathlist."'".$Deaths[$offset]."'";
	$testedlist = $testedlist."'".$PeopleTested[$offset]."'";
	
	if (($offset != ($numram - 1)))
	{	
		$statelist = $statelist.',';
		$confirmedlist = $confirmedlist.',';
		$recoveredlist = $recoveredlist.',';
		$deathlist = $deathlist.',';
		$testedlist = $testedlist.',';
	}
}

?>

<script>


var confirmed = {
  x: [<?php echo $confirmedlist; ?>],
  y: [<?php echo $statelist; ?>],
  name: 'Cases',
  orientation: 'h',
  marker: {
    color: 'rgba(0,0,255,0.6)',
    width: 1
  },
  type: 'bar'
};

var recovered = {
  x: [<?php echo $recoveredlist; ?>],
  y: [<?php echo $statelist; ?>],
  name: 'Recovered',
  orientation: 'h',
  type: 'bar',
  marker: {
    color: 'rgba(0,255,0,0.6)',
    width: 1
  }
};

var deaths = {
  x: [<?php echo $deathlist; ?>],
  y: [<?php echo $statelist; ?>],
  name: 'Deaths',
  orientation: 'h',
  type: 'bar',
  marker: {
    color: 'rgba(255,0,0,0.6)',
    width: 1
  }
};

var tests = {
  x: [<?php echo $testedlist; ?>],
  y: [<?php echo $statelist; ?>],
  name: 'Tested',
  orientation: 'h',
  type: 'bar',
  marker: {
    color: 'rgba(0,0,0,0.6)',
    width: 1
  }
};

var data = [deaths, recovered, confirmed, tests];

var layout = {
  title: 'COVID-19 BY STATE (<?php echo $_date;?>)',
  barmode: 'stack',
  dragmode: 'pan',
  xaxis:{
    rangemode: 'tozero'
  }
};

Plotly.newPlot('myDiv', data, layout, {scrollZoom: true, showlegend: true});

</script>
</html>                           