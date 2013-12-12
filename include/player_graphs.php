<div class="row-fluid">
	<div class="span6">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Stats</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<script>
                            google.load("visualization", "1", {packages:["corechart"]});
	   						google.setOnLoadCallback(drawChart);

	   						function drawChart() {
	                        	var data = new google.visualization.DataTable();
								data.addColumn('date', 'Date');
								data.addColumn('number', 'Kills');
								data.addColumn('number', 'Deaths');
	                            var options = {
	                            	title: 'User stats'
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
					<div id="chart_div" style="width: 900px; height: 500px;"></div></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>