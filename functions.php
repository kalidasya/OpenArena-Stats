<?php

function setIdForNickname($id, $nickname, $statistics, $config, $date) {
    if (isset($config['players'][$nickname])) {
        if (!isset($statistics[$config['players'][$nickname]])) {
            $statistics[$config['players'][$nickname]] = array();
        }

        $statistics[$config['players'][$nickname]]['id'] = $id;

        if (!isset($statistics[$config['players'][$nickname]]['nicknames'])) {
            $statistics[$config['players'][$nickname]]['nicknames'] = array();
        }

        if (!in_array($nickname, $statistics[$config['players'][$nickname]]['nicknames'])) {
            $statistics[$config['players'][$nickname]]['nicknames'][] = $nickname;
        }
    }
    else {
        if (!isset($statistics[$nickname])) {
            $statistics[$nickname] = array();
        }

        $statistics[$nickname]['id'] = $id;
    }

    if (!isset($statistics[$config['players'][$nickname]][$date])) {
        $statistics[$config['players'][$nickname]][$date] = array();
    }

    return $statistics;
}

function addItem($playerId, $item, $statistics, $config, $date) {
    foreach($statistics as $nickname => &$stats) {
        if($stats['id'] == $playerId) {
            if(!isset($stats[$date]['items'])) {
                $stats[$date]['items'] = array();
            }

            if (!isset($stats[$date]['items'][$config['items'][$item]])) {
                $stats[$date]['items'][$config['items'][$item]] = 0;
            }

            $stats[$date]['items'][$config['items'][$item]]++;
        }
    }

    return $statistics;
}

function addFrag($killerId, $victimId, $weaponId, $statistics, $config, $date) {
    foreach($statistics as $nickname => &$stats) {
        if ($killerId != 1022 && $stats['id'] == $killerId) {
            $killerName = $nickname;

            if (!isset($stats[$date]['weapons'])) {
                $stats[$date]['weapons'] = array();
            }

            if (!isset($stats[$date]['weapons'][$config['weapons'][$weaponId]])) {
                $stats[$date]['weapons'][$config['weapons'][$weaponId]] = 0;
            }

            $stats[$date]['weapons'][$config['weapons'][$weaponId]]++;
        }

        if ($stats['id'] == $victimId) {
            $victimName = $nickname;
        }
    }

    if($killerId != 1022) {
        if (!isset($statistics[$killerName][$date]['victims'][$victimName])) {
            $statistics[$killerName][$date]['victims'][$victimName] = 0;
        }

        if (!isset($statistics[$victimName][$date]['enemies'][$killerName])) {
            $statistics[$victimName][$date]['enemies'][$killerName] = 0;
        }

        if (!isset($statistics[$killerName][$date]['kills'])) {
            $statistics[$killerName][$date]['kills'] = 0;
        }

        if (!isset($statistics[$victimName][$date]['deaths'])) {
            $statistics[$victimName][$date]['deaths'] = 0;
        }

        $statistics[$killerName][$date]['victims'][$victimName]++;
        $statistics[$killerName][$date]['kills']++;
        $statistics[$victimName][$date]['enemies'][$killerName]++;
        $statistics[$victimName][$date]['deaths']++;
    } else {
        if (!isset($statistics[$victimName][$date]['suicides'])) {
            $statistics[$victimName][$date]['suicides'] = 0;
        }

        $statistics[$victimName][$date]['suicides']++;
    }

    return $statistics;
}

function addAward($playerId, $awardId, $statistics, $config, $date) {
    foreach($statistics as $nickname => &$stats) {
        if($stats['id'] == $playerId) {
            if(!isset($stats[$date]['awards'])) {
                $stats[$date]['awards'] = array();
            }

            if (!isset($stats[$date]['awards'][$config['awards'][$awardId]])) {
                $stats[$date]['awards'][$config['awards'][$awardId]] = 0;
            }

            $stats[$date]['awards'][$config['awards'][$awardId]]++;
        }
    }

    return $statistics;
}

function addChallenge($playerId, $challengeId, $statistics, $config, $date) {
    foreach($statistics as $nickname => &$stats) {
        if($stats['id'] == $playerId) {
            if(!isset($stats[$date]['challenges'])) {
                $stats[$date]['challenges'] = array();
            }

            if (!isset($stats[$date]['challenges'][$config['challenges'][$challengeId]])) {
                $stats[$date]['challenges'][$config['challenges'][$challengeId]] = 0;
            }

            $stats[$date]['challenges'][$config['challenges'][$challengeId]]++;
        }
    }

    return $statistics;
}

function addFlagEvent($playerId, $teamId, $eventId, $statistics, $config, $date) {
    foreach($statistics as $nickname => &$stats) {
        if($stats['id'] == $playerId) {
            if(!isset($stats[$date]['flagevents'])) {
                $stats[$date]['flagevents'] = array();
            }

            if(!isset($stats[$date]['flagevents'][$config['ctfteams'][$teamId]])) {
                $stats[$date]['flagevents'][$config['ctfteams'][$teamId]] = array();
            }


            if (!isset($stats[$date]['flagevents'][$config['ctfteams'][$teamId]][$config['ctf'][$eventId]])) {
                $stats[$date]['flagevents'][$config['ctfteams'][$teamId]][$config['ctf'][$eventId]] = 0;
            }

            $stats[$date]['flagevents'][$config['ctfteams'][$teamId]][$config['ctf'][$eventId]]++;
        }
    }

    return $statistics;
}

function addMap($mapname, $maps, $config, $date) {
    if ( !isset($maps[$mapname]) ) {
        $maps[$mapname] = array( 'dates' => array(), 'victim_heatmap' => array(), 'killer_heatmap' => array() );
    }
/*
    if ( !isset($maps[$mapname]['dates'][$date]) ) {
        $maps[$mapname]['dates'][$date] = 1;
    } else {
        ++$maps[$mapname]['dates'][$date];
    }
 */
    return $maps;
}

function addMapKill($mapname, $coordinates, $line, $maps, $config, $date) {
    list($victim, $killer) = explode( ' ', $coordinates );
    list($x,$y,$height) = explode(';', $victim );

    if ( $x != '' && $y != '' ) {
        $maps[$mapname]['victim_heatmap'][] = array( 'x' => $x, 'y' => $y);
    }

    list($kx,$ky,$kheight) = explode(';', $killer);

    if ( $kx != '' && $ky != '' && $kx != 0 && $ky != 0 && $kheight != 0 ) {
	$maps[$mapname]['killer_heatmap'][] = array( 'x' => $kx, 'y' => $ky);
    } 
    
    return $maps;
}
