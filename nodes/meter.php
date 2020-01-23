<?php
$thisYear = date('Y');
$lastYear = (date('Y')-1);
$lastLastYear = (date('Y')-2);

$meterClass = new Meters;
$meterClass->meterUID = $_GET['meterUID'];
$meter = $meterClass->getOne();	

$location = $db->where("uid", $meter['location']);
$location = $db->getOne("locations");	

$readingsClass = new Readings;
$readingsClass->meterUID = $meter['uid'];

$readingsAll = $readingsClass->readingsByMeter(20);

$consumptionThisYear = $readingsClass->consumptionByMeterByYear($thisYear);
$consumptionLastYear = $readingsClass->consumptionByMeterByYear($lastYear);
$consumptionLastLastYear = $readingsClass->consumptionByMeterByYear($lastLastYear);

?>

<div class="container">
	<div class="row">
		<div class="col-sm">
			<h3><?php echo $meter['name']; ?> <small class="text-muted">(<?php echo $location['name'];?>)</small></h3>
			<p class="lead">Billed to tenant: <?php echo $meter['billed'];?></p>
		</div>
		<div class="col-sm">
			<p class="text-right"><?php echo $meter['type'] . " meter serial: " . $meter['serial']; ?></p>
		</div>
	</div>
	<canvas id="canvas" width="400" height="200"></canvas>
	<div class="btn-group float-right" role="group" aria-label="Basic example">
		<a href="index.php?n=meter_edit&meterUID=<?php echo $meter['uid'];?>" class="btn btn-sm btn-outline-secondary">Edit</a>
		<a href="#" class="btn btn-sm btn-outline-secondary" id="link2" download="chart.png">Export as Image</a>
	</div>
	<h3>Consumption <?php echo $thisYear . ": " . array_sum($consumptionThisYear) . $meterClass->thisMeterUnits() . " <i>(~£" . round((array_sum($consumptionThisYear) * 0.14)) . ")</i>";?><br />
	Consumption <?php echo $lastYear . ": " . array_sum($consumptionLastYear) . $meterClass->thisMeterUnits() . " <i>(~£" . round((array_sum($consumptionLastYear) * 0.14)) . ")</i>";?><br />
	Consumption <?php echo $lastLastYear . ": " . array_sum($consumptionLastLastYear) . $meterClass->thisMeterUnits() . " <i>(~£" . round((array_sum($consumptionLastLastYear) * 0.14)) . ")</i>";?></h3>
	<div class="row">
		<?php
		foreach ($readingsAll AS $reading) {
			$readingLabelsArray[] = "'" . $reading['date'] . "'";
			$readingArray[] = $reading['reading1'];
		}
		?>
		
		<form role="form" id="contactForm" class="form-inline" data-toggle="validator" class="shake">
		<input type="hidden" id="meter" value="<?php echo $meter['uid'];?>">
		<div class="alert alert-danger display-error" style="display: none"></div>
		<table id="readingsTable" class="table table-bordered table-striped" >
		<tbody>
			<tr>
				<td width="50%">Date</td>
				<td width="50%">Reading</td>
			</tr>
			<tr>
				<td><input type="text" id="date" class="form-control" value="<?php echo date('Y-m-d H:i', time()); ?>" placeholder="Date"></td>
				<td>
					<div class="input-group">
						<input type="text" class="form-control" id="reading" placeholder="Reading">
						<div class="input-group-append">
							<button type="submit" id="submit" class="btn btn-success"><span>&#10003;</span>
						</div>
					</div>
					
					
					
</button></td>
			</tr>
			<?php
			foreach ($readingsAll AS $reading) {
				$output  = "<tr>";
				$output .= "<td>" . date('Y-m-d H:i', strtotime($reading['date'])) . "</td>";
				$output .= "<td>" . $reading['reading1'] . "<a href=\"#\" id=\"" . $reading['uid'] . "\" class=\"badge badge-pill badge-light float-right readingDelete\">x</span>" . "</td>";
				$output .= "</tr>";
				echo $output;
			}
			?>
		</tbody>
		</table>
		</form>
	</div>
