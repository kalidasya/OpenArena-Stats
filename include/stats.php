<?php

date_default_timezone_set('Europe/Amsterdam');

require_once('config.php');
require_once('functions.php');

if ($argv[1]){
	$logfile    = fopen($argv[1], 'r');
} else {
	$logfile    = fopen('games.log', 'r');
}

$start_time = microtime(true);

$link   = mysqli_connect($config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['name']);
mysqli_autocommit($link, TRUE);

$gametype     = NULL;
$team_counter = 0;

// Walk through all lines in the logfile
while(($line = fgets($logfile, 4096)) !== false) {

    $match  = array();
    preg_match('/(?P<time>[0-9]+\:[0-9]+) (?P<type>[a-z]+)\: (?P<rest>.*)/i', trim($line), $match);
    
    if (count($match) >= 3 || preg_match('/(?P<time>[0-9]+\:[0-9]+) ShutdownGame\:/i', trim($line), $shutdown)) {

        if (!empty($shutdown)) {
            $match['type'] = 'shutdowngame';
            $match['time'] = $shutdown['time'];
        }

        // Loop through the different types
        switch(strtolower($match['type'])) {
            case 'initgame':
                $info = explode_to_assoc(substr($match['rest'], 1), '\\');
                $starttime = $info['g_timestamp'];
                mysqli_query($link, "INSERT INTO games 
                                            SET map = '". mysqli_real_escape_string($link, $info['mapname']) ."', 
                                                gametype_id = '". mysqli_real_escape_string($link, $info['g_gametype']) ."', 
                                                capturelimit = '". mysqli_real_escape_string($link, $info['capturelimit']) ."', 
                                                fraglimit = '". mysqli_real_escape_string($link, $info['fraglimit']) ."', 
                                                timelimit = '". mysqli_real_escape_string($link, $info['timelimit']) ."', 
                                                starttime = '". mysqli_real_escape_string($link, $info['g_timestamp']) ."'");
                $gameid = mysqli_insert_id($link);
                $round_players = array();
                $gametype = $info['g_gametype'];
            break;

            case 'shutdowngame':
                if (!empty($round_players)) {

                    $participant = FALSE;
                    foreach($round_players as $player) {
                        if ($player['participate']) {
                            $participant = TRUE;
                            break;
                        }
                    }

                    if ($participant) {
                        $timeparts = explode(':', $match['time']);
                        $endtime = date('Y-m-d H:i:s', strtotime($starttime .'+'. $timeparts[0] .'minutes +'. $timeparts[1] .'seconds'));
                        mysqli_query($link, "UPDATE games SET endtime = '". mysqli_real_escape_string($link, $endtime) ."' WHERE id = '". mysqli_real_escape_string($link, $gameid) ."'");
                        mysqli_commit($link);
                    } else {
                        mysqli_rollback($link);
                    }
                } else {
                    mysqli_rollback($link);
                }
            break;

            case 'clientuserinfochanged':
                $playerid = substr($match['rest'], 0, strpos($match['rest'], ' '));
                $info = explode_to_assoc(substr($match['rest'], strpos($match['rest'], ' ')+1), '\\');
                if (!array_key_exists($playerid, $round_players)) {
                    $result = mysqli_query($link, "SELECT id FROM players WHERE oa_guid = '". mysqli_real_escape_string($link, $info['id']) ."' LIMIT 1");
                    if($result->num_rows == 0) {
                        $timeparts = explode(':', $match['time']);
                        $firstseen = date('Y-m-d H:i:s', strtotime($starttime .'+'. $timeparts[0] .'minutes +'. $timeparts[1] .'seconds'));
                        mysqli_query($link, "INSERT INTO players SET nickname = '". mysqli_real_escape_string($link, $info['n']) ."', 
                            oa_guid = '". mysqli_real_escape_string($link, $info['id']) ."', 
                            first_seen = '". mysqli_real_escape_string($link, $firstseen) ."', 
                            last_seen = '". mysqli_real_escape_string($link, $firstseen) ."'");
                        $local_player_id = mysqli_insert_id($link);
                    } else {
                        $player_row = $result->fetch_row();
                        $local_player_id = $player_row[0];
                    }
                    mysqli_query($link, "INSERT INTO nicknames SET player_id = ".$local_player_id.", nickname = '". mysqli_real_escape_string($link, $info['n']) ."'");
                    mysqli_query($link, "UPDATE players SET nickname = '". mysqli_real_escape_string($link, $info['n']) ."' WHERE id = '". mysqli_real_escape_string($link, $local_player_id) ."'");
                    $round_players[$playerid] = $info;
                    $round_players[$playerid]['localid'] = $local_player_id;
                    $round_players[$playerid]['participate'] = 0;
                }
                else {
                    $round_players[$playerid]['t'] = $info['t'];
                }

                if (!in_array($gametype, array(1,2,3,10))) {
                    // Non-team based game, put everyone in a different team
                    $round_players[$playerid]['t'] = $team_counter++;
                }
            break;

            case 'clientbegin':
                if (array_key_exists($match['rest'], $round_players)) {
                    $round_players[$match['rest']]['participate'] = 1;
                }
            break;

            case 'clientdisconnect':
                if (array_key_exists($match['rest'], $round_players)) {
                    $timeparts = explode(':', $match['time']);
                    $lastseen = date('Y-m-d H:i:s', strtotime($starttime .'+'. $timeparts[0] .'minutes +'. $timeparts[1] .'seconds'));
                    mysqli_query($link, "UPDATE players SET last_seen = '". mysqli_real_escape_string($link, $lastseen) ."' WHERE id = '". mysqli_real_escape_string($link, $round_players[$match['rest']]['localid']) ."'");
                }
            break;
                
            case 'item':
                preg_match('/^(?P<playerid>[0-9]+) (?P<itemname>.*)/i', trim($match['rest']), $item);
            break;

            case 'kill':
                preg_match('/^(?P<killerid>[0-9]+) (?P<victimid>[0-9]+) (?P<weaponid>[0-9]+)/i', trim($match['rest']), $kill);
                if ($kill['killerid'] == 1022) {
                    $killer = 1022;
                    $killer_team = '0';
                } else {
                    $killer = $round_players[$kill['killerid']]['localid'];
                    $killer_team = $round_players[$kill['killerid']]['t'];
                }
                $timeparts = explode(':', $match['time']);
                $time_of_kill = date('Y-m-d H:i:s', strtotime($starttime .'+'. $timeparts[0] .'minutes +'. $timeparts[1] .'seconds'));
                mysqli_query($link, "INSERT INTO games_kills SET game_id = '". mysqli_real_escape_string($link, $gameid) ."', 
                    killer_id = '". mysqli_real_escape_string($link, $killer) ."', 
                    killer_team = '". mysqli_real_escape_string($link, $killer_team) ."', 
                    victim_id = '". mysqli_real_escape_string($link, $round_players[$kill['victimid']]['localid']) ."', 
                    victim_team = '". mysqli_real_escape_string($link, $round_players[$kill['victimid']]['t']) ."', 
                    weapon_id = '". mysqli_real_escape_string($link, $kill['weaponid']) ."', 
                    time_of_kill = '". mysqli_real_escape_string($link, $time_of_kill) ."'");
            break;

            case 'award':
                preg_match('/^(?P<playerid>[0-9]+) (?P<awardid>[0-9]+)/i', trim($match['rest']), $award);
                $timeparts = explode(':', $match['time']);
                $time_of_award = date('Y-m-d H:i:s', strtotime($starttime .'+'. $timeparts[0] .'minutes +'. $timeparts[1] .'seconds'));
                mysqli_query($link, "INSERT INTO games_awards SET game_id = '". mysqli_real_escape_string($link, $gameid) ."', 
                    award_id = '". mysqli_real_escape_string($link, $award['awardid']) ."', 
                    player_id = '". mysqli_real_escape_string($link, $round_players[$award['playerid']]['localid']) ."',
                    time_of_award = '". mysqli_real_escape_string($link, $time_of_award) ."'");
            break;

            case 'challenge':
                preg_match('/^(?P<playerid>[0-9]+) (?P<challengeid>[0-9]+)/i', trim($match['rest']), $challenge);
                $timeparts = explode(':', $match['time']);
                $time_of_challenge = date('Y-m-d H:i:s', strtotime($starttime .'+'. $timeparts[0] .'minutes +'. $timeparts[1] .'seconds'));
                mysqli_query($link, "INSERT INTO games_challenges SET game_id = '". mysqli_real_escape_string($link, $gameid) ."', 
                    player_id = '". mysqli_real_escape_string($link, $round_players[$challenge['playerid']]['localid']) ."',
                    challenge_id = '". mysqli_real_escape_string($link, $challenge['challengeid']) ."', 
                    time_of_challenge = '". mysqli_real_escape_string($link, $time_of_challenge) ."'");
            break;

            case 'playerscore':
                preg_match('/^(?P<playerid>[0-9]+) (?P<points>[0-9]+)/i', trim($match['rest']), $playerscore);
            break;

            case 'score':
                preg_match('/^(?P<score>[0-9]+) .* client\: (?P<playerid>[0-9]+)/i', trim($match['rest']), $score);
            break;

            case 'ctf':
                preg_match('/^(?P<playerid>[0-9]+) (?P<flag>1|2) (?P<actiontype>[0-9]+)/i', trim($match['rest']), $ctf);
                $timeparts = explode(':', $match['time']);
                $time_of_action = date('Y-m-d H:i:s', strtotime($starttime .'+'. $timeparts[0] .'minutes +'. $timeparts[1] .'seconds'));
                mysqli_query($link, "INSERT INTO games_ctf SET game_id = '". mysqli_real_escape_string($link, $gameid) ."', 
                    player_id = '". mysqli_real_escape_string($link, $round_players[$ctf['playerid']]['localid']) ."', 
                    flag = '". mysqli_real_escape_string($link, $ctf['flag']) ."', 
                    ctf_action_id = '". mysqli_real_escape_string($link, $ctf['actiontype']) ."', 
                    time_of_action = '". mysqli_real_escape_string($link, $time_of_action) ."'");
            break;

            default:
            break;
        }

    }

}

fclose($logfile);
mysqli_close($link);

if ($logfile == 'games.log'){
	if (!copy('games.log', 'logfiles/'. time() .'.log')) {
	    echo 'FAILED TO COPY FILE.'.PHP_EOL;
	} else {
	    $logfile = fopen('games.log', 'w');
	    ftruncate($logfile, 0);
	    fclose($logfile);
	}
}

$endtime    = microtime(true);
$totaltime  = $endtime - $start_time;

echo 'Parsed in ' .$totaltime. ' seconds'. PHP_EOL;
