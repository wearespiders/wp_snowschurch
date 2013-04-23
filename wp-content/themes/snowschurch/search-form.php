<?php
// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
?>
	<form method="get" id="searchform" action="<?php echo home_url( '/' ); ?>">
		<input type="text" class="field myTextbox" name="s" id="s" placeholder="<?php esc_attr_e('search here &hellip;', 'snowschurch'); ?>" />
		<input type="submit" class="submit myButton" name="submit" id="searchsubmit" value="<?php esc_attr_e('Search', 'snowschurch'); ?>"  />
	</form>