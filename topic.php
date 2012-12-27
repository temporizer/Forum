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
							name,
							cat_id,
							post_count,
							view_count
						FROM topics
						WHERE
							id = :id"
					);
					$topic->bindParam(":id", $_GET['id'], PDO::PARAM_INT);
					$topic->execute();
					$data = $topic->fetch(PDO::FETCH_ASSOC);
					if(!empty($data))
					{
						echo '<h3 class="title left">' . $data['name'] . '</h3><span class="right"><button class="small button">Reply</button></span><hr />';
						
						//Now we need to start getting posts.
						//Logic says that the topic id is now clean and legit, since we can get it from the query itself. Saves a bit of time for PHP and MySQL.
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
								posts.id DESC"
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
				?>
			</div>
<?php
	require(dirname(__FILE__) . '/backend/footer.php');
?>