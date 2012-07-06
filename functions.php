<?php

function setIdForNickname($id, $nickname, $statistics, $config) {
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

    return $statistics;
}

function addItem($playerId, $item, $statistics, $config) {
    foreach($statistics as $nickname => &$stats) {
        if($stats['id'] == $playerId) {
            if(!isset($stats['items'])) {
                $stats['items'] = array();
            }

            if (!isset($stats['items'][$config['items'][$item]])) {
                $stats['items'][$config['items'][$item]] = 0;
            }

            $stats['items'][$config['items'][$item]]++;
        }
    }

    return $statistics;
}

function addFrag($killerId, $victimId, $weaponId, $statistics, $config) {
    foreach($statistics as $nickname => &$stats) {
        if ($killerId != 1022 && $stats['id'] == $killerId) {
            $killerName = $nickname;

            if (!isset($stats['weapons'])) {
                $stats['weapons'] = array();
            }

            if (!isset($stats['weapons'][$config['weapons'][$weaponId]])) {
                $stats['weapons'][$config['weapons'][$weaponId]] = 0;
            }

            $stats['weapons'][$config['weapons'][$weaponId]]++;
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

function addAward($playerId, $awardId, $statistics, $config) {
    foreach($statistics as $nickname => &$stats) {
        if($stats['id'] == $playerId) {
            if(!isset($stats['awards'])) {
                $stats['awards'] = array();
            }

            if (!isset($stats['awards'][$config['awards'][$awardId]])) {
                $stats['awards'][$config['awards'][$awardId]] = 0;
            }

            $stats['awards'][$config['awards'][$awardId]]++;
        }
    }

    return $statistics;
}

function addChallenge($playerId, $challengeId, $statistics, $config) {
    foreach($statistics as $nickname => &$stats) {
        if($stats['id'] == $playerId) {
            if(!isset($stats['challenges'])) {
                $stats['challenges'] = array();
            }

            if (!isset($stats['challenges'][$config['challenges'][$challengeId]])) {
                $stats['challenges'][$config['challenges'][$challengeId]] = 0;
            }

            $stats['challenges'][$config['challenges'][$challengeId]]++;
        }
    }

    return $statistics;
}

function addFlagEvent($playerId, $teamId, $eventId, $statistics, $config) {
    foreach($statistics as $nickname => &$stats) {
        if($stats['id'] == $playerId) {
            if(!isset($stats['flagevents'])) {
                $stats['flagevents'] = array();
            }

            if(!isset($stats['flagevents'][$config['ctfteams'][$teamId]])) {
                $stats['flagevents'][$config['ctfteams'][$teamId]] = array();
            }


            if (!isset($stats['flagevents'][$config['ctfteams'][$teamId]][$config['ctf'][$eventId]])) {
                $stats['flagevents'][$config['ctfteams'][$teamId]][$config['ctf'][$eventId]] = 0;
            }

            $stats['flagevents'][$config['ctfteams'][$teamId]][$config['ctf'][$eventId]]++;
        }
    }

    return $statistics;
}
