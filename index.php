<?php
	require(dirname(__FILE__) . '/backend/header.php');
?>
			<div class="nine columns">				
				<?php
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
							echo '<div class="eleven columns"><strong>' . $row["name"] . '</strong>';
							echo '<p class="forum_desc">' . $row["description"] . '</p></div>';
							echo '</div>';
							echo '<hr />';
						}
						
						$i++;
					}
				?>
			</div>
<?php
	require(dirname(__FILE__) . '/backend/footer.php');
?>