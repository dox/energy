<canvas id="canvas" width="400" height="200"></canvas>
<?php
foreach ($readingsAll AS $reading) {
	$readingLabelsArray[] = "'" . $reading['date_reading'] . "'";
	$readingTemp1Array[] = $reading['temp1'];
	$readingHumidity1Array[] = $reading['humidity1'];
}
?>

<script>
const ctx = document.getElementById('canvas').getContext('2d');
const data = {
    // Labels should be Date objects
    labels: [<?php echo implode($readingLabelsArray, ", ");?>],
    datasets: [{
        fill: false,
        label: 'Temperature',
        data: [<?php echo implode($readingTemp1Array, ", ");?>],
        borderColor: '#fe8b36',
        backgroundColor: '#fe8b36',
        lineTension: 0,
    }, {
        fill: false,
        label: 'Humidity',
        data: [<?php echo implode($readingHumidity1Array, ", ");?>],
        borderColor: '#111111',
        backgroundColor: '#111111',
        lineTension: 0,
    }]
}
const options = {
    type: 'line',
    data: data,
    options: {
        fill: false,
        responsive: true,
        scales: {
            xAxes: [{
                type: 'time',
                display: true,
                scaleLabel: {
                    display: true,
                    labelString: "Date",
                }
            }],
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                },
                display: true,
                scaleLabel: {
                    display: true,
                    labelString: "Other",
                }
            }]
        }
    }
}
const chart = new Chart(ctx, options);
	</script>