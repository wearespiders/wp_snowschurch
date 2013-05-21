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
    <?php
	if (strpos($_SERVER["REQUEST_URI"],'blogs')>0 || strpos($_SERVER["REQUEST_URI"],'comments')>0 || strpos($_SERVER["REQUEST_URI"],'media')>0) 
	{
	?>
    <div id="comments">
    <div id="disqus_thread"></div>
    <script type="text/javascript">
        var disqus_shortname = 'ourladyofsnows'; 
        /* * * DON'T EDIT BELOW THIS LINE * * */
        (function() {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();
    </script>
    <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments.</a></noscript>
    </div>
    <?php
	}
	?>
</article>