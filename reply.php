<?php
	require(dirname(__FILE__) . '/backend/header.php');
	if(!isset($_GET['id']))
	{
		header('Location: index.php');
	}
?>
			<div class="nine columns">
				<?php
					//Get topic data.
					$topic = $User->prepare(
						"SELECT
							id,
							name
						FROM topics
						WHERE
							id = :id"
					);
					$topic->bindParam(":id", $_GET['id'], PDO::PARAM_INT);
					$topic->execute();
					$data = $topic->fetch(PDO::FETCH_ASSOC);
					if(!empty($data))
					{
						echo '<h3 class="title left">Reply - ' . $data['name'] . '</h3><hr />';
						
						if(isset($_POST['Reply']))
						{
							$Message = nl2br(htmlentities($_POST['msg']));
							if($Message != "")
							{
								if(strlen($Message) > 4 && strlen($Message) < 1000)
								{
									//Start inserting message
									$insert = $User->prepare(
										"INSERT
											INTO posts
										(topic_id, message, posted_by, posted_on)
										VALUES
											(:topic_id,
											:msg,
											:posted_by,
											UNIX_TIMESTAMP())"
									);
									$insert->bindParam(":topic_id", $data['id'], PDO::PARAM_INT);
									$insert->bindParam(":msg", $Message, PDO::PARAM_STR);
									$insert->bindParam(":posted_by", $User->user["id"], PDO::PARAM_INT);
									
									if($insert->execute())
									{
										$User->redirect("topic.php?id=" . $data['id'] . "#post_" . $User->lastInsertId());
									}
								}
									else
								{
									echo '<div class="alert-box alert">Your message must be between 5 and 1,000 characters long.</div>';
								}
							}
								else
							{
								echo '<div class="alert-box alert">You must type in a message.</div>';
							}
						}
						
						echo '<form method="POST">';
							echo '<textarea name="msg" style="height:200px;"></textarea><br />';
							echo '<input type="submit" class="button" name="Reply" value="Reply" />';
						echo '</form>';
						echo '<hr />';
						//Get latest posts
						$posts = $User->query(
							"SELECT
								posts.id AS post_id,
								posts.message AS message,
								users.username AS username,
								users.post_count AS post_count,
								users.solved_count AS solved_count,
								posts.posted_on AS posted_on,
								posts.solved_post AS solved_post
							FROM posts
							LEFT JOIN
								users ON
									users.id = posts.posted_by
							WHERE
								posts.topic_id = " . $data['id'] . "
							ORDER BY
								posts.id DESC
							LIMIT
								5"
						);
						
						if(!empty($posts))
						{
							foreach($posts AS $post)
							{
								echo '<div class="row" id="post_' . $post['post_id'] . '">';
									echo '<div class="two columns user_info">';
										echo '<a href="profile.php?id=1"><img src="http://placehold.it/80x80&text=[img]" /><br/ >';
										echo $post['username'] . '</a><br /><br />';
										echo number_format($post['post_count']) . ' post' . (($post['post_count'] == 1) ? '' : 's') . '<br />';
										echo number_format($post['solved_count']) . ' solved';
										echo '<br /><br />January 3rd, 2012<br /><br />';
										echo '<button class="small button">Report</button>';
									echo '</div>';
									
									echo '<div class="ten columns">';
										echo $post['message'];
										echo ($post['solved_post'] == "yes") ? '<br /><br /><br /><div class="alert-box success">The original poster says this post solved his problem.</div>' : '';
									echo '</div>';
								echo '</div>';
								echo '<hr />';
							}
						}
					}
						else
					{
						header('Location: index.php');
					}
				?>
			</div>
<?php
	require(dirname(__FILE__) . '/backend/footer.php');
?>