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
							topics.id,
							topics.name,
							topics.cat_id,
							topics.post_count,
							topics.view_count,
							categories.allow_solved,
							IF( posts.solved_post =  'yes', 1, 0 ) AS solved_count
						FROM topics
						LEFT OUTER JOIN
							posts ON
								posts.topic_id = topics.id
						LEFT JOIN
							categories ON
								topics.cat_id = categories.id
						WHERE
							topics.id = :id"
					);
					$topic->bindParam(":id", $_GET['id'], PDO::PARAM_INT);
					$topic->execute();
					$data = $topic->fetch(PDO::FETCH_ASSOC);
					
					if(!empty($data))
					{
						//header information
						echo '<h3 class="title left">' . (($data['solved_count'] > 0) ? '[SOLVED]' : '') . ' ' . $data['name'] . '</h3>' . 
						'<span class="right">' . ((!$User->logged_in) ? '<button class="small button disabled">Reply</button>' : '<a href="#reply" class="small button">Reply</a>') . '</span><hr />';
						//Now we need to start getting posts.
						$per_page = 10;
						$page = (isset($_GET['page']) ? $_GET['page'] : 1);
						
						$start = ($page - 1) * $per_page; 	
						$posts = $User->prepare(
							"SELECT
								(SELECT
									COUNT(id)
								FROM posts) AS total_posts,
								posts.id AS id,
								posts.message AS message,
								posts.posted_by AS posted_by_id,
								users.username AS username,
								users.post_count AS post_count,
								users.solved_count AS solved_count,
								posts.posted_on AS posted_on,
								posts.solved_post AS solved_post,
								IF(posts.solved_post = 'yes', 1, 0) AS solved_count
							FROM posts
							LEFT JOIN
								users ON
									users.id = posts.posted_by
							WHERE
								posts.topic_id = :id
							ORDER BY
								posts.id ASC
							LIMIT :start, " . $per_page
						);
						$posts->bindParam(":id", $_GET['id'], PDO::PARAM_INT);
						$posts->bindParam(":start", $start, PDO::PARAM_INT);
						$posts->execute();
						$posts_data = $posts->fetchAll(PDO::FETCH_ASSOC);
						
						if(!empty($posts_data))
						{
							$i = 1;
							$OP = null;
							
							foreach($posts_data AS $post)
							{
								$total_posts = $post['total_posts'];
								
								if($i == 1)
								{
									$OP = $post['posted_by_id'];
								}
								
								echo '<div class="row" id="post_' . $post['id'] . '">' .
									'<div class="two columns user_info">' .
										'<a href="profile.php?id=1"><img src="http://placehold.it/80x80&text=[img]" /><br/ >' .
										$post['username'] . '</a><br /><br />' .
										
										number_format($post['post_count']) . ' post' . (($post['post_count'] == 1) ? '' : 's') . '<br />' .
										number_format($post['solved_count']) . ' solved' .
										'<br /><br />January 3rd, 2012<br /><br />' .
										
										//post actions
										'<div href="#" class="small button dropdown">' . 
											'Actions' . 
											'<ul>' . 
												'<li><a href="report.php?type=post&id=' . $post['id'] . '">Report</a></li>';
												if($User->logged_in && $User->user["id"] == $post['posted_by_id'])
												{
													echo '<li><a href="edit.php?type=post&id=' . $post['id'] . '">Edit</a></li>';
												}
												
												if($User->logged_in && $OP == $User->user["id"] && $post['posted_by_id'] != $User->user["id"] && $data['allow_solved'] == "yes")
												{
													if($post['solved_post'] == "yes")
													{
														echo '<li><a href="mark.php?type=unsolve&id=' . $post['id'] . '">Not Solved</a></li>';
													}
														else
													{
														if($post['solved_count'] == 0)
														{
															echo '<li><a href="edit.php?type=solved&id=' . $post['id'] . '">Mark Solved</a></li>';
														}
													}
												}
											echo '</ul>' . 
										'</div>' . 
										
										'<br /><br /><a href="#post_' . $post['id'] . '">#' . $i . '</a>' . 
									'</div>' . 
									
									'<div class="ten columns">' . 
										$post['message'] . 
										(($post['solved_post'] == "yes") ? '<br /><br /><br /><div class="alert-box success">The original poster says this post solved his problem.</div>' : '') . 
									'</div>' . 
								'</div>' . 
								'<hr />';
								$i++;
							}
							
							//pages
							$total_pages = ceil($total_posts / $per_page);
							
							if($total_pages > 1)
							{
								echo 'Page ' . $page . ' of ' . $total_pages . '<br /><br />';
								echo '<ul class="pagination">' .
									(($page > 1) ? '<li class="arrow unavailable"><a href="topic.php?id=' . $_GET['id'] . '&page=' . ($page-1) . '">&laquo;</a></li>' : '');
										for($i = 1; $i <= $total_pages; $i++)
										{
											echo '<li' . (($i == $page) ? ' class="current"' : '') . '><a href="topic.php?id=' . $_GET['id'] . '&page=' . $i . '">' . $i . '</a></li>';
										}
									echo (($page < $total_pages) ? '<li class="arrow"><a href="topic.php?id=' . $_GET['id'] . '&page=' . ($page+1) . '">&raquo;</a></li>' : '') . 
								'</ul>';
							}
							
							if($User->logged_in)
							{
								echo '<h4 id="reply">Reply</h4>';
								echo '<form method="POST" action="reply.php?id=' . $data['id'] . '">';
									echo '<textarea name="msg" style="height:150px;"></textarea><br />';
									echo '<input type="submit" name="Reply" value="Reply" class="button" />';
								echo '</form>';
							}
						}
					}
				?>
			</div>
<?php
	require(dirname(__FILE__) . '/backend/footer.php');
?>