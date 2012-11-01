        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span3">
                    <?php require_once('include/sidebar.php'); ?>
                </div>
                <div class="span9">
                    <div class="page-header">
                        <h1><?php echo $player['name']; ?> <small><?php echo $player['nickname']; ?></small></h1>
                    </div>

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
                                        <td>Ratio (average)</td>
                                        <td><?php echo $general_ratio; ?></td>
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
                                        <td><?php echo $kill['nickname']; ?></td>
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
                                        <td><?php echo $death['nickname']; ?></td>
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
