<?php

require_once 'include/config.php';

$link = mysqli_connect($config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['name']);

$players_result = mysqli_query($link, "SELECT id, nickname FROM players ORDER BY nickname ASC");

if (!isset($_GET['player']) || intval($_GET['player']) <= 0) {
    // Overall statistics
    $player['name'] = 'Overall';
    $player['nickname'] = '';

    // GENERAL
    $general_kill_results = mysqli_query($link, "SELECT COUNT(*) AS kills FROM games_kills WHERE games_kills.killer_id != 1022 AND games_kills.killer_id != games_kills.victim_id AND games_kills.killer_team != games_kills.victim_team");
    $general_kill_row = mysqli_fetch_assoc($general_kill_results);
    $general_teamkill_results = mysqli_query($link, "SELECT COUNT(*) AS kills FROM games_kills WHERE games_kills.killer_team = games_kills.victim_team");
    $general_teamkill_row = mysqli_fetch_assoc($general_teamkill_results);
    $general_death_results = mysqli_query($link, "SELECT COUNT(*) AS deaths FROM games_kills WHERE games_kills.killer_id != 1022 AND games_kills.killer_id != games_kills.victim_id AND games_kills.killer_team != games_kills.victim_team AND games_kills.killer_team != 0");
    $general_death_row = mysqli_fetch_assoc($general_death_results);
    $general_teamdeath_results = mysqli_query($link, "SELECT COUNT(*) AS deaths FROM games_kills WHERE games_kills.killer_team = games_kills.victim_team");
    $general_teamdeath_row = mysqli_fetch_assoc($general_teamdeath_results);
    $general_suicide_results = mysqli_query($link, "SELECT COUNT(*) AS suicides FROM games_kills WHERE games_kills.killer_id = 1022 OR games_kills.killer_id = games_kills.victim_id");
    $general_suicide_row = mysqli_fetch_assoc($general_suicide_results);
    if ($general_death_row['deaths'] > 0) {
        $general_ratio = number_format($general_kill_row['kills'] / $general_death_row['deaths'], 2);
    } else {
        $general_ratio = '-';
    }

    // CTF
    $ctf_stats = array();
    $ctf_results = mysqli_query($link, "SELECT flag, ctf_action_id, COUNT(*) AS times FROM games_ctf GROUP BY flag, ctf_action_id");
    while ($ctf_row = mysqli_fetch_assoc($ctf_results)) {
        $ctf_stats[$ctf_row['flag']][$ctf_row['ctf_action_id']] = $ctf_row['times'];
    }

    // KILLERS
    $kill_results = mysqli_query($link, "SELECT players.id, players.nickname, COUNT(games_kills.id) AS kills FROM games_kills INNER JOIN players ON games_kills.killer_id = players.id WHERE games_kills.killer_id != 1022 AND games_kills.killer_id != games_kills.victim_id GROUP BY games_kills.killer_id ORDER BY kills DESC");
    $kill_rows = mysqli_fetch_all($kill_results, MYSQLI_ASSOC);

    // VICTIMS
    $death_results = mysqli_query($link, "SELECT players.id, players.nickname, COUNT(games_kills.id) AS deaths FROM games_kills INNER JOIN players ON games_kills.victim_id = players.id WHERE games_kills.killer_id != 1022 AND games_kills.killer_id != games_kills.victim_id GROUP BY games_kills.victim_id ORDER BY deaths DESC");
    $death_rows = mysqli_fetch_all($death_results, MYSQLI_ASSOC);

    // AWARDS
    $award_results = mysqli_query($link, "SELECT awards.name, awards.description, COUNT(games_awards.award_id) AS amount FROM games_awards INNER JOIN awards ON games_awards.award_id = awards.id GROUP BY games_awards.award_id ORDER BY amount DESC");
    $award_rows = mysqli_fetch_all($award_results, MYSQLI_ASSOC);

    // CHALLENGES
    $challenge_results = mysqli_query($link, "SELECT challenges.name, challenges.description, COUNT(games_challenges.challenge_id) AS amount FROM games_challenges INNER JOIN challenges ON games_challenges.challenge_id = challenges.id GROUP BY games_challenges.challenge_id ORDER BY amount DESC");
    $challenge_rows = mysqli_fetch_all($challenge_results, MYSQLI_ASSOC);

    // WEAPONS
    $weapon_results = mysqli_query($link, "SELECT weapons.nicename, weapons.description, COUNT(games_kills.weapon_id) AS amount FROM games_kills INNER JOIN weapons ON games_kills.weapon_id = weapons.id GROUP BY games_kills.weapon_id ORDER BY amount DESC");
    $weapon_rows = mysqli_fetch_all($weapon_results, MYSQLI_ASSOC);
    
} else {
    // Player specific statistics
    $player_result  = mysqli_query($link, "SELECT id, name, nickname FROM players WHERE players.id = '". mysqli_real_escape_string($link, $_GET['player']) ."' LIMIT 1");
    $player = mysqli_fetch_assoc($player_result);

    // GENERAL
    $general_kill_results = mysqli_query($link, "SELECT COUNT(*) AS kills FROM games_kills WHERE games_kills.killer_id != 1022 AND games_kills.killer_id != games_kills.victim_id AND games_kills.killer_id = '". mysqli_real_escape_string($link, $_GET['player']) ."'");
    $general_kill_row = mysqli_fetch_assoc($general_kill_results);
    $general_teamkill_results = mysqli_query($link, "SELECT COUNT(*) AS kills FROM games_kills WHERE games_kills.killer_team = games_kills.victim_team AND games_kills.killer_id = '". mysqli_real_escape_string($link, $_GET['player']) ."'");
    $general_teamkill_row = mysqli_fetch_assoc($general_teamkill_results);
    $general_death_results = mysqli_query($link, "SELECT COUNT(*) AS deaths FROM games_kills WHERE games_kills.killer_id != 1022 AND games_kills.killer_id != games_kills.victim_id AND games_kills.victim_id = '". mysqli_real_escape_string($link, $_GET['player']) ."'");
    $general_death_row = mysqli_fetch_assoc($general_death_results);
    $general_teamdeath_results = mysqli_query($link, "SELECT COUNT(*) AS deaths FROM games_kills WHERE games_kills.killer_team = games_kills.victim_team AND games_kills.victim_id = '". mysqli_real_escape_string($link, $_GET['player']) ."'");
    $general_teamdeath_row = mysqli_fetch_assoc($general_teamdeath_results);
    $general_suicide_results = mysqli_query($link, "SELECT COUNT(*) AS suicides FROM games_kills WHERE (games_kills.killer_id = 1022 AND games_kills.victim_id = '". mysqli_real_escape_string($link, $_GET['player']) ."') OR (games_kills.killer_id = games_kills.victim_id AND games_kills.killer_id = '". mysqli_real_escape_string($link, $_GET['player']) ."')");
    $general_suicide_row = mysqli_fetch_assoc($general_suicide_results);
    if ($general_death_row['deaths'] > 0) {
        $general_ratio = number_format($general_kill_row['kills'] / $general_death_row['deaths'], 2);
    } else {
        $general_ratio = '-';
    }

    // CTF
    $ctf_stats = array();
    $ctf_results = mysqli_query($link, "SELECT flag, ctf_action_id, COUNT(*) AS times FROM games_ctf WHERE games_ctf.player_id = '". mysqli_real_escape_string($link, $_GET['player']) ."' GROUP BY flag, ctf_action_id");
    while ($ctf_row = mysqli_fetch_assoc($ctf_results)) {
        $ctf_stats[$ctf_row['flag']][$ctf_row['ctf_action_id']] = $ctf_row['times'];
    }

    // KILLERS
    $kill_results = mysqli_query($link, "SELECT players.id, players.nickname, COUNT(games_kills.id) AS kills FROM games_kills INNER JOIN players ON games_kills.killer_id = players.id WHERE games_kills.killer_id != 1022 AND games_kills.killer_id != games_kills.victim_id AND games_kills.victim_id = '". mysqli_real_escape_string($link, $_GET['player']) ."' GROUP BY games_kills.killer_id ORDER BY kills DESC");
    $kill_rows = mysqli_fetch_all($kill_results, MYSQLI_ASSOC);

    // VICTIMS
    $death_results = mysqli_query($link, "SELECT players.id, players.nickname, COUNT(games_kills.id) AS deaths FROM games_kills INNER JOIN players ON games_kills.victim_id = players.id WHERE games_kills.killer_id != 1022 AND games_kills.killer_id != games_kills.victim_id AND games_kills.killer_id = '". mysqli_real_escape_string($link, $_GET['player']) ."' GROUP BY games_kills.victim_id ORDER BY deaths DESC");
    $death_rows = mysqli_fetch_all($death_results, MYSQLI_ASSOC);

    // AWARDS
    $award_results = mysqli_query($link, "SELECT awards.name, awards.description, COUNT(games_awards.award_id) AS amount FROM games_awards INNER JOIN awards ON games_awards.award_id = awards.id WHERE games_awards.player_id = '". mysqli_real_escape_string($link, $_GET['player']) ."' GROUP BY games_awards.award_id ORDER BY amount DESC");
    $award_rows = mysqli_fetch_all($award_results, MYSQLI_ASSOC);

    // CHALLENGES
    $challenge_results = mysqli_query($link, "SELECT challenges.name, challenges.description, COUNT(games_challenges.challenge_id) AS amount FROM games_challenges INNER JOIN challenges ON games_challenges.challenge_id = challenges.id WHERE games_challenges.player_id = '". mysqli_real_escape_string($link, $_GET['player']) ."'  GROUP BY games_challenges.challenge_id ORDER BY amount DESC");
    $challenge_rows = mysqli_fetch_all($challenge_results, MYSQLI_ASSOC);

    // WEAPONS
    $weapon_results = mysqli_query($link, "SELECT weapons.nicename, weapons.description, COUNT(games_kills.weapon_id) AS amount FROM games_kills INNER JOIN weapons ON games_kills.weapon_id = weapons.id WHERE games_kills.killer_id = '". mysqli_real_escape_string($link, $_GET['player']) ."' GROUP BY games_kills.weapon_id ORDER BY amount DESC");
    $weapon_rows = mysqli_fetch_all($weapon_results, MYSQLI_ASSOC);
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>OpenArena Statistics</title>
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
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="until-date">Until:</label>
                            <div class="controls">
                                <select id="until-date" name="until-date">
                                    <option value="alltime">Alltime</option>
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
                            <?php if (!isset($_GET['player'])) : ?>
                            <li class="active"><a href="/">Overall</a></li>
                            <?php else : ?>
                            <li><a href="/">Overall</a></li>
                            <?php endif; ?>
                            <?php while ($player_row = mysqli_fetch_array($players_result, MYSQLI_ASSOC)) : ?>
                                <?php if (isset($_GET['player']) && $player_row['id'] == $_GET['player']) : ?>
                                    <li class="active"><a href="?player=<?php echo $player_row['id']; ?>"><?php echo $player_row['nickname']; ?></a></li>
                                <?php else : ?>
                                    <li><a href="?player=<?php echo $player_row['id']; ?>"><?php echo $player_row['nickname']; ?></a></li>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        </ul>
                    </div>
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

        <script type="text/javascript" src="https://www.google.com/jsapi"></script>

    </body>
</html>
