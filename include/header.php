<!DOCTYPE html>
<html lang="en">
    <head>
	<base href="http://quake.brensen.com/stats/" target="_self"></base>
        <meta charset="utf-8">
        <title>OpenArena Statistics</title>
        <link href="css/bootstrap.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
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
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    </head>

    <body>

        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="brand" href="#">OpenArena Statistics</a>
                    <ul class="nav" role="navigation">
                        <li class="dropdown">
                            <a id="players" href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Players <b class="caret"></b></a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="players">
                                <?php if (!isset($_GET['player'])) : ?>
                                <li class="active"><a href="#">Overall</a></li>
                                <?php else : ?>
                                <li><a href="#">Overall</a></li>
                                <?php endif; ?>
                                <li class="divider"></li>
                                <?php while ($player_row = mysqli_fetch_array($players_result, MYSQLI_ASSOC)) : ?>
                                    <?php if (isset($_GET['player']) && $player_row['id'] == $_GET['player']) : ?>
                                        <li class="active"><a href="?player=<?php echo $player_row['id']; ?>"><?php echo $player_row['nickname']; ?></a></li>
                                    <?php else : ?>
                                        <li><a href="?player=<?php echo $player_row['id']; ?>"><?php echo $player_row['nickname']; ?></a></li>
                                    <?php endif; ?>
                                <?php endwhile; ?>
                            </ul>
                        </li>
                    </ul>
                    <ul class="nav">
                    <?php $t_val = ($today)?0:1;?>
                    	<li>
                    		<?php if (isset($_GET['player'])) : ?>
                    			<a href="?player=<?php echo $player['id']; ?>&today=<?php echo $t_val; ?>">Today only</a>
                   			<?php else : ?>
                   				<a href="?today=<?php echo $t_val; ?>">Today only</a>
                    		<?php endif; ?>
                    	</li>
                    </ul>
					<ul class="nav pull-right">
							<li>
								<a href="#">
									<iframe src="http://ghbtns.com/github-btn.html?user=kalidasya&repo=OpenArena-Stats&type=fork" allowtransparency="true" frameborder="0" scrolling="0" width="62" height="20"></iframe>
								</a>
							</li>
					</ul>
                </div>
            </div>
        </div>
