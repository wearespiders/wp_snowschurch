<?php get_header(); ?>
<div class="root">
<?php get_template_part( 'page', 'header'); ?>
<?php get_template_part( 'top', 'navbar'); ?>
<?php get_template_part( 'page', 'banner'); ?>
<?php get_template_part( 'page', 'body'); ?>
<?php get_template_part( 'bottom', 'navbar'); ?>
<?php get_template_part( 'footer', 'authorbar'); ?>
</div>
<?php get_template_part( 'footer', 'disclaimer'); ?>