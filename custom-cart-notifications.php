<?php 
/*
 * Plugin Name: Custom Cart Notifications
 * Author: One-pix
 * Author URI: one-pix.ru
 */

/*if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
}*/
class Custom_Cart_Notifications {

	public $post_type_name = 'ccn_message';
	
	public function __construct () {
		$this->load();
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'plugin_page_settings_link') );
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_menu' ), 100 );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_ccn_admin_assets') );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_ccn_frontend_assets') );
		add_action( 'init', array( $this, 'ccn_post_type' ) );
		/*add_action( 'init', array( $this, 'ccn_tax_registration' ) );*/
		add_action( 'admin_menu', array( $this, 'ccn_add_submenu_woocommerce' ) );
		add_filter( 'manage_edit-' . $this->post_type_name . '_columns', array( $this, 'edit_columns' ) );
		add_action( 'manage_' . $this->post_type_name . '_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );
		add_action( 'add_meta_boxes',  [$this, 'add_message_metabox'] );
		add_action( 'save_post_' . $this->post_type_name, [$this, 'save_ccn_message_data'], 10, 3 );
		add_action( 'woocommerce_before_cart', array($this, 'before_cart_content_ccn_notification') );
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'ccn_messages_update') );
		add_action( 'admin_action_duplicate_ccn_message_as_draft', array( $this, 'duplicate_ccn_message_as_draft') );
		add_filter( 'post_row_actions', array( $this, 'duplicate_ccn_message_link'), 10, 2 );

		add_action( 'wp_ajax_nopriv_get_layout_color', array( $this, 'get_layout_color_handler' ) );
		add_action( 'wp_ajax_get_layout_color', array( $this, 'get_layout_color_handler' ) );

		add_action( 'wp_ajax_nopriv_get_message_data', array( $this, 'get_message_data_handler' ) );
		add_action( 'wp_ajax_get_message_data', array( $this, 'get_message_data_handler' ) );
	}

	public function load() {

    }
    
    private function free_shipping_threshold_order_amount() {

    	if(get_option('ccn_free_shipping_threshold_order_amount')):
    		$free_shipping_threshold_order_amount = get_option('ccn_free_shipping_threshold_order_amount');
    	else:
    		$free_shipping_threshold_order_amount = 0;
    	endif;
           
    	return $free_shipping_threshold_order_amount;
    
    }
    

    private function plugin_nonce_name() {
    	return basename(__FILE__);
    }

	private function plugin_dir(){
		return plugin_dir_url( __FILE__ );
	}

	public function register_ccn_admin_assets() {
		wp_register_style('ccn-datepicker-styles', plugins_url('assets/libs/datepicker.css', __FILE__));
		wp_register_style('ccn-clockpicker-styles', plugins_url('assets/libs/clockpicker.css', __FILE__));
		wp_register_style('ccn-colorpicker-styles', plugins_url('assets/libs/colorpicker.min.css', __FILE__));
        wp_register_style('ccn-admin-styles', plugins_url('assets/css/admin-style.css', __FILE__));
        
        wp_register_script('ccn-datepicker-scripts', plugins_url('assets/libs/datepicker.min.js', __FILE__));
        wp_register_script('ccn-clockpicker-scripts', plugins_url('assets/libs/clockpicker.min.js', __FILE__));
        wp_register_script('ccn-colorpicker-scripts', plugins_url('assets/libs/colorpicker.min.js', __FILE__));
        wp_register_script('ccn-admin-scripts', plugins_url('assets/js/admin-scripts.js', __FILE__));

        wp_enqueue_style('ccn-datepicker-styles');
        wp_enqueue_style('ccn-clockpicker-styles');
        wp_enqueue_style('ccn-colorpicker-styles');
		
        if ( is_admin() && (get_current_screen()->post_type == $this->post_type_name || ( isset($_GET['page']) && $_GET['page'] == 'ccn_layouts') || ( isset($_GET['page']) && $_GET['page'] == 'ccn_upgrade') ) ) {        
        	wp_enqueue_style('ccn-admin-styles');
    	}

        wp_enqueue_script('ccn-datepicker-scripts');  
        wp_enqueue_script('ccn-clockpicker-scripts');  
        wp_enqueue_script('ccn-colorpicker-scripts');  
        wp_enqueue_script('ccn-admin-scripts');
        wp_localize_script( 'ccn-admin-scripts', 'ccnAjax', array(
	    	'url'	=> admin_url( 'admin-ajax.php' ),
	  	));
	}

	public function plugin_page_settings_link( $links ) {

		array_unshift($links, '<a href="' . admin_url( 'edit.php?post_type=' . $this->post_type_name ) . '">' . __('Configure') . '</a>');
		return $links;
	}

	public function register_ccn_frontend_assets(){
		wp_register_style('front-end-styles', plugins_url('assets/css/front-end-styles.css', __FILE__));
		wp_enqueue_style('front-end-styles');  
	}

	public function add_admin_bar_menu() {

		global $wp_admin_bar;

		if ( !is_admin() ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		/*if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! empty( $this->settings['admin_tabs'] ) ) {
			foreach ( $this->settings['admin-tabs'] as $item => $title ) {

				$wp_admin_bar->add_menu( array(
					'parent' => $this->settings['parent'],
					'title'  => $title,
					'id'     => $this->settings['parent'] . '-' . $item,
					'href'   => admin_url( 'themes.php' ) . '?page=' . $this->settings['parent_page'] . '&tab=' . $item,
				) );
			}
		}*/
		global $pagenow;
		$current_page = get_current_screen();

        if ( ($current_page->post_type == $this->post_type_name) ) {
			require_once 'templates/page-top-bar.php';			
		}

	}

	public function ccn_post_type() {

		$labels = array(
			'name'               => __( 'Cart Messages', 'Post Type General Name', 'custom-cart-messages' ),
			'singular_name'      => __( 'Cart Message', 'Post Type Singular Name', 'custom-cart-messages' ),
			'menu_name'          => __( 'Cart Message', 'custom-cart-messages' ),
			'parent_item_colon'  => __( 'Parent Item:', 'custom-cart-messages' ),
			'all_items'          => __( 'All Messages', 'custom-cart-messages' ),
			'view_item'          => __( 'View Messages', 'custom-cart-messages' ),
			'add_new_item'       => __( 'Add New Message', 'custom-cart-messages' ),
			'add_new'            => __( 'Add New Message', 'custom-cart-messages' ),
			'edit_item'          => __( 'Edit Message', 'custom-cart-messages' ),
			'update_item'        => __( 'Update Message', 'custom-cart-messages' ),
			'search_items'       => __( 'Search Message', 'custom-cart-messages' ),
			'not_found'          => __( 'Not found', 'custom-cart-messages' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'custom-cart-messages' ),
		);
		$args   = array(
			'label'               => __( 'ccn_message', 'custom-cart-messages' ),
			'description'         => __( 'Cart Message Description', 'custom-cart-messages' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
		);
		register_post_type( $this->post_type_name, $args );
	}

	/*public function ccn_tax_registration() {
		$labels = array(
		    'name'              => __( 'Message Layouts', 'textdomain' ),
		    'singular_name'     => __( 'Message Layout', 'textdomain' ),
		    'search_items'      => __( 'Search Layouts', 'textdomain' ),
		    'all_items'         => __( 'All Message Layouts', 'textdomain' ),
		    'parent_item'       => __( 'Parent Layout', 'textdomain' ),
		    'parent_item_colon' => __( 'Parent Layout:', 'textdomain' ),
		    'edit_item'         => __( 'Edit Layout', 'textdomain' ), 
		    'update_item'       => __( 'Update Layout', 'textdomain' ),
		    'add_new_item'      => __( 'Add New Layout', 'textdomain' ),
		    'new_item_name'     => __( 'New Layout', 'textdomain' ),
		    'menu_name'         => __( 'Message Layouts', 'textdomain' ),
		);
		$args = array(
		    'labels'            => $labels,
		    'public'            => true,
		    'show_admin_column' => true,
		    'show_ui'           => true,
		    'hierarchical'      => false,
		    'capabilities'      => array(
		        'manage_terms'  => 'edit_posts',
		        'edit_terms'    => 'edit_posts',
		        'delete_terms'  => 'edit_posts',
		        'assign_terms'  => 'edit_posts'
		    )
		);
		register_taxonomy( 'ccn_layout', $this->post_type_name, $args );
	}*/

	public function ccn_add_submenu_woocommerce() {
		add_submenu_page(
			'woocommerce',
			__( 'Custom Cart Notifications', 'custom-cart-messages' ),
			__( 'Custom Cart Notifications', 'custom-cart-messages' ),
			'manage_woocommerce',
			'edit.php?post_type=' . $this->post_type_name,
			false
		);
		add_submenu_page(
			null,
			__( 'Layouts for Custom Cart Notifications', 'custom-cart-messages' ),
			__( 'Layouts for Custom Cart Notifications', 'custom-cart-messages' ),
			'manage_options',
			'ccn_layouts',
			function(){ require_once 'templates/page-top-bar.php'; require_once 'templates/page-layouts.php';},
		);
		add_submenu_page(
			null,
			__( 'Upgrade Custom Cart Notifications Plugin', 'custom-cart-messages' ),
			__( 'Upgrade Custom Cart Notifications Plugin', 'custom-cart-messages' ),
			'manage_options',
			'ccn_upgrade',
			function(){ require_once 'templates/page-top-bar.php'; require_once 'templates/page-upgrade.php';},
		);
	}

	public function edit_columns( $columns ) {

		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'title'       => __( 'Name', 'custom-cart-messages' ),
			'date'        => __( 'Date Modified', 'custom-cart-messages' ),
			'type'        => __( 'Message Type', 'custom-cart-messages' ),

		);

		return $columns;
	}

	public function custom_columns( $column, $post_id ) {

		$type = get_post_meta( $post_id, 'ccn_message_type', true );

		switch ( $column ) {
			case 'type':
				$types = $this->get_types();
				if ( isset( $types[ $type ] ) ) {
					echo esc_html( $types[ $type ]['title'] );
				}
				break;
		}
	}

	public function get_types(){

		$types = array(
			'simple_message'		=> array(
				'title'		=> __( 'Simple Message', 'custom-cart-messages' ),
				'notice' 	=> __('', 'custom-cart-messages'),
			),
			'minimum_amount'		=> array(
				'title' 	=> __( 'Minimum Amount', 'custom-cart-messages' ),
				'notice' 	=> __('Use "{remaining_amount}" to show the remaining order threshold for free shipping.', 'custom-cart-messages'),
			),
			'cart_products'			=> array(
				'title' 	=> __( 'Products in the Cart (PRO)', 'custom-cart-messages' ),
				'notice' 	=> __('', 'custom-cart-messages'),
			),
			'cart_categories'		=> array(
				'title' 	=> __( 'Categories in the Cart (PRO)', 'custom-cart-messages' ),
				'notice' 	=> __('', 'custom-cart-messages'),
			),
			'product_sale'   		=> array(
				'title' 	=> __( 'Product Related Sale Countdown (PRO)', 'custom-cart-messages' ),
				'notice' 	=> __('', 'custom-cart-messages'),
			),
			'deadline'				=>  array(
				'title' 	=> __( 'Deadline (PRO)', 'custom-cart-messages' ),
				'notice' 	=> __('', 'custom-cart-messages'),
			),
			'url_related_message'	=> array(
				'title' 	=> __( 'URL Related Message (PRO)', 'custom-cart-messages' ),
				'notice' 	=> __('', 'custom-cart-messages'),
			),
			'ref_url_related_message'   => array(
				'title' 	=>__( 'Referrer URL Related Message (PRO)', 'custom-cart-messages' ),
				'notice' 	=> __('', 'custom-cart-messages'),
			),
		);

		return $types;
	}

	public function add_message_metabox(){
   		add_meta_box( 'ccn_message_metabox', __('Message Settings','custom-cart-messages'), [$this, 'message_metabox_fields'], $this->post_type_name, 'advanced', 'core' );
  	}

	public function message_metabox_fields( $post_id  ) {

		require_once 'templates/plugin-options.php';

	}

	public function save_ccn_message_data( $post_id ) {

		if ( isset( $_POST['ccn_message_active'] ) ) {
			update_post_meta( $post_id, 'ccn_message_active', $_POST['ccn_message_active'] );
		} else {
			$ccn_message_active = 0;
			update_post_meta( $post_id, 'ccn_message_active', $ccn_message_active );
		}

		if ( isset( $_POST['ccn_message_type'] ) ) {
			update_post_meta( $post_id, 'ccn_message_type', $_POST['ccn_message_type'] );
		}

		if ( isset( $_POST['minimum_order_amount'] ) ) {
			update_post_meta( $post_id, 'minimum_order_amount', $_POST['minimum_order_amount'] );
		}

		if ( isset( $_POST['ccn_message_header'] ) ) {
			update_post_meta( $post_id, 'ccn_message_header', $_POST['ccn_message_header'] );
		}

		if ( isset( $_POST['ccn_message_header_tag'] ) ) {
			update_post_meta( $post_id, 'ccn_message_header_tag', $_POST['ccn_message_header_tag'] );
		}

		if ( isset( $_POST['ccn_message_text'] ) ) {
			update_post_meta( $post_id, 'ccn_message_text', $_POST['ccn_message_text'] );
		}

		if ( isset( $_POST['ccn_button_text'] ) ) {
			update_post_meta( $post_id, 'ccn_button_text', $_POST['ccn_button_text'] );
		}

		if ( isset( $_POST['ccn_button_url'] ) ) {
			update_post_meta( $post_id, 'ccn_button_url', $_POST['ccn_button_url'] );
		}

		if ( isset( $_POST['ccn_message_layout'] ) ) {
			update_post_meta( $post_id, 'ccn_message_layout', $_POST['ccn_message_layout'] );
		}

		if ( isset( $_POST['ccn_new_tab'] ) ) {
			update_post_meta( $post_id, 'ccn_new_tab', $_POST['ccn_new_tab'] );
		} else {
			$ccn_new_tab = 0;
			update_post_meta( $post_id, 'ccn_new_tab', $ccn_new_tab );
		}

		if ( isset( $_POST['ccn_free_shipping_threshold_order_amount'] ) ) {
			update_option( 'ccn_free_shipping_threshold_order_amount', $_POST['ccn_free_shipping_threshold_order_amount'] );
		}
	}

	public function message_template( $message_id ) {

		$message = '';
		$layout = get_post_meta( $message_id, 'ccn_message_layout', true );
		$layout_colors = $this->layout_colors($layout);

		$ccn_layout_box_border_color = $layout_colors['ccn_layout_box_border_color'];
		$ccn_layout_box_background_color = $layout_colors['ccn_layout_box_background_color'];
		$ccn_layout_box_text_color = $layout_colors['ccn_layout_box_text_color'];
		$ccn_layout_button_background_color = $layout_colors['ccn_layout_button_background_color'];
		$ccn_layout_button_text_color = $layout_colors['ccn_layout_button_text_color'];
		$ccn_layout_button_background_color_on_hover = $layout_colors['ccn_layout_button_background_color_on_hover'];
		$ccn_message_header = get_post_meta( $message_id, 'ccn_message_header', true );
		$ccn_message_header_tag = get_post_meta( $message_id, 'ccn_message_header_tag', true );
		$ccn_message_text = get_post_meta( $message_id, 'ccn_message_text', true );
		$ccn_button_url = get_post_meta( $message_id, 'ccn_button_url', true );
		$ccn_button_text = get_post_meta( $message_id, 'ccn_button_text', true );
		$ccn_new_tab = get_post_meta( $message_id, 'ccn_new_tab', true );
		$minimum_order_amount = get_post_meta($message_id, 'minimum_order_amount', true);
		if ( $ccn_new_tab == 1 ) {
			$ccn_target_blank = 'target="_blank"';
		} else {
			$ccn_target_blank = '';
		}
		
		$cart_total = WC()->cart->cart_contents_total;
		$remaining_amount = number_format($this->free_shipping_threshold_order_amount() - $cart_total, 2);
		if ( $remaining_amount <= 0 ) {
			$remaining_amount = number_format(0, 2);
		}
			
			$message .= '<div class="ccn-message-preview__content" style="border-color:' . $ccn_layout_box_border_color . '; background-color:'. $ccn_layout_box_background_color . ';">';
				
				$message .= '<' . $ccn_message_header_tag . ' class="ccn-message-preview__title" style="color:' . $ccn_layout_box_text_color . ';">' . $ccn_message_header . '</' . $ccn_message_header_tag . '>';
				
				$message .= '<div class="ccn-message-preview__text" style="color:' . $ccn_layout_box_text_color . ';">';
					$message .= str_replace('{remaining_amount}', get_woocommerce_currency_symbol() . $remaining_amount, $ccn_message_text);
				
				$message .= '</div>';
			
				$message .= '<a ' . $ccn_target_blank . ' href="' . $ccn_button_url . '" class="ccn-message-preview__button" style="background-color:'. $ccn_layout_button_background_color . '; color: ' . $ccn_layout_button_text_color . ';" onMouseOver="this.style.backgroundColor=`' . $ccn_layout_button_background_color_on_hover . '`"  onMouseOut="this.style.backgroundColor=`' . $ccn_layout_button_background_color . '`">' . $ccn_button_text . '</a>';
			$message .= '</div>';
		return $message;
	}

	public function before_cart_content_ccn_notification_html(){

		$message_html = '<div class="ccn-before_cart_content">';

		$args = array(
			'post_type'     => $this->post_type_name,
			'posts_per_page'=> -1,
			'orderby'       => 'id',
			'order'         => 'DESC',
			'meta_query'	=> array(
				'relationship'	=> 'AND',
				array(
					'key'     => 'ccn_message_active',
					'value'   => 1,
					'compare' => '=',
				),
				array(
					'key'     => 'ccn_message_type',
					'value'   => 'simple_message',
					'compare' => '=',
				),
			),
		);
		
		$messages = new WP_Query($args);
		if( $messages->have_posts() ):
			while( $messages->have_posts() ): $messages->the_post();
				
				$message_id = get_the_ID();

				$message_html .= $this->message_template( $message_id );
			
			endwhile;
			wp_reset_postdata();
		endif;

		$cart_total = WC()->cart->cart_contents_total;

		$args = array(
			'post_type'     => $this->post_type_name,
			'posts_per_page'=> -1,
			'orderby'       => 'id',
			'order'         => 'DESC',
			'meta_query'	=> array(
				'relationship'	=> 'AND',
				array(
					'key'     => 'ccn_message_active',
					'value'   => 1,
					'compare' => '=',
				),
				array(
					'key'     => 'ccn_message_type',
					'value'   => 'minimum_amount',
					'compare' => '=',
				),
				array(
					'key'     => 'minimum_order_amount',
					'value'   => $cart_total,
					'compare' => '<=',
					'type'	  => 'NUMERIC'
				),
			),
		);
		
		$messages = new WP_Query($args);
		if( $messages->have_posts() ):
			while( $messages->have_posts() ): $messages->the_post();
				
				$message_id = get_the_ID();

				$message_html .= $this->message_template( $message_id );
			
			endwhile;
			wp_reset_postdata();
		endif;

		$message_html .= '</div>';
		return $message_html;
	}

	public function before_cart_content_ccn_notification() {
		
		$messages = $this->before_cart_content_ccn_notification_html();
		echo $messages;
	}

	private function layout_colors_data(){
		$layout_colors = array(
			'information_layout'  => array(
				'title'											=> 'Information layout',
				'ccn_layout_box_background_color' 				=> '#fff',
				'ccn_layout_button_background_color' 			=> '#99baef',
				'ccn_layout_box_border_color' 					=> '#99baef',
				'ccn_layout_button_background_color_on_hover'	=> '#0073aa',
				'ccn_layout_box_text_color' 					=> '#000',
				'ccn_layout_button_text_color' 					=> '#fff',
			),
			'warning_layout'   => array(
				'title'											=> 'Warning layout',
				'ccn_layout_box_background_color' 				=> '#bce5b1',
				'ccn_layout_button_background_color' 			=> '#197112',
				'ccn_layout_box_border_color' 					=> '#00ff1a',
				'ccn_layout_button_background_color_on_hover'	=> '#26ad0b',
				'ccn_layout_box_text_color' 					=> '#000',
				'ccn_layout_button_text_color' 					=> '#fff',
			),
		);
		return $layout_colors;
	}

	private function layout_colors( $layout='information_layout' ){

		$layout_colors = $this->layout_colors_data();
		return $layout_colors[$layout];
	}

	public function get_layout_color_handler() {
		if( isset($_POST['layout']) ){
			$layout = $_POST['layout'];
		}
		$layout_colors = $this->layout_colors($layout);
		wp_send_json( $layout_colors );
	}

	public function ccn_messages_update( $array ){
		$array['.ccn-before_cart_content'] = $this->before_cart_content_ccn_notification_html();
		return $array;
	}

	public function duplicate_ccn_message_as_draft(){
		global $wpdb;
		if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'admin_action_duplicate_ccn_message_as_draft' == $_REQUEST['action'] ) ) ) {
			wp_die('No post to duplicate has been supplied!');
		}
	 
		/*
		 * Nonce verification
		 */
		if ( !isset( $_GET['duplicate_nonce'] ) || !wp_verify_nonce( $_GET['duplicate_nonce'], $this->plugin_nonce_name() ) )
			return;
	 
		/*
		 * get the original post id
		 */
		$post_id = (isset($_GET['post']) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );
		/*
		 * and all the original post data then
		 */
		$post = get_post( $post_id );
	 
		/*
		 * if you don't want current user to be the new post author,
		 * then change next couple of lines to this: $new_post_author = $post->post_author;
		 */
		$current_user = wp_get_current_user();
		$new_post_author = $current_user->ID;
	 
		/*
		 * if post data exists, create the post duplicate
		 */
		if (isset( $post ) && $post != null) {
	 
			/*
			 * new post data array
			 */
			$args = array(
				'comment_status' => $post->comment_status,
				'ping_status'    => $post->ping_status,
				'post_author'    => $new_post_author,
				'post_content'   => $post->post_content,
				'post_excerpt'   => $post->post_excerpt,
				'post_name'      => $post->post_name,
				'post_parent'    => $post->post_parent,
				'post_password'  => $post->post_password,
				'post_status'    => 'draft',
				'post_title'     => $post->post_title,
				'post_type'      => $post->post_type,
				'to_ping'        => $post->to_ping,
				'menu_order'     => $post->menu_order
			);
	 
			/*
			 * insert the post by wp_insert_post() function
			 */
			$new_post_id = wp_insert_post( $args );
	 
			/*
			 * get all current post terms ad set them to the new post draft
			 */
			$taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
			foreach ($taxonomies as $taxonomy) {
				$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
				wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
			}
	 
			/*
			 * duplicate all post meta just in two SQL queries
			 */
			$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
			if (count($post_meta_infos)!=0) {
				$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
				foreach ($post_meta_infos as $meta_info) {
					$meta_key = $meta_info->meta_key;
					if( $meta_key == '_wp_old_slug' ) continue;
					$meta_value = addslashes($meta_info->meta_value);
					$sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
				}
				$sql_query.= implode(" UNION ALL ", $sql_query_sel);
				$wpdb->query($sql_query);
			}
	 
	 
			/*
			 * finally, redirect to the edit post screen for the new draft
			 */
			wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
			exit;
		} else {
			wp_die('Post creation failed, could not find original post: ' . $post_id);
		}
	}

	public function duplicate_ccn_message_link( $actions, $post ) {
		if (current_user_can('edit_posts')) {
			$actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=duplicate_ccn_message_as_draft&post=' . $post->ID, $this->plugin_nonce_name(), 'duplicate_nonce' ) . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
		}
		return $actions;
	}

	public function get_message_data_handler(){
		if ( isset( $_POST['message_type'] ) ) {
			$message_type = $_POST['message_type'];
		}
		$types = $this->get_types();
		$notice = $types[$message_type]['notice'];

		wp_send_json(array(
			'notice' => $notice, 
		));
	}
}
new Custom_Cart_Notifications();