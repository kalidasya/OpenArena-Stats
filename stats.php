<?php

require_once('config.php');
require_once('functions.php');

$logfiles   = scandir($config['logdir']);
$size       = 0;
$players    = array();
$maps       = array();
$currentmap = '';
$dates      = array();
$starttime  = microtime(true);

// Loop through all returned logfiles
foreach($logfiles as $log) {

    // Strip out the "." and ".." folders
    if (!in_array($log, array('.', '..'))) {

        preg_match('/openarena\_([0-9-]+)\.log/', $log, $date);
        $date = $date[1];
        $dates[] = $date;

        // Open the current logfile
        $handle = fopen($config['logdir'] .'/'. $log, 'r');

        // Walk through all lines in the logfile
        while(($line = fgets($handle, 4096)) !== false) {

            // Split the line on timestamp and type
            list($timestamp, $info, $coordinates) = explode('|', trim($line));

            // Filter lines
            if (strpos($line, ':')) {

                list($type, $stats)     = explode(':', trim($info));

                // Loop through the different types
                switch(strtolower($type)) {
                    case 'clientuserinfochanged':
                        list($id, $rest)        = explode(' ', trim($stats));
                        list($dump, $nickname)  = explode('\\', $rest);

                        $players = setIdForNickname($id, $nickname, $players, $config, $date);
                    break;
                        
                    case 'item':
                        list($playerId, $item) = explode(' ', trim($stats));

                        $players = addItem($playerId, $item, $players, $config, $date);
                    break;

                    case 'kill':
                        list($killerId, $victimId, $weaponId) = explode(' ', trim($stats));

                        $players = addFrag($killerId, $victimId, $weaponId, $players, $config, $date);

                        $maps = addMapKill($currentmap, $coordinates, $line, $maps, $config, $date );
                    break;

                    case 'award':
                        list($playerId, $awardId)   = explode(' ', trim($stats));

                        $players = addAward($playerId, $awardId, $players, $config, $date);
                    break;

                    case 'challenge':
                        list($playerId, $challengeId) = explode(' ', trim($stats));

                        $players = addChallenge($playerId, $challengeId, $players, $config, $date);
                    break;

                    case 'playerscore':
                        break;

                    case 'score':
                        break;

                    case 'ctf':
                        list($playerId, $teamId, $eventId) = explode(' ', trim($stats));

                        $players = addFlagEvent($playerId, $teamId, $eventId, $players, $config, $date);
                    break;

                    default:
                        break;
                }

            } else {
                if ( substr($info, 0, 12) == 'loaded maps/' ) {
                    list( $mapname, $ext) = explode( '.', substr($info, 12) );
                    $maps = addMap( $mapname, $maps, $maps, $config, $date );
                    $currentmap = $mapname;
                }
            }

        }

        fclose($handle);
        $size += filesize($config['logdir'] .'/'. $log);

    }

}

foreach($players as $nickname => &$info) {
    foreach($info as $key => &$stats) {
        if (is_array($stats) && $key != 'nicknames') {
            @arsort($stats['flagevents']);
            @arsort($stats['flagevents']['Red']);
            @arsort($stats['flagevents']['Blue']);

            @arsort($stats['awards']);

            @arsort($stats['enemies']);

            @arsort($stats['victims']);

            @arsort($stats['weapons']);
            
            @arsort($stats['challenges']);

            @arsort($stats['items']);
        }
    }
}

$endtime    = microtime(true);
$totaltime  = $endtime - $starttime;

$statistics             = array();
$statistics['players']  = $players;
$statistics['dates']    = $dates;
$statistics['parsed']   = 'Parsed '. (count($logfiles) - 2) .' files ('. number_format(($size / 1048576), 2) .' MB) in '. number_format($totaltime, 2) .' seconds.';

//echo '<pre>' .print_r($statistics, true). '</pre>';
