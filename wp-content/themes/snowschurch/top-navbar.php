<div class="top-navbar top-navbar-gradient">
    <div id='cssmenu'>
    <!--?php wp_page_menu('title_li=&sort_column=menu_order&show_home=1') ?-->
    <?php 
wp_nav_menu(array(
  'menu' => 'Main Menu', 
  'container_id' => 'cssmenu', 
  'walker' => new CSS_Menu_Maker_Walker()
)); 
?>
    </div>
</div>