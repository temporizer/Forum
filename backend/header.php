<?php
	//get site configuration
	require_once(dirname(__FILE__) . "/config/site.php");
	
	if(STATUS === FALSE)
	{
		//website is suppose to be offline
		header('Location: ' . STATUS_REDIRECT);e
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
					<h1>Forum</h1>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="three columns">
				<div class="panel">
					<div class="row">
						<div class="six column mobile-one"><a href="#"><img src="http://placehold.it/100x80&text=[img]" /></a></div>
						<div class="six column">
							<strong>Username</strong>
						</div>
					</div><br />
					<dl class="vertical tabs">
						<dd><a href="#">Home</a></dd>
						<dd><a href="#">Latest Posts</a></dd>
						<dd><a href="#">Popular Posts</a></dd>
						<dd><a href="#">Blog</a></dd>
						<dd><a href="#">Contact Us</a></dd>
					</dl>
				</div>
			</div>
			