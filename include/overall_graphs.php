<div class="row-fluid">
	<div class="span6">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Stats</th>
				</tr>
				<script>
				var overallData = [];
				<?php
				$prev_name = "";
				echo "overallData = [";
				foreach ( $general_stats_rows as $stats ) {
					if($prev_name == ""){
						echo "{'player':{'id':".$stats['id'].", 'name':'".$stats['name']."'}, 'scores': [\n"; 
					} elseif($prev_name != $stats['nickname']){
						echo "]},\n";
					} else {
						echo "{'date':'".$stats ['year'] . "-" . $stats ['month'] . "-" . $stats ['day']."', 'kills': ".$stats['kills'].", 'deaths': ".$stats['deats']."},\n";
					}
				}
				echo "];"
				?>
					google.load("visualization", "1", {packages:["corechart"]});
					google.setOnLoadCallback(drawChart);
						function drawChart() {
                        	var data_kill = new google.visualization.DataTable();
                        	var data_death = new google.visualization.DataTable();
                        	data_kill.addColumn('date', 'Date', 'date');
                        	data_death.addColumn('date', 'Date', 'date');

                            var options = {
                            	title: 'User stats'
                            };
                            overallData.forEach(function(val, i, a){
								data_kill.addColumn('string',val.player.name,val.player.id);
								data_death.addColumn('string',val.player.name,val.player.id);
								
                            });
                            overallData.forEach(function(val, i, a){
                                
								val.scores.forEach(function(val2, i2, a2){
									var rowId = 0;
									data_kill.addRow();
									data_death.addRow();
									
									data_kill.setCell(rowId, 'date', new Date(val2.date));
									data_kill.setCell(rowId, val.player.id, val2.kills);

									data_death.setCell(rowId, 'date', new Date(val2.date));
									data_death.setCell(rowId, val.player.id, val2.deaths);
									rowId++;
								});
								
                            });
							var chart_kills = new google.visualization.LineChart(document.getElementById('chart_kills'));
							chart_kills.draw(data_kill, options);

							var chart_deaths = new google.visualization.LineChart(document.getElementById('chart_deaths'));
							chart_deaths.draw(data_death, options);
                        }
				</script>
			</thead>
			<tbody>
				<tr>
					<td>
						<div id="chart_kills" style="width: 600px; height: 450px;"></div>
					</td>
					<td>
						<div id="chart_deaths" style="width: 600px; height: 450px;"></div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>