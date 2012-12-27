<?php
	require(dirname(__FILE__) . '/backend/header.php');
?>
			<div class="nine columns">				
				<?php
					if(isset($_GET['action']))
					{
						$action = strtolower($_GET['action']);
						if($action == "popular")
						{
							echo '<h3 class="title">Popular Threads</h3><hr />';
							
							//most popular posts
							//this might be a bit difficult. Let's just go about in an easy way, I suppose.
							//Take into consideration the last post made instead of when the topic was made.
							//Take into consideration that ordering by the last post's time is also great.
							//Order by replies, views, then time.
							$topics = $User->query(
								"SELECT
									topics.id,
									topics.name,
									topics.post_count,
									topics.view_count,
									COUNT( IF( posts.solved_post =  'yes', 1, NULL ) )
										AS solved_post,
									users.username
										AS posted_by,
									users.id
										AS posted_by_id
								FROM topics
								LEFT OUTER JOIN
									posts ON
										posts.topic_id = topics.id
								LEFT OUTER JOIN
									users ON
										users.id = posts.posted_by
								GROUP BY
									topics.id
								ORDER BY
									topics.post_count DESC,
									topics.view_count DESC,
									posts.id DESC"
							);
							
							if(!empty($topics))
							{
								foreach($topics AS $topic)
								{
									echo '<div class="row">';
										echo '<div class="five columns"><h5 class="category" style="font-weight:normal;">' . (($topic['solved_post'] > 0) ? '[S] ' : '') . '<a href="topic.php?id=' . $topic['id'] . '">' . $topic['name'] . '</a></h5></div>';
										echo '<div class="three columns">Posted by <a href="profile.php?id=' . $topic['posted_by_id'] . '">' . $topic['posted_by'] . '</a></div>';
										echo '<div class="two columns">' . number_format($topic['view_count']) . ' view' . (($topic['view_count'] == 1) ? '' : 's') . '</div>';
										echo '<div class="two columns">' . number_format($topic['post_count']) . ' post' . (($topic['post_count'] == 1) ? '' : 's') . '</div>';
									echo '</div>';
									
									echo '<hr />';
								}
							}
								else
							{
								echo '<strong>Doesn\'t look like there are any posts.</strong>';
							}
						}
							else
						if($action == "latest")
						{
							echo '<h3 class="title">Latest Posts</h3><hr />';
							
							//latest posts on threads
							$topics = $User->query(
								"SELECT
									topics.id,
									topics.name,
									topics.post_count,
									topics.view_count,
									COUNT( posts.solved_post )
										AS solved_post,
									users.username
										AS posted_by,
									users.id
										AS posted_by_id
								FROM topics
								LEFT OUTER JOIN
									posts ON
										posts.topic_id = topics.id
								LEFT OUTER JOIN
									users ON
										users.id = posts.posted_by
								GROUP BY
									topics.id
								ORDER BY
									posts.id DESC"
							);
							
							if(!empty($topics))
							{
								foreach($topics AS $topic)
								{
									echo '<div class="row">';
										echo '<div class="five columns"><h5 class="category" style="font-weight:normal;">' . (($topic['solved_post'] > 0) ? '[S] ' : '') . '<a href="topic.php?id=' . $topic['id'] . '">' . $topic['name'] . '</a></h5></div>';
										echo '<div class="three columns">Posted by <a href="profile.php?id=' . $topic['posted_by_id'] . '">' . $topic['posted_by'] . '</a></div>';
										echo '<div class="two columns">' . number_format($topic['view_count']) . ' view' . (($topic['view_count'] == 1) ? '' : 's') . '</div>';
										echo '<div class="two columns">' . number_format($topic['post_count']) . ' post' . (($topic['post_count'] == 1) ? '' : 's') . '</div>';
									echo '</div>';
									
									echo '<hr />';
								}
							}
								else
							{
								echo '<strong>Doesn\'t seem like there are any posts.</strong>';
							}
						}
							else
						{
							header('Location: index.php');
						}
					}
						else
					if(isset($_GET['fid']))
					{
						$fid = $_GET['fid'];
						if(($cat_name = $User->categoryName($fid)) !== FALSE)
						{
							echo '<h3 class="title">' . $cat_name . '</h3><hr />';
							
							//start getting topics in category
							$topics = $User->prepare(
								"SELECT
									topics.id,
									topics.name,
									topics.post_count,
									topics.view_count,
									COUNT( posts.solved_post )
										AS solved_post, 
									users.username
										AS posted_by,
									users.id AS posted_by_id
								FROM topics
								LEFT OUTER JOIN
									posts ON
										posts.topic_id = topics.id
								LEFT OUTER JOIN
									users ON
										users.id = posts.posted_by
								WHERE
									topics.cat_id = :cat
								GROUP BY
									topics.id"
							);
							$topics->bindParam(":cat", $fid, PDO::PARAM_INT);
							$topics->execute();
							$topic_data = $topics->fetchAll(PDO::FETCH_ASSOC);
							
							if(!empty($topic_data))
							{
								//if there are topics
								foreach($topic_data AS $topic)
								{
									//loop through them
									echo '<div class="row">';
										echo '<div class="five columns"><h5 class="category" style="font-weight:normal;">' . (($topic['solved_post'] > 0) ? '[S] ' : '') . '<a href="topic.php?id=' . $topic['id'] . '">' . $topic['name'] . '</a></h5></div>';
										echo '<div class="three columns">Posted by <a href="profile.php?id=' . $topic['posted_by_id'] . '">' . $topic['posted_by'] . '</a></div>';
										echo '<div class="two columns">' . number_format($topic['view_count']) . ' view' . (($topic['view_count'] == 1) ? '' : 's') . '</div>';
										echo '<div class="two columns">' . number_format($topic['post_count']) . ' post' . (($topic['post_count'] == 1) ? '' : 's') . '</div>';
									echo '</div>';
									
									echo '<hr />';
								}
							}
								else
							{
								//no topics
								echo '<strong>No topics in this category.</strong>';
							}
						}
							else
						{
							header('Location: index.php');
						}
					}
						else
					{
						//forum id not set in uri
						//let's start getting forum categories
						$cats = $User->query(
							"SELECT
								id,
								name,
								description,
								parent,
								placement
							FROM categories
							ORDER BY
								placement ASC,
								id ASC"
						);
						
						$i = 0;
						foreach($cats AS $row)
						{
							if($row["parent"] == "yes")
							{
								echo ($i > 0) ? '<br />' : '';
								echo '<h5 class="category">' . $row["name"] . '</h5>';
								echo '<hr />';
							}
								else
							{
								echo '<div class="row">';
									echo '<div class="one column mobile-one"><img src="http://placehold.it/50x50&text=[img]" /></div>';
									echo '<div class="eleven columns">';
										echo '<strong><a href="index.php?fid=' . $row['id'] . '">' . $row["name"] . '</a></strong>';
										echo '<p class="forum_desc">' . $row["description"] . '</p>';
									echo '</div>';
								echo '</div>';
								
								echo '<hr />';
							}
							$i++;
						}
					}
				?>
			</div>
<?php
	require(dirname(__FILE__) . '/backend/footer.php');
?>