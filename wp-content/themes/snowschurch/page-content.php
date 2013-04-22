<?php 
    //function_exists() — Return TRUE if the given function has been defined.
    if (function_exists('wp_bac_breadcrumb')) {wp_bac_breadcrumb();} 
?>
<article id="<?php the_ID(); ?>">
	<contenttitle><?php the_title(); ?></contenttitle>
	<content>
	<?php
	if ( have_posts() ) : while ( have_posts() ) : the_post();
   the_content();
endwhile; endif;
	?>
    </content>
</article>