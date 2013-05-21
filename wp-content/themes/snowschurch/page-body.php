<div class="root-page-body">
	<?php /*?><div class="root-leftbar"><?php get_template_part( 'side', 'leftbar'); ?></div>	<?php */?>
    <div class="root-page-content">
    <?php 
	// display the recent page
	if (is_front_page())
	{
		if ( !function_exists('dynamic_sidebar')
        || !dynamic_sidebar() ) : 
		endif;
	} 
	
	//display the page content
	if ($_GET['submit'] == 'Search') {
		get_template_part( 'search', 'results'); 
		} 
	else {
		get_template_part( 'page', 'content'); 
		}	
	?>
	</div>
    <div class="root-rightbar"><?php get_template_part( 'side', 'rightbar'); ?></div>  
</div>