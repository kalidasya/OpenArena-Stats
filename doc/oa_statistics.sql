# ************************************************************
# Sequel Pro SQL dump
# Version 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.5.27)
# Database: oa_statistics
# Generation Time: 2012-11-01 15:26:32 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table awards
# ------------------------------------------------------------

DROP TABLE IF EXISTS `awards`;

CREATE TABLE `awards` (
  `id` bigint(8) unsigned NOT NULL,
  `name` varchar(20) NOT NULL DEFAULT '',
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `awards` WRITE;
/*!40000 ALTER TABLE `awards` DISABLE KEYS */;

INSERT INTO `awards` (`id`, `name`, `description`)
VALUES
	(0,'Gauntlet','Awarded when the player successfully frags someone with the gauntlet.'),
	(1,'Excellent','Given when the player gains two frags within two seconds.The server admin may replace its in-game behavior with custom \"Multikills\" messages; however, the \"Excellent\" medals will be stored after the match anyway.'),
	(2,'Impressive','Given when the player achieves two consecutive hits with the railgun.'),
	(3,'Defense','The \"Defend\" or \"Defense\" medal is achieved when you kill an enemy that was inside your base, or that was hitting a team-mate of yours that was carrying the flag.'),
	(4,'Capture','Achieved when the player scores capturing the flag.'),
	(5,'Assist','Achieved when you return your flag within ten seconds before a teammate of yours makes a capture.');

/*!40000 ALTER TABLE `awards` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table challenges
# ------------------------------------------------------------

DROP TABLE IF EXISTS `challenges`;

CREATE TABLE `challenges` (
  `id` bigint(8) unsigned NOT NULL,
  `name` varchar(40) NOT NULL DEFAULT '',
  `nicename` varchar(40) DEFAULT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `challenges` WRITE;
/*!40000 ALTER TABLE `challenges` DISABLE KEYS */;

INSERT INTO `challenges` (`id`, `name`, `nicename`, `description`)
VALUES
	(0,'GENERAL_TEST',NULL,NULL),
	(1,'GENERAL_TOTALKILLS',NULL,NULL),
	(2,'GENERAL_TOTALDEATHS',NULL,NULL),
	(3,'GENERAL_TOTALGAMES',NULL,NULL),
	(101,'GAMETYPES_FFA_WINS',NULL,NULL),
	(102,'GAMETYPES_TOURNEY_WINS',NULL,NULL),
	(103,'GAMETYPES_TDM_WINS',NULL,NULL),
	(104,'GAMETYPES_CTF_WINS',NULL,NULL),
	(105,'GAMETYPES_1FCTF_WINS',NULL,NULL),
	(106,'GAMETYPES_OVERLOAD_WINS',NULL,NULL),
	(107,'GAMETYPES_HARVESTER_WINS',NULL,NULL),
	(108,'GAMETYPES_ELIMINATION_WINS',NULL,NULL),
	(109,'GAMETYPES_CTF_ELIMINATION_WINS',NULL,NULL),
	(110,'GAMETYPES_LMS_WINS',NULL,NULL),
	(111,'GAMETYPES_DD_WINS',NULL,NULL),
	(112,'GAMETYPES_DOM_WINS',NULL,NULL),
	(201,'WEAPON_GAUNTLET_KILLS',NULL,NULL),
	(202,'WEAPON_MACHINEGUN_KILLS',NULL,NULL),
	(203,'WEAPON_SHOTGUN_KILLS',NULL,NULL),
	(204,'WEAPON_GRENADE_KILLS',NULL,NULL),
	(205,'WEAPON_ROCKET_KILLS',NULL,NULL),
	(206,'WEAPON_LIGHTNING_KILLS',NULL,NULL),
	(207,'WEAPON_PLASMA_KILLS',NULL,NULL),
	(208,'WEAPON_RAIL_KILLS',NULL,NULL),
	(209,'WEAPON_BFG_KILLS',NULL,NULL),
	(210,'WEAPON_GRAPPLE_KILLS',NULL,NULL),
	(211,'WEAPON_CHAINGUN_KILLS',NULL,NULL),
	(212,'WEAPON_NAILGUN_KILLS',NULL,NULL),
	(213,'WEAPON_MINE_KILLS',NULL,NULL),
	(214,'WEAPON_PUSH_KILLS',NULL,NULL),
	(215,'WEAPON_INSTANT_RAIL_KILLS',NULL,NULL),
	(216,'WEAPON_TELEFRAG_KILLS',NULL,NULL),
	(217,'WEAPON_CRUSH_KILLS',NULL,NULL),
	(301,'AWARD_IMPRESSIVE',NULL,NULL),
	(302,'AWARD_EXCELLENT',NULL,NULL),
	(303,'AWARD_CAPTURE',NULL,NULL),
	(304,'AWARD_ASSIST',NULL,NULL),
	(305,'AWARD_DEFENCE',NULL,NULL),
	(401,'POWERUP_QUAD_KILL',NULL,NULL),
	(402,'POWERUP_SPEED_KILL',NULL,NULL),
	(403,'POWERUP_FLIGHT_KILL',NULL,NULL),
	(404,'POWERUP_INVIS_KILL',NULL,NULL),
	(405,'POWERUP_MULTI_KILL',NULL,NULL),
	(406,'POWERUP_COUNTER_QUAD',NULL,NULL),
	(407,'POWERUP_COUNTER_SPEED',NULL,NULL),
	(408,'POWERUP_COUNTER_FLIGHT',NULL,NULL),
	(409,'POWERUP_COUNTER_INVIS',NULL,NULL),
	(410,'POWERUP_COUNTER_ENVIR',NULL,NULL),
	(411,'POWERUP_COUNTER_REGEN',NULL,NULL),
	(412,'POWERUP_COUNTER_MULTI',NULL,NULL),
	(501,'FFA_TOP3',NULL,NULL),
	(502,'FFA_FROMBEHIND',NULL,NULL),
	(503,'FFA_BETTERTHAN',NULL,NULL),
	(504,'FFA_JUDGE',NULL,NULL),
	(505,'FFA_CHEAPKILLER',NULL,NULL);

/*!40000 ALTER TABLE `challenges` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ctf_actions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ctf_actions`;

CREATE TABLE `ctf_actions` (
  `id` bigint(8) unsigned NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `ctf_actions` WRITE;
/*!40000 ALTER TABLE `ctf_actions` DISABLE KEYS */;

INSERT INTO `ctf_actions` (`id`, `description`)
VALUES
	(0,'Flag taken'),
	(1,'Flag captured'),
	(2,'Flag returned'),
	(3,'Flagcarrier killed');

/*!40000 ALTER TABLE `ctf_actions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table games
# ------------------------------------------------------------

DROP TABLE IF EXISTS `games`;

CREATE TABLE `games` (
  `id` bigint(8) unsigned NOT NULL AUTO_INCREMENT,
  `map` varchar(40) NOT NULL DEFAULT '',
  `gametype_id` bigint(8) unsigned NOT NULL,
  `capturelimit` int(11) unsigned NOT NULL,
  `fraglimit` int(11) unsigned NOT NULL,
  `timelimit` int(11) unsigned NOT NULL,
  `starttime` datetime NOT NULL,
  `endtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Dump of table games_scores
# ------------------------------------------------------------

DROP TABLE IF EXISTS `games_scores`;

CREATE TABLE `games_scores` (
  `id` bigint(8) unsigned NOT NULL AUTO_INCREMENT,
  `game_id` bigint(8) unsigned NOT NULL,
  `player_id` bigint(8) unsigned NOT NULL,
  `score` int(11) unsigned NOT NULL,
  `ping` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table games_awards
# ------------------------------------------------------------

DROP TABLE IF EXISTS `games_awards`;

CREATE TABLE `games_awards` (
  `id` bigint(8) unsigned NOT NULL AUTO_INCREMENT,
  `game_id` bigint(8) unsigned NOT NULL,
  `award_id` bigint(8) unsigned NOT NULL,
  `player_id` bigint(8) unsigned NOT NULL,
  `time_of_award` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table games_challenges
# ------------------------------------------------------------

DROP TABLE IF EXISTS `games_challenges`;

CREATE TABLE `games_challenges` (
  `id` bigint(8) unsigned NOT NULL AUTO_INCREMENT,
  `game_id` bigint(8) unsigned NOT NULL,
  `player_id` bigint(8) unsigned NOT NULL,
  `challenge_id` bigint(8) unsigned NOT NULL,
  `time_of_challenge` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table games_ctf
# ------------------------------------------------------------

DROP TABLE IF EXISTS `games_ctf`;

CREATE TABLE `games_ctf` (
  `id` bigint(8) unsigned NOT NULL AUTO_INCREMENT,
  `game_id` bigint(8) unsigned NOT NULL,
  `player_id` bigint(8) unsigned NOT NULL,
  `flag` tinyint(1) unsigned NOT NULL,
  `ctf_action_id` bigint(8) unsigned NOT NULL,
  `time_of_action` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table games_kills
# ------------------------------------------------------------

DROP TABLE IF EXISTS `games_kills`;

CREATE TABLE `games_kills` (
  `id` bigint(8) unsigned NOT NULL AUTO_INCREMENT,
  `game_id` bigint(8) unsigned NOT NULL,
  `killer_id` bigint(8) unsigned NOT NULL,
  `killer_team` bigint(8) unsigned NOT NULL,
  `victim_id` bigint(8) unsigned NOT NULL,
  `victim_team` bigint(8) unsigned NOT NULL,
  `weapon_id` bigint(8) unsigned NOT NULL,
  `time_of_kill` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table gametypes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `gametypes`;

CREATE TABLE `gametypes` (
  `id` bigint(8) unsigned NOT NULL,
  `name` varchar(40) NOT NULL DEFAULT '',
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `gametypes` WRITE;
/*!40000 ALTER TABLE `gametypes` DISABLE KEYS */;

INSERT INTO `gametypes` (`id`, `name`, `description`)
VALUES
	(0,'Free For All','In a Free For All arena (FFA for short) also called a deathmatch, the goal is quite simple: kill the others and try not to be killed! You gain one frag each time you kill another player. You lose one frag each time you die by yourself (lava, rocket in wall, water...).'),
	(1,'Tournament','Also referred to as 1on1 or tourney, this game mode features a sequence of one on one battles where everybody else is spectating. The winner of the battle will stay for the next battle to fight the next combatant.'),
	(2,'Single Player Deathmatch','This mode is used when playing the series of pre-defined matches against bots that you find in the \"Singleplayer\" menu (not when using the \"skirmish\" feature). It is not intended to be used in multiplayer mode.'),
	(3,'Team Deathmatch','In Team Deathmatch (TDM) mode, each player is assigned to one of two teams: Blue team and Red team. The goal for each team is to kill the other team members and protect its own members. Scores of all members of each team are summed up. In this mode, it can be good to turn on friendly fire, to require players to pay more attention to where they shoot, considering that killing a team-mate would make the team lose a precious point (to force more cooperation).'),
	(4,'Capture The Flag','Each player is assigned to one team - red or blue. Team points are scored by capturing the enemies\' flag: get hold of the enemies\' flag (run over it), and then run over your own flag when it is at its home location.\n\nWhen your own flag is not at home, you (or a team member) will have to get it back first. If an enemy is holding it, shoot him first. Then run over the flag he dropped. The announcer will tell you \"red/blue flag returned\" and you can score. Beware that the enemy will try to do the same.\n\nThe match will end when the timelimit or the capturelimit is reached. Fraglimit is not used, but individual scores are shown in the score table: a player can get individual points when doing some actions:\n\n5 points for flag capture.\n1 point for recovering the flag.\n2 points for fragging the flag carrier.\n2 points for fragging the last player who hurt your flag carrier.\n1 point for fraggin someone while either you or your target are near your flag carrier.\n1 point for fraggin someone while either you or your target are near your flag.\n1 point for returning a flag that causes a capture to happen almost immediately. (Gives also the Assist award)\n2 points for fragging a flag carrier if a capture happens almost immediately.\n\nHowever, the only way to win is by capturing flags, so CTF involves a lot of team cooperation in order to achieve the victory.'),
	(5,'One Flag Capture','Each player is assigned to one team - red or blue. Team points are scored by capturing the white flag: get hold of the white flag (run over it), and then run over your _enemies\'_ flag.'),
	(6,'Overload','Each player is assigned to one team - red or blue. Each base has an obelisk. The obelisk has a lot of health (default 2500) and health regeneration. Team points are earned by destroying the enemy obelisk. Be aware that the enemy will attack your obelisk too.'),
	(7,'Harvester','Each player is assigned to one team - red or blue. Killing a blue player spawns a blue skull, and killing a red player spawns a red skull. Skulls spawn at the skull generator, usually at the middle of the map. You can collect skulls of the enemy\'s team color. Touching a skull of your team color will make it go away. Skulls go away when the skull carrier is fragged (they are not dropped). Running over the enemy skull receptacle will give a point for every skull you are carrying.'),
	(8,'Elimination','Two teams. You begin with all weapons and prefixed health and armor. No items to pickup. When you get killed, you will have to wait the end of the round. The team which eliminates the whole other team wins. Very similar to the popular Clan Arena game mode seen in OSP and Rocket Arena 3 mods for Quake 3 Arena.'),
	(9,'Capture The Flag Elimination','Similar to Elimination mode, but with Capture the Flag (capture the flag or kill all enemies to score). An optional \"One way capture\" mode is also available: in this case, one team will be in defense and the other will be in offense in each round; only the offensive team can capture the flag (the other team can score eliminating all the enemies or if the time runs out). It is enabled using g_gametype 9 and elimination_ctf_oneway 1 (default value is 0).'),
	(10,'Last Man Standing','A \"free for all\" variant of Elimination, with some changes and different score modes.'),
	(11,'Double Domination','Two teams. Your team has to keep the control of both checkpoints in the map for 10 seconds in order to score.'),
	(12,'Domination','Two teams. Each team has to get and mantain control of some checkpoints in order to gain points. Here it is advisable to set capturelimit to 500 instead of to 8 like usual.');

/*!40000 ALTER TABLE `gametypes` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table players
# ------------------------------------------------------------

DROP TABLE IF EXISTS `players`;

CREATE TABLE `players` (
  `id` bigint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `nickname` varchar(40) NOT NULL DEFAULT '',
  `oa_guid` varchar(80) DEFAULT NULL,
  `first_seen` datetime DEFAULT NULL,
  `last_seen` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nicknames`;

CREATE TABLE `nicknames` (
  `id` bigint(8) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` bigint(8) unsigned NOT NULL,
  `nickname` varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Dump of table weapons
# ------------------------------------------------------------

DROP TABLE IF EXISTS `weapons`;

CREATE TABLE `weapons` (
  `id` bigint(8) unsigned NOT NULL,
  `name` varchar(40) NOT NULL DEFAULT '',
  `nicename` varchar(40) NOT NULL DEFAULT '',
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `weapons` WRITE;
/*!40000 ALTER TABLE `weapons` DISABLE KEYS */;

INSERT INTO `weapons` (`id`, `name`, `nicename`, `description`)
VALUES
	(0,'MOD_UNKNOWN','Unknown',NULL),
	(1,'MOD_SHOTGUN','Shotgun',NULL),
	(2,'MOD_GAUNTLET','Gauntlet',NULL),
	(3,'MOD_MACHINEGUN','Machine Gun',NULL),
	(4,'MOD_GRENADE','Grenade Launcher',NULL),
	(5,'MOD_GRENADE_SPLASH','Grenade Launcher (Area Damage)',NULL),
	(6,'MOD_ROCKET','Rocket Launcher',NULL),
	(7,'MOD_ROCKET_SPLASH','Rocket Launcher (Area Damage)',NULL),
	(8,'MOD_PLASMA','Plasma Gun',NULL),
	(9,'MOD_PLASMA_SPLASH','Plasma Gun (Area Damage)',NULL),
	(10,'MOD_RAILGUN','Rail Gun',NULL),
	(11,'MOD_LIGHTNING','Lightning Gun',NULL),
	(12,'MOD_BFG','BFG',NULL),
	(13,'MOD_BFG_SPLASH','BFG  (Area Damage)',NULL),
	(14,'MOD_WATER','Water',NULL),
	(15,'MOD_SLIME','Slime',NULL),
	(16,'MOD_LAVA','Lava',NULL),
	(17,'MOD_CRUSH','Crush',NULL),
	(18,'MOD_TELEFRAG','Telefrag',NULL),
	(19,'MOD_FALLING','Falling',NULL),
	(20,'MOD_SUICIDE','Suicide',NULL),
	(21,'MOD_TARGET_LASER','Target Laser',NULL),
	(22,'MOD_TRIGGER_HURT','Hurt',NULL),
	(23,'MOD_NAIL','Nail Gun',NULL),
	(24,'MOD_CHAINGUN','Gattling Gun',NULL),
	(25,'MOD_PROXIMITY_MINE','Proximity Mine Launcher',NULL),
	(26,'MOD_KAMIKAZE','Kamikaze',NULL),
	(27,'MOD_JUICED','Juiced',NULL),
	(28,'MOD_GRAPPLE','Grappling Hook',NULL);

/*!40000 ALTER TABLE `weapons` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


/*test queries*/
/*
SELECT player_id, score, @rank := @rank + 1 AS rank FROM
(
  SELECT u.player_id, u.score
  FROM games_scores u
  LEFT JOIN games_scores u2
    ON u.player_id=u2.player_id
   AND u.game_id = u2.game_id
  WHERE u.game_id = 2017
  ORDER BY u.score DESC
) zz, (SELECT @rank := 0) z;

SELECT rank_number, player_id, score FROM
(
  SELECT player_id, score, @rank := @rank + 1 AS rank_number FROM
  (
    SELECT p.id as player_id, SUM(s.score) as score
    FROM players p
    LEFT JOIN games_scores s
      ON s.player_id=p.id
    WHERE score != 0
    GROUP BY p.id
    ORDER BY score DESC
  ) AS rankings, (SELECT @rank := 0) AS r
) AS overall_rankins LIMIT 0, 100;*/
