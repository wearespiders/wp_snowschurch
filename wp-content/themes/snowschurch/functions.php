<?php
add_theme_support( 'post-thumbnails' );

if ( function_exists('register_sidebar') )
    register_sidebar();

function register_my_menus() {
  register_nav_menus(
    array(
      'header-menu' => __( 'Header Menu' ),
      'extra-menu' => __( 'Extra Menu' )
    )
  );
}
add_action( 'init', 'register_my_menus' );

/**
This class alters the HTML output of your Wordpress menu so that it will match the structure of the CSSMENUMAKER. This will allow us to simply drop in the CSS code to get a menu theme working.
 */
class CSS_Menu_Maker_Walker extends Walker {

  var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );
  
  function start_lvl( &$output, $depth = 0, $args = array() ) {
    $indent = str_repeat("\t", $depth);
    $output .= "\n$indent<ul>\n";
  }
  
  function end_lvl( &$output, $depth = 0, $args = array() ) {
    $indent = str_repeat("\t", $depth);
    $output .= "$indent</ul>\n";
  }
  
  function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
  
    global $wp_query;
    $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
    $class_names = $value = ''; 
    $classes = empty( $item->classes ) ? array() : (array) $item->classes;
    
    /* Add active class */
    if(in_array('current-menu-item', $classes)) {
      $classes[] = 'active';
      unset($classes['current-menu-item']);
    }
    
    /* Check for children */
    $children = get_posts(array('post_type' => 'nav_menu_item', 'nopaging' => true, 'numberposts' => 1, 'meta_key' => '_menu_item_menu_item_parent', 'meta_value' => $item->ID));
    if (!empty($children)) {
      $classes[] = 'has-sub';
    }
    
    $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
    $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
    
    $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
    $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
    
    $output .= $indent . '<li' . $id . $value . $class_names .'>';
    
    $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
    $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
    $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
    $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
    
    $item_output = $args->before;
    $item_output .= '<a'. $attributes .'>';
    $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
    $item_output .= '</a>';
    $item_output .= $args->after;
    
    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }
  
  function end_el( &$output, $item, $depth = 0, $args = array() ) {
    $output .= "</li>\n";
  }
}


/***********************************************************************
* @Author: Boutros AbiChedid 
* @Date:   February 14, 2011
* @Copyright: Boutros AbiChedid (http://bacsoftwareconsulting.com/)
* @Licence: Feel free to use it and modify it to your needs but keep the 
* Author's credit. This code is provided 'as is' without any warranties.
* @Function Name:  wp_bac_breadcrumb()
* @Version:  1.0 -- Tested up to WordPress version 3.1.2
* @Description: WordPress Breadcrumb navigation function. Adding a 
* breadcrumb trail to the theme without a plugin.
* This code does not support multi-page split numbering, attachments,
* custom post types and custom taxonomies.
***********************************************************************/
 
