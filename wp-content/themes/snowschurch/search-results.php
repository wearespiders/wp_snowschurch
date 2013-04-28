<?php 
    //function_exists() — Return TRUE if the given function has been defined.
    if (function_exists('wp_bac_breadcrumb')) {wp_bac_breadcrumb();} 
?>
<content>
<?php if ( have_posts() ) : ?>
	<contenttitle><?php printf( __( 'Search Results for: %s', 'snowschurch' ), '<span>' . get_search_query() . '</span>' ); ?></contenttitle>
	<!--?php twentyeleven_content_nav( 'nav-above' ); ?-->
	<?php /* Start the Loop */ ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<?php
			/* Include the Post-Format-specific template for the content.
			* If you want to overload this in a child theme then include a file
			* called content-___.php (where ___ is the Post Format name) and that will be used instead.
			*/
			get_template_part( 'search-resultdetails', get_post_format() );
		?>
	<?php endwhile; ?>
	<!--?php twentyeleven_content_nav( 'nav-below' ); ?-->
<?php else : ?>
	<contenttitle><?php _e( 'Nothing Found', 'snowschurch' ); ?></contenttitle>
	<content>
		<?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'twentyeleven' ); ?>
		<?php get_search_form(); ?>
	</content>
<?php endif; ?>
</content>