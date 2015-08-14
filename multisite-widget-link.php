<?php
/*
Plugin Name: MultiSite Widget Link 
Plugin URI: http://ecolosites.eelv.fr/multisite-widget-link/
Description: Easily add widgets to link another blog in a multisite instance
Version: 1.0.1
Author: bastho
Author URI: http://ecolosites.eelv.fr/
License: GPLv2
Domain Path: /languages/
Tags: widget,banner,multisite,network
Network: 1
*/


load_plugin_textdomain( 'multisite_widget_link', false, 'multisite-widget-link/languages' );

class MultiSiteWidgetLink extends WP_Widget {
	function __construct() {
		// Instantiate the parent object
		parent::__construct( false, __('Link to neighbor site','multisite_widget_link') );
	}
        function MultiSiteWidgetLink(){
            $this->__construct();
        }

	function widget( $args, $instance ) {
	   global $wpdb;
	   $blog_id=isset($instance['blog_id'])?$instance['blog_id']:1;
	   $show_banner=isset($instance['show_banner'])?$instance['show_banner']:1;
	   $title=isset($instance['title'])?$instance['title']:'';
	   
	   $blog = get_blog_details($blog_id);
	   $img = get_blog_option($blog_id,'header_img');
	   if($show_banner==0 || empty($img)){
			$img =  $blog->blogname;  
	   }
	   else{
			$img='<img src="'.$img.'" alt="'.str_replace('"','',$blog->blogname).'"/>';   
	   }
	   echo $args['before_widget'];
	   if($title!=''){
	   	echo $args['before_title'].$title.$args['after_title'];
	   }
	   echo $args['before_content'];?>
      <a href="<?php echo  $blog->siteurl; ?>"><?php echo $img  ?></a>
      <?php echo $args['after_content']. $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {
		if( isset($_REQUEST['action']) && $_REQUEST['action']=='save-widget' ){
			$instance = array();
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['blog_id'] = strip_tags( $new_instance['blog_id'] );
			$instance['show_banner'] = abs( $new_instance['show_banner'] );
			return $instance;
		}
	}

	function form( $instance ) {
		global $wpdb;
		$blog_id=isset($instance['blog_id'])?$instance['blog_id']:1;
		$show_banner=isset($instance['show_banner'])?$instance['show_banner']:1;
		$title=isset($instance['title'])?$instance['title']:'';
		
		?>
		<input type="hidden" id="<?php echo $this->get_field_id('title'); ?>-title" value="<?php echo $title; ?>">
       <p>
       <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title','eventpost'); ?>
       <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
       </label>
       </p>
       
        <label for="<?php echo $this->get_field_id( 'blog_id' ); ?>"><?php  _e('Site :','multisite_widget_link') ?> 
            <select name="<?php echo $this->get_field_name( 'blog_id' ); ?>" id="<?php echo $this->get_field_id( 'blog_id' ); ?>">
            <?php
            $sql = 'SELECT `blog_id`,`domain` FROM `'.$wpdb->blogs.'` WHERE `public`=1 AND `archived`=\'0\' AND `mature`=0 AND `spam`=0 AND  `deleted`=0 ORDER BY `domain`';
           $blogs_list = $wpdb->get_results($wpdb->prepare($sql,''));
           foreach ($blogs_list as $blog): ?>
             <option value="<?php echo $blog->blog_id; ?>" <?php if($blog->blog_id==$blog_id){ echo' selected';}?>><?php echo $blog->domain; ?></option>
             <?php endforeach; ?>
            </select>
        </label>
        <label for="<?php echo $this->get_field_id( 'show_banner' ); ?>"><?php  _e('Banner :','multisite_widget_link') ?> 
            <select type="checkbox" name="<?php echo $this->get_field_name( 'show_banner' ); ?>" id="<?php echo $this->get_field_id( 'show_banner' ); ?>">
            	<option value='1' <?php if($show_banner==1){ echo'selected'; }?>><?php  _e('Show','multisite_widget_link') ?></option>
                <option value='0' <?php if($show_banner==0){ echo'selected'; }?>><?php  _e('Hide','multisite_widget_link') ?></option>
            </select>			
        </label>
        <?php
	}
}

function MultiSiteWidgetLink_register_widgets() {
	register_widget( 'MultiSiteWidgetLink' );
}

add_action( 'widgets_init', 'MultiSiteWidgetLink_register_widgets' );
