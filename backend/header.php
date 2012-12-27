<?php
	//get site configuration
	require_once(dirname(__FILE__) . "/config/site.php");
	
	if(STATUS === FALSE)
	{
		//website is suppose to be offline
		header('Location: ' . STATUS_REDIRECT);
		exit();
	}
	
	//get user class + pdo class
	require_once(dirname(__FILE__) . "/class/user.php");
	
	//create User + PDO
	$User = new User();
?>
<!DOCTYPE html>
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="en"><!--<![endif]-->
	<head>
	  <meta charset="utf-8" />
	  <meta name="viewport" content="width=device-width" />
	  <title>Forum</title>
	  <link rel="stylesheet" href="styles/style.css">

	</head>
	<body>
		<div class="row">
			<div class="twelve columns">
				<div class="panel">
					<div class="row">
						<div class="two columns">
							<h1 class="title">Forum</h1>
						</div>
						<div class="ten columns">
							<ul class="inline-list right navigation">
								<li><a href="index.php">Home</a></li>
								<li><a href="index.php?action=latest">Latest Posts</a></li>
								<li><a href="#">Popular Posts</a></li>
								<li><a href="#">Blog</a></li>
								<li><a href="#">Contact Us</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="three columns">
				<div class="panel">
					<?php
						if($User->logged_in)
						{
							echo '<div class="row">';
								echo '<div class="six column mobile-one">';
									echo '<a href="#"><img src="http://placehold.it/100x80&text=[img]" /></a>';
								echo '</div>';
								echo '<div class="six column">';
									echo '<strong>' . $User->user["username"] . '</strong>';
								echo '</div>';
							echo '</div>';
							echo '<br />';
							echo number_format($User->user["post_count"]) . ' post' . (($User->user["post_count"] == 1) ? '' : 's') . '<br />';
							echo number_format($User->user["solved_count"]) . ' solved post' . (($User->user["solved_count"] == 1) ? '' : 's') . '<br /><br />';
							
							echo '<dl class="vertical tabs">';
								echo '<dd><a href="settings.php">Settings</a></dd>';
								echo '<dd><a href="login.php?action=logout">Logout</a></dd>';
							echo '</dl>';
						}
							else
						{
							echo '<h5 class="title">Login</h5>';
							echo '<form method="POST" action="login.php?return=' . urlencode($User->current_page()) . '">';
								echo 'Username <input type="text" name="username" value="" /><br />';
								echo 'Password <input type="password" name="password" value="" /><br />';
								echo '<input type="submit" name="Login" value="Login" class="small button" /> ';
								echo '<input type="button" name="register" value="Register" class="small button" /><br /><br />';
								echo '<a href="login.php?action=forgot">I forgot my password</a>';
							echo '</form>';
						}
					?>
				</div>
			</div>
			