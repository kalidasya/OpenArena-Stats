<?php

require_once('config.php');
require_once('functions.php');

$logfiles   = scandir($config['logdir']);
$size       = 0;
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

                        $statistics = setIdForNickname($id, $nickname, $statistics, $config);
                    break;
                        
                    case 'item':
                        list($playerId, $item) = explode(' ', trim($stats));

                        $statistics = addItem($playerId, $item, $statistics, $config);
                    break;

                    case 'kill':
                        list($killerId, $victimId, $weaponId) = explode(' ', trim($stats));

                        $statistics = addFrag($killerId, $victimId, $weaponId, $statistics, $config);
                    break;

                    case 'award':
                        list($playerId, $awardId)   = explode(' ', trim($stats));

                        $statistics = addAward($playerId, $awardId, $statistics, $config);
                    break;

                    case 'challenge':
                        list($playerId, $challengeId) = explode(' ', trim($stats));

                        $statistics = addChallenge($playerId, $challengeId, $statistics, $config);
                    break;

                    case 'playerscore':
                        break;

                    case 'score':
                        break;

                    case 'ctf':
                        list($playerId, $teamId, $eventId) = explode(' ', trim($stats));

                        $statistics = addFlagEvent($playerId, $teamId, $eventId, $statistics, $config);
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

foreach($statistics as $nickname => &$stats) {
    arsort($stats['flagevents']);
    arsort($stats['flagevents']['Red']);
    arsort($stats['flagevents']['Blue']);

    arsort($stats['awards']);

    arsort($stats['enemies']);

    arsort($stats['victims']);

    arsort($stats['weapons']);
    
    arsort($stats['challenges']);

    arsort($stats['items']);
}

echo '<pre>'. print_r($statistics, 1) .'</pre>';

$endtime    = microtime(true);
$totaltime  = $endtime - $starttime;

echo 'Parsed '. count($logfiles) .' files ('. number_format(($size / 1048576), 2) .' MB) in '. number_format($totaltime, 2) .' seconds.';
