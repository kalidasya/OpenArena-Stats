<?php

date_default_timezone_set('Europe/Amsterdam');
require_once('stats.php');

function createDateRangeArray($strDateFrom, $strDateTo) {
    $aryRange     = array();
    $iDateFrom    = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
    $iDateTo      = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

    if ($iDateTo >= $iDateFrom) {
        array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry

        while ($iDateFrom < $iDateTo) {
            $iDateFrom += 86400; // add 24 hours
            array_push($aryRange, date('Y-m-d', $iDateFrom));
        }
    }

    return $aryRange;
}

$selectedPlayer = (isset($_GET['player']) ? $_GET['player'] : 'Overall');
$selectedFrom   = (isset($_GET['from']) ? $_GET['from'] : $statistics['dates'][0]);
$selectedUntil  = (isset($_GET['until']) ? $_GET['until'] : $statistics['dates'][(count($statistics['dates']) - 1)]);

$dateRange      = createDateRangeArray($selectedFrom, $selectedUntil);

$performanceChartData      = '';
$ratioChartData      = '';
$prevRatio      = 0;
$totalKills     = 0;
$totalDeaths    = 0;
$totalSuicides  = 0;
$redFlagCarriersKilled  = 0;
$redFlagsTaken          = 0;
$redFlagsCaptured       = 0;
$redFlagsReturned       = 0;
$blueFlagCarriersKilled  = 0;
$blueFlagsTaken          = 0;
$blueFlagsCaptured       = 0;
$blueFlagsReturned       = 0;
$killers    = array();
$victims    = array();
$items      = array();
$weapons    = array();
$awards     = array();
foreach ($dateRange as $date) {
    list($year, $month, $day) = explode('-', $date);
    $dailyKills     = 0;
    $dailyDeaths    = 0;
    $dailySuicides  = 0;
    $dailyRatio     = 0;
    foreach($statistics['players'] as $player => $stats) {
        if (($selectedPlayer == 'Overall' || $player == $selectedPlayer) && isset($stats[$date])) {

            $kills      = (isset($stats[$date]['kills']) ? $stats[$date]['kills'] : 0);
            $deaths     = (isset($stats[$date]['deaths']) ? $stats[$date]['deaths'] : 0);
            $suicides   = (isset($stats[$date]['suicides']) ? $stats[$date]['suicides'] : 0);
            $ratio      = ($deaths > 0 ? number_format(($kills / $deaths), 2) : $prevRatio);

            if ($selectedPlayer != 'Overall') {
                $performanceChartData  .= 'performancedata.addRow([new Date('. $year .', '. ($month - 1) .', '. $day .'), '. $kills .', '. $deaths .', '. $suicides .']);'. PHP_EOL;
                $ratioChartData  .= 'ratiodata.addRow([new Date('. $year .', '. ($month - 1) .', '. $day .'), '. $ratio .']);'. PHP_EOL;
            }

            $prevRatio      = $ratio;
            $totalKills     += $kills; 
            $totalDeaths    += $deaths; 
            $totalSuicides  += $suicides;

            $dailyKills     += $kills;
            $dailyDeaths    += $deaths;
            $dailySuicides  += $suicides;

            $redFlagsTaken          += (isset($stats[$date]['flagevents']['Red']['Flag taken']) ? $stats[$date]['flagevents']['Red']['Flag taken'] : 0);
            $redFlagsCaptured       += (isset($stats[$date]['flagevents']['Red']['Flag captured']) ? $stats[$date]['flagevents']['Red']['Flag captured'] : 0);
            $redFlagsReturned       += (isset($stats[$date]['flagevents']['Red']['Flag returned']) ? $stats[$date]['flagevents']['Red']['Flag returned'] : 0);
            $redFlagCarriersKilled  += (isset($stats[$date]['flagevents']['Red']['Flagcarrier killed']) ? $stats[$date]['flagevents']['Red']['Flagcarrier killed'] : 0);

            $blueFlagsTaken         += (isset($stats[$date]['flagevents']['Blue']['Flag taken']) ? $stats[$date]['flagevents']['Blue']['Flag taken'] : 0);
            $blueFlagsCaptured      += (isset($stats[$date]['flagevents']['Blue']['Flag captured']) ? $stats[$date]['flagevents']['Blue']['Flag captured'] : 0);
            $blueFlagsReturned      += (isset($stats[$date]['flagevents']['Blue']['Flag returned']) ? $stats[$date]['flagevents']['Blue']['Flag returned'] : 0);
            $blueFlagCarriersKilled += (isset($stats[$date]['flagevents']['Blue']['Flagcarrier killed']) ? $stats[$date]['flagevents']['Blue']['Flagcarrier killed'] : 0);

            if (isset($stats[$date]['enemies'])) {
                foreach($stats[$date]['enemies'] as $enemy => $kills) {
                    if (!isset($killers[$enemy])) {
                        $killers[$enemy] = 0;
                    }
                    $killers[$enemy] += $kills;
                }
            }

            if (isset($stats[$date]['victims'])) {
                foreach($stats[$date]['victims'] as $victim => $deaths) {
                    if (!isset($victims[$victim])) {
                        $victims[$victim] = 0;
                    }
                    $victims[$victim] += $deaths;
                }
            }

            if (isset($stats[$date]['awards'])) {
                foreach($stats[$date]['awards'] as $award => $awarded) {
                    if (!isset($awards[$award])) {
                        $awards[$award] = 0;
                    }
                    $awards[$award] += $awarded;
                }
            }

            if (isset($stats[$date]['items'])) {
                foreach($stats[$date]['items'] as $item => $amount) {
                    if (!isset($items[$item])) {
                        $items[$item] = 0;
                    }
                    $items[$item] += $amount;
                }
            }

            if (isset($stats[$date]['weapons'])) {
                foreach($stats[$date]['weapons'] as $weapon => $kills) {
                    if (!isset($weapons[$weapon])) {
                        $weapons[$weapon] = 0;
                    }
                    $weapons[$weapon] += $kills;
                }
            }
        }
    }
    if ($selectedPlayer == 'Overall') {
        $dailyRatio = @number_format(($dailyKills / $dailyDeaths), 2);
        $performanceChartData  .= 'performancedata.addRow([new Date('. $year .', '. ($month - 1) .', '. $day .'), '. $dailyKills .', '. $dailyDeaths .', '. $dailySuicides .']);'. PHP_EOL;
        $ratioChartData  .= 'ratiodata.addRow([new Date('. $year .', '. ($month - 1) .', '. $day .'), '. $dailyRatio .']);'. PHP_EOL;
    }
}
$performanceChartData  = substr($performanceChartData, 0, -2);
$ratioChartData  = substr($ratioChartData, 0, -2);
arsort($killers);
arsort($victims);
arsort($awards);
arsort($items);
arsort($weapons);

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>OpenArena Statistics</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="/css/bootstrap.css" rel="stylesheet">
        <link href="/css/style.css" rel="stylesheet">
        <style type="text/css">
            body {
                padding-top: 60px;
                padding-bottom: 40px;
            }
            .sidebar-nav {
                padding: 9px 0;
            }
        </style>
        <link href="/css/bootstrap-responsive.css" rel="stylesheet">
        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
    </head>

    <body>

        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="brand" href="#">OpenArena Statistics</a>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span3">
                    <!--
                    <form class="well form-horizontal">
                        <div class="control-group">
                            <label class="control-label" for="from-date">From:</label>
                            <div class="controls">
                                <select id="from-date" name="from-date">
                                    <option value="alltime">Alltime</option>
                                    <?php foreach($statistics['dates'] as $key => $date) : ?>
                                        <?php if ($date == $selectedFrom) : ?>
                                            <option selected="selected" value="<?php echo $date; ?>"><?php echo $date; ?></option>
                                        <?php else : ?>
                                            <option value="<?php echo $date; ?>"><?php echo $date; ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="until-date">Until:</label>
                            <div class="controls">
                                <select id="until-date" name="until-date">
                                    <option value="alltime">Alltime</option>
                                    <?php foreach($statistics['dates'] as $key => $date) : ?>
                                        <?php if ($date == $selectedUntil) : ?>
                                            <option selected="selected" value="<?php echo $date; ?>"><?php echo $date; ?></option>
                                        <?php else : ?>
                                            <option value="<?php echo $date; ?>"><?php echo $date; ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <input type="submit" class="btn btn-primary" value="Submit">
                        </div>
                    </form>
                    -->
                    <div class="well sidebar-nav">
                        <ul class="nav nav-list">
                            <li class="nav-header">Players</li>
                            <?php if ($selectedPlayer == 'Overall') : ?>
                            <li class="active"><a href="/">Overall</a></li>
                            <?php else : ?>
                            <li><a href="/">Overall</a></li>
                            <?php endif; ?>
                            <?php foreach($statistics['players'] as $player => $stats) : ?>
                                <?php if ($player == $selectedPlayer) : ?>
                                    <li class="active"><a href="/<?php echo $player; ?>"><?php echo $player; ?></a></li>
                                <?php else : ?>
                                    <li><a href="/<?php echo $player; ?>"><?php echo $player; ?></a></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="span9">
                    <div class="page-header">
                        <h1><?php echo $selectedPlayer; ?><small><?php echo (isset($statistics['players'][$selectedPlayer]['nicknames']) ? ' '.implode(', ', $statistics['players'][$selectedPlayer]['nicknames']) : ''); ?></small></h1>
                    </div>
                    <div class="row-fluid">
                        <div class="span12" id="performance_chart"></div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12" id="ratio_chart"></div>
                    </div>

                    <hr>

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
                                        <td><?php echo $totalKills; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Deaths</td>
                                        <td><?php echo $totalDeaths; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Suicides</td>
                                        <td><?php echo $totalSuicides; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Ratio (average)</td>
                                        <td><?php echo @number_format(($totalKills / $totalDeaths), 2); ?></td>
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
                                        <td><?php echo $blueFlagsTaken; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Flags captured</td>
                                        <td><?php echo $blueFlagsCaptured; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Flags returned</td>
                                        <td><?php echo $blueFlagsReturned; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Flagcarriers killed</td>
                                        <td><?php echo $blueFlagCarriersKilled; ?></td>
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
                                        <td><?php echo $redFlagsTaken; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Flags captured</td>
                                        <td><?php echo $redFlagsCaptured; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Flags returned</td>
                                        <td><?php echo $redFlagsReturned; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Flagcarriers killed</td>
                                        <td><?php echo $redFlagCarriersKilled; ?></td>
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
                                    <?php foreach($killers as $killer => $amount) : ?>
                                        <tr>
                                            <td><?php echo $killer; ?></td>
                                            <td><?php echo $amount; ?></td>
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
                                <tbody>
                                    <?php foreach($victims as $victim => $amount) : ?>
                                        <tr>
                                            <td><?php echo $victim; ?></td>
                                            <td><?php echo $amount; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
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
                                    <?php foreach($awards as $award => $amount) : ?>
                                        <tr>
                                            <td><?php echo $award; ?></td>
                                            <td><?php echo $amount; ?></td>
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
                                        <th colspan="2">Items</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($items as $item => $amount) : ?>
                                        <tr>
                                            <td><?php echo $item; ?></td>
                                            <td><?php echo $amount; ?></td>
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
                                    <?php foreach($weapons as $weapon => $amount) : ?>
                                        <tr>
                                            <td><?php echo $weapon; ?></td>
                                            <td><?php echo $amount; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">
            google.load("visualization", "1", {packages:["corechart"]});
            google.setOnLoadCallback(drawCharts);

            function drawCharts() {
                var performancedata = new google.visualization.DataTable();
                performancedata.addColumn('datetime', 'Date');
                performancedata.addColumn('number', 'Kills');
                performancedata.addColumn('number', 'Deaths');
                performancedata.addColumn('number', 'Suicides');
                <?php echo $performanceChartData; ?>

                var performanceoptions = {
                    title: 'Performance',
                    lineWidth: 2,
                    pointSize: 3
                };

                var performancechart = new google.visualization.ScatterChart(document.getElementById('performance_chart'));
                performancechart.draw(performancedata, performanceoptions);

                var ratiodata = new google.visualization.DataTable();
                ratiodata.addColumn('datetime', 'Date');
                ratiodata.addColumn('number', 'Ratio');
                <?php echo $ratioChartData; ?>

                var ratiooptions = {
                    title: 'Ratio',
                    lineWidth: 2,
                    pointSize: 3
                };

                var ratiochart = new google.visualization.ScatterChart(document.getElementById('ratio_chart'));
                ratiochart.draw(ratiodata, ratiooptions);
            }
        </script>

    </body>
</html>
