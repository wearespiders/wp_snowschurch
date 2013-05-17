<?php
/*
Plugin Name: SanookSookjai Menu Icon
Plugin URL: http://themeforest.net/user/sanook_sookjai
Description: This is a small plugin to customize your icon menus
Version: 1.0
Author: Sanook_Sookjai
Author URI: http://themeforest.net/user/sanook_sookjai
Contributors: Sanook_Sookjai
Text Domain: sanook_sookjai
*/

class Sanook_Menu_Icon {
	function __construct() {
		add_action( 
			'init', 
			array( $this, 'textdomain' )
		);
		
		add_filter( 
			'wp_setup_nav_menu_item', 
			array( $this, 'sanook_sookjai_add_custom_nav_fields' )
		);
		
		add_action( 
			'wp_update_nav_menu_item', 
			array( $this, 'sanook_sookjai_update_custom_nav_fields'), 
			10, 
			3 
		);
		
		add_filter(
			'wp_edit_nav_menu_walker', 
			array( $this, 'sanook_sookjai_edit_walker'), 
			10, 
			2
		);

	}

	public function textdomain() {
		load_plugin_textdomain( 
			'sanook_sookjai', 
			false, 
			dirname( plugin_basename( __FILE__ ) ) . '/languages' 
		);
	}

	function sanook_sookjai_add_custom_nav_fields( $menu_item ) {
	    $menu_item->menuimage = get_post_meta( $menu_item->ID, '_menu_item_menuimage', true );
	    return $menu_item;
	}
	
	function sanook_sookjai_update_custom_nav_fields( $menu_id, $menu_item_db_id, $args ) {
	    if ( is_array( $_REQUEST['menu-item-menuimage']) ) {
	        $menuimage_value = $_REQUEST['menu-item-menuimage'][$menu_item_db_id];
	        update_post_meta( $menu_item_db_id, '_menu_item_menuimage', $menuimage_value );
	    }
	    
	}
	
	function sanook_sookjai_edit_walker($walker,$menu_id) {
		return 'Sanook_Nav_Icon_Edit';
	}

}

global $sanook_menu_icon;
$sanook_menu_icon = new Sanook_Menu_Icon();

class Sanook_Nav_Icon_Edit extends Walker_Nav_Menu {
	function start_lvl(&$output) {	
	}

	function end_lvl(&$output) {
	}

	function start_el(&$output, $item, $depth, $args) {
	    global $_wp_nav_menu_max_depth;
	   
	    $_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;
	
	    $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
	
	    ob_start();
	    $item_id = esc_attr( $item->ID );
	    $removed_args = array(
	        'action',
	        'customlink-tab',
	        'edit-menu-item',
	        'menu-item',
	        'page-tab',
	        '_wpnonce',
	    );
	
	    $original_title = '';
	    if ( 'taxonomy' == $item->type ) {
	        $original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
	        if ( is_wp_error( $original_title ) )
	            $original_title = false;
	    } elseif ( 'post_type' == $item->type ) {
	        $original_object = get_post( $item->object_id );
	        $original_title = $original_object->post_title;
	    }
	
	    $classes = array(
	        'menu-item menu-item-depth-' . $depth,
	        'menu-item-' . esc_attr( $item->object ),
	        'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
	    );
	
	    $title = $item->title;
	
	    if ( ! empty( $item->_invalid ) ) {
	        $classes[] = 'menu-item-invalid';
	        $title = sprintf( __( '%s (Invalid)' ), $item->title );
	    } elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
	        $classes[] = 'pending';
	        $title = sprintf( __('%s (Pending)'), $item->title );
	    }
	
	    $title = empty( $item->label ) ? $title : $item->label;
	
