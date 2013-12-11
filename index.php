<?php
require_once 'include/config.php';

$link = mysqli_connect ( $config ['db'] ['host'], $config ['db'] ['user'], $config ['db'] ['pass'], $config ['db'] ['name'] );

$players_result = mysqli_query ( $link, "SELECT id, nickname FROM players ORDER BY nickname ASC" );

$is_overall = ! isset ( $_GET ['player'] ) || intval ( $_GET ['player'] ) <= 0;
$today = isset ( $_GET ['today'] ) && intval ( $_GET ['today'] ) == 1;
if ($today){
	$today_filter_a = " AND DATE(games_kills.time_of_kill) = DATE(NOW())";
	$today_filter_w = " WHERE DATE(games_kills.time_of_kill) = DATE(NOW())";
	$today_filter_ctf_a = " AND DATE(games_ctf.time_of_action) = DATE(NOW())";
	$today_filter_ctf_w = " WHERE DATE(games_ctf.time_of_action) = DATE(NOW())";
	$today_filter_award_a = " AND DATE(games_awards.time_of_award) = DATE(NOW())";
	$today_filter_award_w = " WHERE DATE(games_awards.time_of_award) = DATE(NOW())";
	$today_filter_challenges_a = " AND DATE(games_challenges.time_of_challenge) = DATE(NOW())";
	$today_filter_challenges_w = " WHERE DATE(games_challenges.time_of_challenge) = DATE(NOW())";
} else {
	$today_filter_a = "";
	$today_filter_w = "";
	$today_filter_ctf = "";
	$today_filter_award = "";
}

if ($is_overall) {
	
	// Overall statistics
	$killer_filter = "".$today_filter_a;
	$victim_filter = "".$today_filter_a;
	$ctf_filter = "".$today_filter_ctf_w;
	$awards_filter = "".$today_filter_award_w;
	$challenge_filter = "".$today_filter_challenges_w;
	$weapon_filter = "".$today_filter_w;
	
	$general_suicide_q = "SELECT COUNT(*) AS suicides FROM games_kills WHERE (games_kills.killer_id = 1022 OR games_kills.killer_id = games_kills.victim_id )".$today_filter_a;
	$general_stats_q = "SELECT SUM(1) as kills,
														 SUM(0) as deaths,
														 YEAR(time_of_kill) as year,
														 MONTH(time_of_kill) as month,
														 DAY(time_of_kill) as day
												  FROM games_kills
												  WHERE games_kills.killer_id !=1022
														AND games_kills.killer_id != games_kills.victim_id
												  GROUP BY YEAR(time_of_kill), MONTH(time_of_kill), DAY(time_of_kill)";
	
	$player ['name'] = 'Overall';
	$player ['nickname'] = '';
	$player ['last_seen'] = '';
} else {
	// Player specific statistics
	
	$killer_filter = " AND games_kills.killer_id = '" . mysqli_real_escape_string ( $link, $_GET ['player'] ) . "'".$today_filter_a;
	$victim_filter = " AND games_kills.victim_id = '" . mysqli_real_escape_string ( $link, $_GET ['player'] ) . "'".$today_filter_a;
	$ctf_filter = " WHERE games_ctf.player_id = '" . mysqli_real_escape_string ( $link, $_GET ['player'] ) . "'".$today_filter_ctf_a;
	$awards_filter = "WHERE games_awards.player_id = '" . mysqli_real_escape_string ( $link, $_GET ['player'] ) . "'".$today_filter_award_a;
	$challenge_filter = "WHERE games_challenges.player_id = '" . mysqli_real_escape_string ( $link, $_GET ['player'] ) . "'".$today_filter_challenges_a;
	$weapon_filter = "WHERE games_kills.killer_id = '" . mysqli_real_escape_string ( $link, $_GET ['player'] ) . "'".$today_filter_a;
	
	$general_suicide_q = "SELECT COUNT(*) AS suicides FROM games_kills WHERE ((games_kills.killer_id = 1022 AND games_kills.victim_id = '". mysqli_real_escape_string($link, $_GET['player']) ."') OR (games_kills.killer_id = games_kills.victim_id AND games_kills.killer_id = '". mysqli_real_escape_string($link, $_GET['player']) ."'))".$today_filter_a;
	
	$general_stats_q = "SELECT SUM(CASE WHEN killer_id =  '" . mysqli_real_escape_string ( $link, $_GET ['player'] ) . "' THEN 1 ELSE 0 END) as kills,
														 SUM(CASE WHEN victim_id =  '" . mysqli_real_escape_string ( $link, $_GET ['player'] ) . "' THEN 1 ELSE 0 END) as deaths,
														 YEAR(time_of_kill) as year, 
														 MONTH(time_of_kill) as month, 
														 DAY(time_of_kill) as day
												  FROM games_kills
												  WHERE games_kills.killer_id !=1022
														AND games_kills.killer_id != games_kills.victim_id
    													AND (killer_id ='" . mysqli_real_escape_string ( $link, $_GET ['player'] ) . "' OR victim_id='" . mysqli_real_escape_string ( $link, $_GET ['player'] ) . "')
												  GROUP BY YEAR(time_of_kill), MONTH(time_of_kill), DAY(time_of_kill)";
	
	$player_result  = mysqli_query($link, "SELECT id, name, nickname, last_seen FROM players WHERE players.id = '". mysqli_real_escape_string($link, $_GET['player']) ."' LIMIT 1");
	$player = mysqli_fetch_assoc($player_result);
}

