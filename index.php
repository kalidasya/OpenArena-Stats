<?php

require_once 'include/config.php';

$link = mysqli_connect($config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['name']);

$players_result = mysqli_query($link, "SELECT id, nickname FROM players ORDER BY nickname ASC");

if (!isset($_GET['player']) || intval($_GET['player']) <= 0) {
    // Overall statistics
    $pagefile = 'overall';
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
        $general_kd_ratio = number_format($general_kill_row['kills'] / $general_death_row['deaths'], 2);
    } else {
        $general_kd_ratio = '-';
    }
    $corrected_kills = $general_kill_row['kills'] - $general_teamkill_row['kills'];
    $corrected_deaths = $general_death_row['deaths'] + $general_suicide_row['suicides'];
    if ( $corrected_deaths > 0 ) {
        $general_kd_ratio = number_format($corrected_kills / $corrected_deaths, 2);
    } else {
        $general_corrected_ratio = '-';
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
    $pagefile = 'player';
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
        $general_kd_ratio = number_format($general_kill_row['kills'] / $general_death_row['deaths'], 2);
    } else {
        $general_kd_ratio = '-';
    }
    $corrected_kills = $general_kill_row['kills'] - $general_teamkill_row['kills'];
    $corrected_deaths = $general_death_row['deaths'] + $general_suicide_row['suicides'];
    if ( $corrected_deaths > 0 ) {
        $general_kd_ratio = number_format($corrected_kills / $corrected_deaths, 2);
    } else {
        $general_corrected_ratio = '-';
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

require_once('include/header.php');

require_once('include/'. $pagefile .'.php');

require_once('include/footer.php');
