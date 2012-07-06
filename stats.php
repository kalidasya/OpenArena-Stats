<?php

require_once('config.php');

$logfiles   = scandir($config['logdir']);

$kill_pattern       = '/(?P<killer>[a-zA-Z0-9-_\ ]+)\ killed\ (?P<victim>[a-zA-Z0-9-_\ ]+)\ by\ (?P<weapon>[A-Z_]+)/';
$taken_pattern      = '/(?P<player>[a-zA-Z0-9-_\ ]+)\ got\ the\ (RED|BLUE)\ flag/';
$capture_pattern    = '/(?P<player>[a-zA-Z0-9-_\ ]+)\ captured\ the\ (RED|BLUE)\ flag/';
$return_pattern     = '/(?P<player>[a-zA-Z0-9-_\ ]+)\ returned\ the\ (RED|BLUE)\ flag/';
$frag_pattern       = '/(?P<player>[a-zA-Z0-9-_\ ]+)\ fragged\ (RED|BLUE)\'s\ flag\ carrier/';
$award_pattern      = '/(?P<player>[a-zA-Z0-9-_\ ]+)\ gained\ the\ (?P<award>[A-Z]+)\ award/';
$challenge_pattern  = '/Client\ (?P<playerId>[0-9]+)\ got\ award\  (?P<challenge>[0-9]+)/';

$size   = 0;
$statistics = array();

$starttime  = microtime(true);

// Loop through all returned logfiles
foreach($logfiles as $log) {

    // Strip out the "." and ".." folders
    if (!in_array($log, array('.', '..'))) {

        // Open the current logfile
        $handle = fopen($config['logdir'] .'/'. $log, 'r');

        // Walk through all lines in the logfile
        while(($line = fgets($handle, 4096)) !== false) {

            // Filter lines
            if (strpos($line, ':')) {

                // Split the line on timestamp and type
                list($timestamp, $info) = explode('|', trim($line));
                list($type, $stats)     = explode(':', trim($info));

                // Loop through the different types
                switch(strtolower($type)) {
                    case 'clientuserinfochanged':
                        list($id, $rest)        = explode(' ', trim($stats));
                        list($dump, $nickname)  = explode('\\', $rest);

                        $statistics = setIdForNickname($id, $nickname, $statistics);
                    break;
                        
                    case 'item':
                        list($playerId, $item) = explode(' ', trim($stats));
                    break;

                    case 'kill':
                        list($killerId, $victimId, $weaponId) = explode(' ', trim($stats));

                        $statistics = addFrag($killerId, $victimId, $weaponId, $statistics);
                    break;

                    case 'award':
                        list($playerId, $awardId)   = explode(' ', trim($stats));
                    break;

                    case 'challenge':
                        list($playerId, $challengeId) = explode(' ', trim($stats));
                    break;

                    case 'playerscore':
                        list($playerId, $points) = explode(' ', trim($stats));
                    break;

                    case 'score':
                        break;

                    case 'ctf':
                        list($playerId, $teamId, $eventId) = explode(' ', trim($stats));
                    break;

                    default:
                        break;
                }

            }

        }

        fclose($handle);
        $size += filesize($config['logdir'] .'/'. $log);

    }

}

echo '<pre>'. print_r($statistics, 1) .'</pre>';

function setIdForNickname($id, $nickname, $statistics) {
    if (!isset($statistics[$nickname])) {
        $statistics[$nickname] = array();
    }

    $statistics[$nickname]['id'] = $id;

    return $statistics;
}

function addFrag($killerId, $victimId, $weaponId, $statistics) {
    foreach($statistics as $nickname => $stats) {
        if ($killerId != 1022 && $stats['id'] == $killerId) {
            $killerName = $nickname;
        }

        if ($stats['id'] == $victimId) {
            $victimName = $nickname;
        }
    }

    if($killerId != 1022) {
        if (!isset($statistics[$killerName]['victims'][$victimName])) {
            $statistics[$killerName]['victims'][$victimName] = 0;
        }

        if (!isset($statistics[$victimName]['enemies'][$killerName])) {
            $statistics[$victimName]['enemies'][$killerName] = 0;
        }

        if (!isset($statistics[$killerName]['kills'])) {
            $statistics[$killerName]['kills'] = 0;
        }

        if (!isset($statistics[$victimName]['deaths'])) {
            $statistics[$victimName]['deaths'] = 0;
        }

        $statistics[$killerName]['victims'][$victimName]++;
        $statistics[$killerName]['kills']++;
        $statistics[$victimName]['enemies'][$killerName]++;
        $statistics[$victimName]['deaths']++;
    } else {
        if (!isset($statistics[$victimName]['suicides'])) {
            $statistics[$victimName]['suicides'] = 0;
        }

        $statistics[$victimName]['suicides']++;
    }

    return $statistics;
}

$endtime    = microtime(true);
$totaltime  = $endtime - $starttime;

echo 'Parsed '. count($logfiles) .' files ('. number_format(($size / 1048576), 2) .' MB) in '. number_format($totaltime, 2) .' seconds.';
