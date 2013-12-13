<div class="row-fluid">
    <script>
        google.load("visualization", "1", {packages:["corechart"]});
        google.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('date', 'Date');
            data.addColumn('number', 'Kills');
            data.addColumn('number', 'Deaths');
            var options = {
               title: 'User stats',
               chartArea: {left:40,top:30,width:"80%",height:"80%"} 
            };
            <?php
                foreach ( $general_stats_rows as $stats ) {
                    echo "data.addRow([new Date(" . $stats ['year'] . "," . $stats ['month'] . "," . $stats ['day'] . "), " . $stats ['kills'] . ", " . $stats ['deaths'] . "]);";
                }
            ?>  
            var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
    </script>
	<div id="chart_div" class="span12" style="height: 300px;"></div>
</div>