//---------------------------------------------------------------------
// SQL QUERIES
//---------------------------------------------------------------------

// GENERAL
$general_kill_results = mysqli_query ( $link, sprintf ( "SELECT COUNT(*) AS kills FROM games_kills WHERE games_kills.killer_id != 1022 AND games_kills.killer_id != games_kills.victim_id AND games_kills.killer_team != games_kills.victim_team %s", $killer_filter ) );
$general_kill_row = mysqli_fetch_assoc ( $general_kill_results );
$general_teamkill_results = mysqli_query ( $link, sprintf ( "SELECT COUNT(*) AS kills FROM games_kills WHERE games_kills.killer_team = games_kills.victim_team %s", $killer_filter ) );
$general_teamkill_row = mysqli_fetch_assoc ( $general_teamkill_results );
$general_death_results = mysqli_query ( $link, sprintf ( "SELECT COUNT(*) AS deaths FROM games_kills WHERE games_kills.killer_id != 1022 AND games_kills.killer_id != games_kills.victim_id AND games_kills.killer_team != games_kills.victim_team AND games_kills.killer_team != 0 %s", $victim_filter ) );
$general_death_row = mysqli_fetch_assoc ( $general_death_results );
$general_teamdeath_results = mysqli_query ( $link, sprintf ( "SELECT COUNT(*) AS deaths FROM games_kills WHERE games_kills.killer_team = games_kills.victim_team %s", $victim_filter ) );
$general_teamdeath_row = mysqli_fetch_assoc ( $general_teamdeath_results );
$general_suicide_results = mysqli_query ( $link, $general_suicide_q );
$general_suicide_row = mysqli_fetch_assoc ( $general_suicide_results );
$general_stats_results = mysqli_query ( $link, $general_stats_q );

$general_stats_rows = mysqli_fetch_all ( $general_stats_results, MYSQLI_ASSOC );

if ($general_death_row ['deaths'] > 0) {
	$general_kd_ratio = number_format ( $general_kill_row ['kills'] / $general_death_row ['deaths'], 2 );
} else {
	$general_kd_ratio = '-';
}
$corrected_kills = $general_kill_row ['kills'] - $general_teamkill_row ['kills'];
$corrected_deaths = $general_death_row ['deaths'] + $general_suicide_row ['suicides'];
if ($corrected_deaths > 0) {
	$general_corrected_ratio = number_format ( $corrected_kills / $corrected_deaths, 2 );
} else {
	$general_corrected_ratio = '-';
}
 
// CTF
$ctf_stats = array ();
$ctf_results = mysqli_query ( $link, sprintf ( "SELECT flag, ctf_action_id, COUNT(*) AS times FROM games_ctf %s GROUP BY flag, ctf_action_id", $ctf_filter ) );
while ( $ctf_row = mysqli_fetch_assoc ( $ctf_results ) ) {
	$ctf_stats [$ctf_row ['flag']] [$ctf_row ['ctf_action_id']] = $ctf_row ['times'];
}

// KILLERS
$kill_results = mysqli_query ( $link, sprintf ( "SELECT players.id, players.nickname, COUNT(games_kills.id) AS kills FROM games_kills INNER JOIN players ON games_kills.killer_id = players.id WHERE games_kills.killer_id != 1022 AND games_kills.killer_id != games_kills.victim_id %s GROUP BY games_kills.killer_id ORDER BY kills DESC", $victim_filter ) );
$kill_rows = mysqli_fetch_all ( $kill_results, MYSQLI_ASSOC );

// VICTIMS
$death_results = mysqli_query ( $link, sprintf ( "SELECT players.id, players.nickname, COUNT(games_kills.id) AS deaths FROM games_kills INNER JOIN players ON games_kills.victim_id = players.id WHERE games_kills.killer_id != 1022 AND games_kills.killer_id != games_kills.victim_id %s GROUP BY games_kills.victim_id ORDER BY deaths DESC", $killer_filter ) );
$death_rows = mysqli_fetch_all ( $death_results, MYSQLI_ASSOC );

// AWARDS
$award_results = mysqli_query ( $link, sprintf ( "SELECT awards.name, awards.description, COUNT(games_awards.award_id) AS amount FROM games_awards INNER JOIN awards ON games_awards.award_id = awards.id %s GROUP BY games_awards.award_id ORDER BY amount DESC", $awards_filter ) );
$award_rows = mysqli_fetch_all ( $award_results, MYSQLI_ASSOC );

// CHALLENGES
$challenge_results = mysqli_query ( $link, sprintf ( "SELECT challenges.name, challenges.description, COUNT(games_challenges.challenge_id) AS amount FROM games_challenges INNER JOIN challenges ON games_challenges.challenge_id = challenges.id %s GROUP BY games_challenges.challenge_id ORDER BY amount DESC", $challenge_filter ) );
$challenge_rows = mysqli_fetch_all ( $challenge_results, MYSQLI_ASSOC );

// WEAPONS
$weapon_results = mysqli_query ( $link, sprintf("SELECT weapons.nicename, weapons.description, COUNT(games_kills.weapon_id) AS amount FROM games_kills INNER JOIN weapons ON games_kills.weapon_id = weapons.id %s GROUP BY games_kills.weapon_id ORDER BY amount DESC", $weapon_filter) );
$weapon_rows = mysqli_fetch_all ( $weapon_results, MYSQLI_ASSOC );


require_once('include/header.php');

require_once('include/overall.php');

require_once('include/footer.php');