</div>

<script type="text/javascript">
  $(document).ready(function() {

      $('#submit').click(function(e){
        e.preventDefault();
    
        var date = $("#date").val();
        var reading = $("#reading").val();
        var meter = $("#meter").val();

        $.ajax({
            type: "POST",
            url: "/actions/reading_add.php",
            dataType: "json",
            data: {date:date, reading:reading, meter:meter},
            success : function(data){
                if (data.code == "200"){
                    //alert("Success: " +data.msg);
                   $('#readingsTable > tbody > tr:eq(1)').after('<tr><td>' + date + '</td><td>' + reading + '</td></tr>');
                } else {
                    $(".display-error").html("<ul>"+data.msg+"</ul>");
                    $(".display-error").css("display","block");
                }
            }
        });
      });

  });
</script>

<script>
<?php
if ($meter['type'] == "Gas"){
	$colourThis = $colour_gas_year1;
	$colourLast = $colour_gas_year2;
	$colourLastLast = $colour_gas_year3;
} else if ($meter['type'] == "Electric") {
	$colourThis = $colour_electric_year1;
	$colourLast = $colour_electric_year2;
	$colourLastLast = $colour_electric_year3;
}
?>
var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
var color = Chart.helpers.color;
var barChartData = {
	labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
	datasets: [{
		label: '<?php echo $meter['type'] . ": " . $lastLastYear;?>',
		backgroundColor: color(window.chartColors.<?php echo $colourLastLast; ?>).alpha(0.5).rgbString(),
		borderColor: window.chartColors.<?php echo $colourLastLast; ?>,
		borderWidth: 1,
		data: [<?php echo implode($consumptionLastLastYear, ", ");?>]
	}, {
		label: '<?php echo $meter['type'] . ": " . $lastYear;?>',
		backgroundColor: color(window.chartColors.<?php echo $colourLast; ?>).alpha(0.5).rgbString(),
		borderColor: window.chartColors.<?php echo $colourLast; ?>,
		borderWidth: 1,
		data: [<?php echo implode($consumptionLastYear, ", ");?>]
	}, {
		label: '<?php echo $meter['type'] . ": " . $thisYear;?>',
		backgroundColor: color(window.chartColors.<?php echo $colourThis; ?>).alpha(0.5).rgbString(),
		borderColor: window.chartColors.<?php echo $colourThis; ?>,
		borderWidth: 1,
		data: [<?php echo implode($consumptionThisYear, ", ");?>]
	}]
};

window.onload = function() {
	var ctx = document.getElementById('canvas').getContext('2d');
	window.myBar = new Chart(ctx, {
		type: 'bar',
		data: barChartData,
		options: {
			responsive: true,
			legend: {
				position: 'top',
			},
			animation: {
				duration: 1000,
				onComplete: done
			},
			scales: {
				yAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: '<?php echo str_replace("<sup>3</sup>", "3", $meterClass->thisMeterUnits()); ?>'
					}
				}]
			}
		},
	});
};

function done(){
	var url=myBar.toBase64Image();
	document.getElementById("link2").href=url;

	//var url_base64jp = document.getElementById("canvas").toDataURL("image/jpg");
	//link2.href = url_base64;
	//document.getElementById("link2").href=url;
}
</script>

<script>
$(".readingDelete").click(function() {
	var r=confirm("Warning!  Are you sure you want to delete this reading?  This cannot be undone!");
	
	if (r==true) {
		var thisObject = $(this);
		var uid = $(thisObject).attr('id');
		
		var url = 'actions/reading_delete.php';
		
		// perform the post to the action (take the info and submit to database)
		$.post(url,{
		    uid: uid
		}, function(data){
			$(thisObject).parent().parent().fadeOut();
		},'html');
	} else {
	}
	
	return false;

});


</script>
