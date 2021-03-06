        <div class="container-fluid">
            <div class="row-fluid">
                 <div class="span2">
                    <?php require_once('include/sidebar.php'); ?>
                </div>
                <div class="span10">
                    <div class="page-header">
                        <h1><?php echo $player['name']; ?> <small><?php echo $player['nickname']; ?></small></h1>
						<?php if ($player['last_seen']){?>
							<p class="muted">Last seen: <?php echo $player['last_seen']; ?></p>
						<?php }?>
						<?php 
            				if ($player_nicknames){
				                echo "<p class=muted>Nicknames:</p>";
				                echo "<ul>";
				                foreach($player_nicknames as $key => $value){
				                    echo "<li class=muted>" . $value[0]. "</li>";
				                }
				                echo "</ul>";
							}
						?>
                    </div>
                    <?php
                        if ($is_overall) {
                            require_once('include/overall_graphs.php');
                        } else {
                            require_once('include/player_graphs.php');
                        }
                    ?>
                    <div class="row-fluid">
                        <div class="span4">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th colspan="2">General</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Kills</td>
                                        <td><?php echo $general_kill_row['kills']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Teamkills</td>
                                        <td><?php echo $general_teamkill_row['kills']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Deaths</td>
                                        <td><?php echo $general_death_row['deaths']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Teamdeaths</td>
                                        <td><?php echo $general_teamdeath_row['deaths']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Suicides</td>
                                        <td><?php echo $general_suicide_row['suicides']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>K/D Ratio</td>
                                        <td><?php echo $general_kd_ratio; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Corrected Ratio</td>
                                        <td><?php echo $general_corrected_ratio; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="span4">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th colspan="2">CTF (Red team)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Flags taken</td>
                                        <td><?php echo isset($ctf_stats[1][0]) ? $ctf_stats[1][0] : 0; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Flags captured</td>
                                        <td><?php echo isset($ctf_stats[1][1]) ? $ctf_stats[1][1] : 0; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Flags returned</td>
                                        <td><?php echo isset($ctf_stats[1][2]) ? $ctf_stats[1][2] : 0; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Flagcarriers killed</td>
                                        <td><?php echo isset($ctf_stats[1][3]) ? $ctf_stats[1][3] : 0; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="span4">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th colspan="2">CTF (Blue team)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Flags taken</td>
                                        <td><?php echo isset($ctf_stats[2][0]) ? $ctf_stats[2][0] : 0; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Flags captured</td>
                                        <td><?php echo isset($ctf_stats[2][1]) ? $ctf_stats[2][1] : 0; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Flags returned</td>
                                        <td><?php echo isset($ctf_stats[2][2]) ? $ctf_stats[2][2] : 0; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Flagcarriers killed</td>
                                        <td><?php echo isset($ctf_stats[2][3]) ? $ctf_stats[2][3] : 0; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row-fluid">
                        <div class="span4">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th colspan="2">Killers</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($kill_rows as $kill) : ?>
                                    <tr>
                                        <td><a href="?player=<?php echo $kill['id']; ?>"><?php echo $kill['nickname']; ?></a></td>
                                        <td><?php echo $kill['kills']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="span4">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th colspan="2">Victims</th>
                                    </tr>
                                </thead>
                                    <?php foreach ($death_rows as $death) : ?>
                                    <tr>
                                        <td><a href="?player=<?php echo $death['id']; ?>"><?php echo $death['nickname']; ?></a></td>
                                        <td><?php echo $death['deaths']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="span4">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th colspan="2">Awards</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($award_rows as $award) : ?>
                                    <tr>
                                        <td><?php echo $award['name']; ?></td>
                                        <td><?php echo $award['amount']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row-fluid">
                        <div class="span6">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th colspan="2">Challenges</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($challenge_rows as $challenge) : ?>
                                    <tr>
                                        <td><?php echo $challenge['name']; ?></td>
                                        <td><?php echo $challenge['amount']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="span6">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th colspan="2">Weapons</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($weapon_rows as $weapon) : ?>
                                    <tr>
                                        <td><?php echo $weapon['nicename']; ?></td>
                                        <td><?php echo $weapon['amount']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
