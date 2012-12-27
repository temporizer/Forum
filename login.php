<?php
	require(dirname(__FILE__) . '/backend/header.php');
	
	if($User->logged_in)
	{
		if(isset($_GET['action']) && $_GET['action'] == "logout")
		{
			$User->logout();
		}
		
		header('Location: index.php');
	}
?>
			<div class="nine columns">
				<h3 class="title">Login</h3><hr />
				<?php
					if(isset($_POST['Login']))
					{
						$Username = $_POST['username'];
						$Password = $_POST['password'];
						$Return_to = (isset($_GET['return'])) ? urldecode($_GET['return']) : 'index.php';
						
						if($Username != "" && $Password != "")
						{
							//Check if username and and password are correct.
							$Password = $User->hash($Password);
							
							$check = $User->prepare(
								"SELECT
									id,
									username,
									password,
									email,
									access,
									banned_until,
									post_count,
									solved_count,
									avatar,
									description
								FROM users
								WHERE
									username = :username AND
									password = :password"
							);
							$check->bindParam(":username", $Username, PDO::PARAM_STR);
							$check->bindParam(":password", $Password, PDO::PARAM_STR);
							$check->execute();
							$data = $check->fetch(PDO::FETCH_ASSOC);
							if(!empty($data))
							{
								if($data['banned_until'] > time())
								{
									$timeleft = $User->parseTime(($data['banned_until'] - time()), "after");
									echo '<div class="alert-box alert">You are banned until <abbr title="' . date("F d Y h:i A", ($data['banned_until'] - time())) . '">' . $timeleft . '</abbr></div>';
								}
									else
								if($data["access"] == 0)
								{
									echo '<div class="alert-box alert">You are banned forever.</div>';
								}
									else
								{
									$session_data = $User->create_session_data($data);
									echo $session_data;
									$_SESSION = $session_data;
									// unsets password field in session data
									//unset($_SESSION['password']);
									header('Location: ' . $Return_to);
								}
							}
								else
							{
								echo '<div class="alert-box alert">The username and password combination doesn\'t match up.</div>';
							}
						}
							else
						{
							echo '<div class="alert-box alert">Please enter a username and a password</div>';
						}
					}
				?>
				<form method="POST" action="login.php?return=<?=((!isset($_GET['return'])) ? $_GET['return'] : 'index.php')?>">
					Username <input type="text" name="username" value="" span="five" /><br />
					Password <input type="password" name="password" value="" /><br />
					<input type="submit" name="Login" value="Login" class="button" />
					<input type="button" name="register" value="Register" class="button" /><br /><br />
					<a href="login.php?action=forgot">I forgot my password</a>
				</form>
			</div>
<?php
	require(dirname(__FILE__) . '/backend/footer.php');
?>