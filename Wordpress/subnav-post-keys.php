<?php
/*

	------------------------------------------------------------------------------------
	subnav-post-keys.php

	Create a page listing using a custom field as the page title.
	Only intended to work on a single parent page, could be generalized to work anywhere
	------------------------------------------------------------------------------------
	
*/

	// the parent post ID
	$postID = 988;

	// the custom field name
	$customField = "nav-title";


	// you'll want to place this inside the loop
	if ( have_posts() ) while ( have_posts() ) : the_post(); ?>



					<ul id="sub-nav">
					<?php

						// get parent regardless whether we're on a second or third-level page
						if(empty($wp_query->post->post_parent)) { 
							$parentPost = $wp_query->post->ID; 
						} else { 
							$parentPost = $wp_query->post->post_parent; 
						}

						// place this on both the selected post and any child pages
						if (is_page($postID) || ($parent == $postID)){

							// get a listing of pages underneath selected parent
							$args = array(
								'child_of'     => $postID,
								'depth'        => 0,
								'show_date'    => '',
								'date_format'  => get_option('date_format'),
								'exclude'      => '',
								'include'      => '',
								'title_li'     => '',
								'echo'         => 0,
								'authors'      => '',
								'sort_column'  => 'menu_order, post_title',
								'link_before'  => '',
								'link_after'   => '',
								'walker' => '' );
							$pages = get_pages($args);
	

							// loop through resulting page list							
							foreach($pages as $key => $data) {
	
								// find the custom field value
								$customFieldValue = get_post_meta($data->ID, $customField, 1);
	
								// dish up the results
								echo "<li><a href=\"" . get_page_link($data->ID) . "\">" . $customFieldValue . "</a></li>";
							}

						
						}
					?>
					</ul>



<?php endwhile; // end of the loop. ?>