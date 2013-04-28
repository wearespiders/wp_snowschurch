<div id="searchresult-item">
	<div id="searchresult-title">
		<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'snowschurch' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
	</div>	
	<div id="searchresult-summary">		
		<?php if ( is_search() ) : // Only display Excerpts for Search ?>
			<?php the_excerpt(); ?>
		<?php else : ?>
				<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'snowschurch' ) ); ?>
				<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'snowschurch' ) . '</span>', 'after' => '</div>' ) ); ?>
		<?php endif; ?>
	</div>
	<div id="searchresult-footer">
		Created On: <?php echo get_the_date( $d ); ?> | Last updated: <?php the_modified_date();  ?>
	</div>
	<div id="searchresult-seperator"></div>
</div>