function wp_bac_breadcrumb() {   
    //Variable (symbol >> encoded) and can be styled separately.
    //Use >> for different level categories (parent >> child >> grandchild)
            $delimiter = '<span class="delimiter"> &raquo; </span>'; 
    //Use bullets for same level categories ( parent . parent )
    $delimiter1 = '<span class="delimiter1"> &bull; </span>';
     
    //text link for the 'Home' page
            $main = 'Home';  
    //Display only the first 30 characters of the post title.
            $maxLength= 30;
     
    //variable for archived year 
    $arc_year = get_the_time('Y'); 
    //variable for archived month 
    $arc_month = get_the_time('F'); 
    //variables for archived day number + full
    $arc_day = get_the_time('d');
    $arc_day_full = get_the_time('l');  
     
    //variable for the URL for the Year
    $url_year = get_year_link($arc_year);
    //variable for the URL for the Month    
    $url_month = get_month_link($arc_year,$arc_month);
 
    /*is_front_page(): If the front of the site is displayed, whether it is posts or a Page. This is true 
    when the main blog page is being displayed and the 'Settings > Reading ->Front page displays' 
    is set to "Your latest posts", or when 'Settings > Reading ->Front page displays' is set to 
    "A static page" and the "Front Page" value is the current Page being displayed. In this case 
    no need to add breadcrumb navigation. is_home() is a subset of is_front_page() */
     
    //Check if NOT the front page (whether your latest posts or a static page) is displayed. Then add breadcrumb trail.
    if (!is_front_page()) {         
        //If Breadcrump exists, wrap it up in a div container for styling. 
        //You need to define the breadcrumb class in CSS file.
        echo '<div class="breadcrumb">';
         
        //global WordPress variable $post. Needed to display multi-page navigations. 
        global $post, $cat;         
        //A safe way of getting values for a named option from the options database table. 
        $homeLink = get_option('home'); //same as: $homeLink = get_bloginfo('url');
        //If you don't like "You are here:", just remove it.
        echo '<a href="' . $homeLink . '">' . $main . '</a>' . $delimiter;    
         
        //Display breadcrumb for single post
        if (is_single()) { //check if any single post is being displayed.           
            //Returns an array of objects, one object for each category assigned to the post.
            //This code does not work well (wrong delimiters) if a single post is listed 
            //at the same time in a top category AND in a sub-category. But this is highly unlikely.
            $category = get_the_category();
            $num_cat = count($category); //counts the number of categories the post is listed in.
             
            //If you have a single post assigned to one category.
            //If you don't set a post to a category, WordPress will assign it a default category.
            if ($num_cat <=1)  //I put less or equal than 1 just in case the variable is not set (a catch all).
            {
                echo get_category_parents($category[0],  true,' ' . $delimiter . ' ');
                //Display the full post title.
                echo ' ' . get_the_title(); 
            }
            //then the post is listed in more than 1 category.  
            else { 
                //Put bullets between categories, since they are at the same level in the hierarchy.
                echo the_category( $delimiter1, multiple); 
                    //Display partial post title, in order to save space.
                    if (strlen(get_the_title()) >= $maxLength) { //If the title is long, then don't display it all.
                        echo ' ' . $delimiter . trim(substr(get_the_title(), 0, $maxLength)) . ' ...';
                    }                         
                    else { //the title is short, display all post title.
                        echo ' ' . $delimiter . get_the_title(); 
                    } 
            }           
        } 
        //Display breadcrumb for category and sub-category archive
        elseif (is_category()) { //Check if Category archive page is being displayed.
            //returns the category title for the current page. 
            //If it is a subcategory, it will display the full path to the subcategory. 
            //Returns the parent categories of the current category with links separated by '»'
            echo 'Archive Category: "' . get_category_parents($cat, true,' ' . $delimiter . ' ') . '"' ;
        }       
        //Display breadcrumb for tag archive        
        elseif ( is_tag() ) { //Check if a Tag archive page is being displayed.
            //returns the current tag title for the current page. 
            echo 'Posts Tagged: "' . single_tag_title("", false) . '"';
        }        
        //Display breadcrumb for calendar (day, month, year) archive
        elseif ( is_day()) { //Check if the page is a date (day) based archive page.
            echo '<a href="' . $url_year . '">' . $arc_year . '</a> ' . $delimiter . ' ';
            echo '<a href="' . $url_month . '">' . $arc_month . '</a> ' . $delimiter . $arc_day . ' (' . $arc_day_full . ')';
        } 
        elseif ( is_month() ) {  //Check if the page is a date (month) based archive page.
            echo '<a href="' . $url_year . '">' . $arc_year . '</a> ' . $delimiter . $arc_month;
        } 
        elseif ( is_year() ) {  //Check if the page is a date (year) based archive page.
            echo $arc_year;
        }       
        //Display breadcrumb for search result page
        elseif ( is_search() ) {  //Check if search result page archive is being displayed. 
            echo 'Search Results for: "' . get_search_query() . '"';
        }       
        //Display breadcrumb for top-level pages (top-level menu)
        elseif ( is_page() && !$post->post_parent ) { //Check if this is a top Level page being displayed.
            echo get_the_title();
        }           
        //Display breadcrumb trail for multi-level subpages (multi-level submenus)
        elseif ( is_page() && $post->post_parent ) {  //Check if this is a subpage (submenu) being displayed.
            //get the ancestor of the current page/post_id, with the numeric ID 
            //of the current post as the argument. 
            //get_post_ancestors() returns an indexed array containing the list of all the parent categories.                
            $post_array = get_post_ancestors($post);
             
            //Sorts in descending order by key, since the array is from top category to bottom.
            krsort($post_array); 
             
            //Loop through every post id which we pass as an argument to the get_post() function. 
            //$post_ids contains a lot of info about the post, but we only need the title. 
            foreach($post_array as $key=>$postid){
                //returns the object $post_ids
                $post_ids = get_post($postid);
                //returns the name of the currently created objects 
                $title = $post_ids->post_title; 
                //Create the permalink of $post_ids
                echo '<a href="' . get_permalink($post_ids) . '">' . $title . '</a>' . $delimiter;
            }
            the_title(); //returns the title of the current page.               
        }           
        //Display breadcrumb for author archive   
        elseif ( is_author() ) {//Check if an Author archive page is being displayed.
            global $author;
            //returns the user's data, where it can be retrieved using member variables. 
            $user_info = get_userdata($author);
            echo  'Archived Article(s) by Author: ' . $user_info->display_name ;
        }       
        //Display breadcrumb for 404 Error 
        elseif ( is_404() ) {//checks if 404 error is being displayed 
            echo  'Error 404 - Not Found.';
        }       
        else {
            //All other cases that I missed. No Breadcrumb trail.
        }
       echo '</div>';     
    }   
}


