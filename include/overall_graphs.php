<div class="row-fluid">
	<script>
    	var overallData = [];
		<?php
	    	$prev_id = "";
			echo "overallData = [";
			foreach ( $general_stats_rows as $stats ) {
				if($prev_id == ""){
					echo "{'player':{'id':".$stats['id'].", 'name':'".$stats['name']."'}, 'scores': [\n";
					$prev_id =  $stats['id'];
				} elseif($prev_id != $stats['id']){
					echo "]},\n";
					echo "{'player':{'id':".$stats['id'].", 'name':'".$stats['name']."'}, 'scores': [\n";
					$prev_id =  $stats['id'];
				} 
				echo "{'date':'".$stats ['year'] . "-" . $stats ['month'] . "-" . $stats ['day']."', 'kills': ".$stats['kills'].", 'deaths': ".$stats['deaths']."},\n";
			}
			echo "]}];"
		?>
		google.load("visualization", "1", {packages:["corechart"]});
		google.setOnLoadCallback(drawChart);
		function drawChart() {
            var data_kill = new google.visualization.DataTable();
            var data_death = new google.visualization.DataTable();
            data_kill.addColumn('date', 'Date', 'date');
            data_death.addColumn('date', 'Date', 'date');

            var options = {
                title: 'User stats',
                chartArea: {left:5,top:5,width:"80%",height:"90%"},
                vAxis: {maxValue: 10, textPosition: "out", slantedText: true},
                titlePosition:"in"
            };
            var colIds = [];
            overallData.forEach(function(val, i, a){
		    	data_kill.addColumn('number',val.player.name,val.player.id);
				data_death.addColumn('number',val.player.name,val.player.id);
				colIds[val.player.id]=i+1;
			});
            overallData.forEach(function(val, i, a){
                                
			val.scores.forEach(function(val2, i2, a2){
                var kill_row = data_kill.getFilteredRows([{column:0, value:new Date(val2.date+" 01:00:00")}])
                if(kill_row.length===0){
                    data_kill.addRow();
                    kill_row[0] = data_kill.getNumberOfRows()-1;
                }
                var death_row = data_death.getFilteredRows([{column:0, value:new Date(val2.date+" 01:00:00")}]);
                if(death_row.length===0){
                    data_death.addRow();
                    death_row[0] = data_death.getNumberOfRows()-1;;
                }
				var colid = colIds[val.player.id];
				data_kill.setCell(kill_row[0], 0, new Date(val2.date+" 01:00:00"));
				data_kill.setCell(kill_row[0], colid, val2.kills);

				data_death.setCell(death_row[0], 0, new Date(val2.date+" 01:00:00"));
				data_death.setCell(death_row[0], colid, val2.deaths);
			});
								
         });
         options.title="Kills";
		 var chart_kills = new google.visualization.LineChart(document.getElementById('chart_kills'));
		 chart_kills.draw(data_kill, options);
         
         options.title="Deaths";
		 var chart_deaths = new google.visualization.LineChart(document.getElementById('chart_deaths'));
		 chart_deaths.draw(data_death, options);
       }
	</script>
    <div class="span5">
	    <div id="chart_kills" style="height: 300px;"></div>
    </div>
    <div class="span5">
	    <div id="chart_deaths" style="height: 300px;"></div>
    </div>
</div>