	    ?>
	    <li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes ); ?>">
	        <dl class="menu-item-bar">
	            <dt class="menu-item-handle">
	                <span class="item-title"><?php echo esc_html( $title ); ?></span>
	                <span class="item-controls">
	                    <span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
	                    <span class="item-order hide-if-js">
	                        <a href="<?php
	                            echo wp_nonce_url(
	                                add_query_arg(
	                                    array(
	                                        'action' => 'move-up-menu-item',
	                                        'menu-item' => $item_id,
	                                    ),
	                                    remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
	                                ),
	                                'move-menu_item'
	                            );
	                        ?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up'); ?>">&#8593;</abbr></a>
	                        |
	                        <a href="<?php
	                            echo wp_nonce_url(
	                                add_query_arg(
	                                    array(
	                                        'action' => 'move-down-menu-item',
	                                        'menu-item' => $item_id,
	                                    ),
	                                    remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
	                                ),
	                                'move-menu_item'
	                            );
	                        ?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down'); ?>">&#8595;</abbr></a>
	                    </span>
	                    <a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php esc_attr_e('Edit Menu Item'); ?>" href="<?php
	                        echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
	                    ?>"><?php _e( 'Edit Menu Item' ); ?></a>
	                </span>
	            </dt>
	        </dl>
	
	        <div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
	            <?php if( 'custom' == $item->type ) : ?>
	                <p class="field-url description description-wide">
	                    <label for="edit-menu-item-url-<?php echo $item_id; ?>">
	                        <?php _e( 'URL' ); ?><br />
	                        <input type="text" 
                            	id="edit-menu-item-url-<?php echo $item_id; ?>" 
                                class="widefat code edit-menu-item-url" 
                                name="menu-item-url[<?php echo $item_id; ?>]" 
                                value="<?php echo esc_attr( $item->url ); ?>" />
	                    </label>
	                </p>
	            <?php endif; ?>
	            <p class="description description-thin">
	                <label for="edit-menu-item-title-<?php echo $item_id; ?>">
	                    <?php _e( 'Navigation Label' ); ?><br />
	                    <input type="text" 
                        	id="edit-menu-item-title-<?php echo $item_id; ?>" 
                            class="widefat edit-menu-item-title" 
                            name="menu-item-title[<?php echo $item_id; ?>]" 
                            value="<?php echo esc_attr( $item->title ); ?>" />
	                </label>
	            </p>
	            <p class="description description-thin">
	                <label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
	                    <?php _e( 'Title Attribute' ); ?><br />
	                    <input type="text" 
                        	id="edit-menu-item-attr-title-<?php echo $item_id; ?>" 
                            class="widefat edit-menu-item-attr-title" 
                            name="menu-item-attr-title[<?php echo $item_id; ?>]" 
                            value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
	                </label>
	            </p>
	            <p class="field-link-target description">
	                <label for="edit-menu-item-target-<?php echo $item_id; ?>">
	                    <input type="checkbox" 
                        	id="edit-menu-item-target-<?php echo $item_id; ?>" 
                            value="_blank" 
                            name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
	                    <?php _e( 'Open link in a new window/tab' ); ?>
	                </label>
	            </p>
	            <p class="field-css-classes description description-thin">
	                <label for="edit-menu-item-classes-<?php echo $item_id; ?>">
	                    <?php _e( 'CSS Classes (optional)' ); ?><br />
	                    <input type="text" 
                        	id="edit-menu-item-classes-<?php echo $item_id; ?>" 
                            class="widefat code edit-menu-item-classes" 
                            name="menu-item-classes[<?php echo $item_id; ?>]" 
                            value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
	                </label>
	            </p>
	            <p class="field-xfn description description-thin">
	                <label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
	                    <?php _e( 'Link Relationship (XFN)' ); ?><br />
	                    <input type="text" 
                        	id="edit-menu-item-xfn-<?php echo $item_id; ?>" 
                            class="widefat code edit-menu-item-xfn" 
                            name="menu-item-xfn[<?php echo $item_id; ?>]" 
                            value="<?php echo esc_attr( $item->xfn ); ?>" />
	                </label>
	            </p>
	            <p class="field-description description description-wide">
	                <label for="edit-menu-item-description-<?php echo $item_id; ?>">
	                    <?php _e( 'Description' ); ?><br />
	                    <textarea 
                        	id="edit-menu-item-description-<?php echo $item_id; ?>" 
                            class="widefat edit-menu-item-description" 
                            rows="3" cols="20" 
                            name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); ?></textarea>
	                    <span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.'); ?></span>
	                </label>
	            </p>        
	            <?php
	            /* New fields insertion starts here */
	            ?>      
	            <p class="field-custom description description-wide">
	                <label for="edit-menu-item-menuimage-<?php echo $item_id; ?>">
	                    <?php _e( 'Menu Icon' ); ?><br />
                        <img id="menu-icon-<?php echo $item_id; ?>" src="<?php echo esc_attr( $item->menuimage ); ?>" width="50"/><br/>
	                    <input type="hidden" 
                        	id="edit-menu-item-menuimage-<?php echo $item_id; ?>" 
                            class="widefat code edit-menu-item-custom" 
                            name="menu-item-menuimage[<?php echo $item_id; ?>]" 
                            value="<?php echo esc_attr( $item->menuimage ); ?>" />
                        <a id="bt-icon-add-<?php echo $item_id; ?>" 
                        	class="button button-primary" 
                            <?php if($item->menuimage != ''){ ?>style="display:none;"<? }?> 
                            onclick="getMenuIcon('<?php echo $item_id; ?>');">Add an Icon</a>
                        <a id="bt-icon-remove-<?php echo $item_id; ?>" 
                        	class="button button-primary" 
                            <?php if($item->menuimage == ''){ ?>style="display:none;"<? }?> 
                            onclick="removeMenuIcon('<?php echo $item_id; ?>');">Remove an Icon</a>
                            
	                </label>
	            </p>
	            <?php
	            /* New fields insertion ends here */
	            ?>
	            <div class="menu-item-actions description-wide submitbox">
	                <?php if( 'custom' != $item->type && $original_title !== false ) : ?>
	                    <p class="link-to-original">
	                        <?php printf( __('Original: %s'), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
	                    </p>
	                <?php endif; ?>
	                <a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
	                echo wp_nonce_url(
	                    add_query_arg(
	                        array(
	                            'action' => 'delete-menu-item',
	                            'menu-item' => $item_id,
	                        ),
	                        remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
	                    ),
	                    'delete-menu_item_' . $item_id
	                ); ?>"><?php _e('Remove'); ?></a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-<?php echo $item_id; ?>" href="<?php echo esc_url( add_query_arg( array('edit-menu-item' => $item_id, 'cancel' => time()), remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) ) ) );
	                    ?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Cancel'); ?></a>
	            </div>
	
	            <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
	            <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
	            <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
	            <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
	            <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
	            <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
	        </div><!-- .menu-item-settings-->
	        <ul class="menu-item-transport"></ul>
	    <?php
	    
	    $output .= ob_get_clean();

	    }
}