/******************
remove classes from wp_list_pages
*******************/
 
function remove_page_class($wp_list_pages) {
	$pattern = '/\<li class="page_item[^>]*>/';
	$replace_with = '<li>';
	$pattern2 = "/\<ul class='children'>/";
	$replace_with2 = '<ul>';
	$result1= preg_replace($pattern, $replace_with, $wp_list_pages);
	$result2= preg_replace($pattern2, $replace_with2, $result1);
	return $result2;
}
add_filter('wp_list_pages', 'remove_page_class');

/***********************************************************************
* @URL:   http://christianvarga.com/blog/2012/12/how-to-get-submenu-items-from-a-wordpress-menu-based-on-parent-or-sibling/
* @Description: To get submenu items from a WordPress menu based on parent or sibling
***********************************************************************/
 
function my_custom_submenu() {
  global $post;
  
  $menu_items = wp_get_nav_menu_items('Menu');
  $current_menu_id = 0;
  
  // get current top level menu item id
  foreach ( $menu_items as $item ) {
    if ( $item->object_id == $post->ID ) {
      // if it's a top level page, set the current id as this page. if it's a subpage, set the current id as the parent
      $current_menu_id = ( $item->menu_item_parent ) ? $item->menu_item_parent : $item->ID;
      break;
    }
  }
 
  // display the submenu
  //echo "<ul><li>".the_title()."</li>";
  echo "<ul class='side-rightbar-menu' style='padding:0px; margin:0px; list-style:none;'>";
  foreach ( $menu_items as $item ) {
    if ( $item->menu_item_parent == $current_menu_id ) {
      //$class = ( $item->object_id == $post->ID ) ? "class='current_page_item'" : "";
      echo "<li {$class}><a href='{$item->url}'>{$item->title}</a></li>";
    }
  }
  
  echo "</ul>";
}

/*************************************
Integrating Disqus Into WordPress Without a Plugin
**************************************/
function disqus_embed($disqus_shortname) {
    global $post;
    wp_enqueue_script('disqus_embed','http://'.$disqus_shortname.'.disqus.com/embed.js');
    echo '<div id="disqus_thread"></div>
    <script type="text/javascript">
        var disqus_shortname = "'.$disqus_shortname.'";
        var disqus_title = "'.$post->post_title.'";
        var disqus_url = "'.get_permalink($post->ID).'";
        var disqus_identifier = "'.$disqus_shortname.'-'.$post->ID.'";
    </script>';
}

function getFBCountLikes()
{
$source_url = "https://www.facebook.com/ourladyofsnows";  //This could be anything URL source including stripslashes($_POST['url'])
$url = "http://api.facebook.com/restserver.php?method=links.getStats&urls=".urlencode($source_url);
$xml = file_get_contents($url);
$xml = simplexml_load_string($xml);
//$shares =  $xml->link_stat->share_count;
$likes =  $xml->link_stat->like_count;
/*$comments = $xml->link_stat->comment_count;
$total = $xml->link_stat->total_count;
$max = max($shares,$likes,$comments);*/
echo (number_format(floatval($likes)));
}
?>
