<?php
	require(dirname(__FILE__) . '/backend/header.php');
	if(isset($_GET['fid']))
	{
		$fid = $_GET['fid'];
	}
?>
			<div class="nine columns">				
				<?php
					if(isset($fid))
					{
						//start getting topics in category
						$topics = $User->prepare("SELECT topics.id, topics.name, topics.post_count, topics.view_count, (SELECT COUNT(posts.solved_post) FROM posts WHERE posts.topic_id = topics.id AND solved_post = 'yes') AS solved_post FROM topics WHERE topics.cat_id = :cat ORDER BY topics.id DESC");
						$topics->bindParam(":cat", $fid, PDO::PARAM_INT);
						$topics->execute();
						$topic_data = $topics->fetchAll(PDO::FETCH_ASSOC);
						
						if(!empty($topic_data))
						{
							foreach($topic_data AS $topic)
							{
								echo '<div class="row">';
									echo '<div class="eight columns"><h5 class="category">' . $topic['name'] . '</h5></div>';
									echo '<div class="two columns">' . number_format($topic['view_count']) . ' views</div>';
									echo '<div class="two columns">' . number_format($topic['post_count']) . ' posts</div>';
								echo '</div>';
								
								echo '<hr />';
							}
						}
							else
						{
							echo '<strong>No topics in this category.</strong>';
						}
					}
						else
					{
						//let's start getting forum categories
						$cats = $User->query("SELECT id, name, description, parent, placement FROM categories ORDER BY placement ASC, id ASC");
						
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