add_action( 'admin_init', 'sanook_menus_icon_scripts' ); 
function sanook_menus_icon_scripts() { 
	wp_enqueue_script( 'thickbox' ); 
	wp_enqueue_script( 'media-upload' ); 
	wp_enqueue_script( 'sanook-menu-icon', plugins_url( 'customize.js' , __FILE__ ));
	wp_enqueue_style( 'thickbox' );
	wp_enqueue_style( 'sanook-menu-icon', plugins_url( 'customize.css' , __FILE__ ));
}

add_action( 'wp_head', 'sanook_menus_icon_style' ); 
function sanook_menus_icon_style() { 
	echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('url').'/wp-content/plugins/sanooksookjai-menu-icon/customize.css'.'" />';
}

add_filter("attribute_escape", "button_menus_icons", 10, 2);
function button_menus_icons($safe_text, $text) {
	return str_replace("Insert into Post", "Set to Icon", $text);
}

add_filter( 'nav_menu_item_id', 'display_menu_icon',10, 3);
function display_menu_icon($item_id, $item, $args) {
	$args->link_before = '';
	if($item->menuimage != ''){
    	$args->link_before = '<img id="menu-icon-'.$item_id.'" class="menu-icons" src="'.$item->menuimage.'" alt="'.$item->title.'" title="'.$item->title.'"/>';
	}
}