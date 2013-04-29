<div class="root-page-body">
	<?php /*?><div class="root-leftbar"><?php get_template_part( 'side', 'leftbar'); ?></div>	<?php */?>
    <div class="root-page-content">
	<?php 
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