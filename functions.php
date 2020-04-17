<?php
/*
===========================================
    Require Theme Files
===========================================
 */
require get_theme_file_path('/inc/like-route.php');
require get_theme_file_path('/inc/search-route.php');

/*
===========================================
    Theme Page Banner
===========================================
 */
function pageBanner($args = NULL) {
    if (!$args['title']) {
        $args['title'] = get_the_title();
    }

    if (!$args['subtitle']) {
        $args['subtitle'] = get_field('page_banner_subtitle');
    }

    if (!$args['bg_image']) {
        if (get_field('page_banner_background_image')) {
            $args['bg_image'] = get_field('page_banner_background_image')['sizes']['page_banner_bg_size'];
        } else {
            $args['bg_image'] = get_theme_file_uri('/images/ocean.jpg');
        }
    }
?>
	<div class="page-banner">
		<div class="page-banner__bg-image" style="background-image: url(<?php echo $args['bg_image']; ?>"></div>
		<div class="page-banner__content container container--narrow">
			<h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
			<div class="page-banner__intro">
				<p><?php echo $args['subtitle']; ?></p>
			</div>
		</div>
	</div>
<?php } ?>


<?php
/*
===========================================
    Theme resources
===========================================
 */
function fictional_university_files() {
	wp_enqueue_script('google_map_api', '//maps.googleapis.com/maps/api/js?key=AIzaSyCWqq_1fzA-lW5qJJWsUh8GfxcZewg4KMk', NULL, '1.0', true);
	wp_enqueue_script('main_university_js', get_theme_file_uri('/js/scripts-bundled.js'), NULL, '1.0', true);

	wp_enqueue_style('custom_google_fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
	wp_enqueue_style('font_awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
	wp_enqueue_style('fictional_university_main_styles', get_stylesheet_uri() );

	wp_localize_script('main_university_js', 'universityData', array(
        'root_url' => get_site_url(),
        'nonce' => wp_create_nonce('wp_rest'),
    ));
}
add_action('wp_enqueue_scripts', 'fictional_university_files');

/*
===========================================
    Theme support
===========================================
 */
function fictional_university_features() {
	register_nav_menu('header-menu-location', 'Header Primary Menu');
	register_nav_menu('footer-menu-location-1', 'Footer Location One');
	register_nav_menu('footer-menu-location-2', 'Footer Location Two');

	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_image_size('professor_portrait', 200, 220, true);
	add_image_size('page_banner_bg_size', 1500, 350, true);
}
add_action('after_setup_theme', 'fictional_university_features');

/*
===========================================
    Theme Queries
===========================================
 */
function fictional_university_adjust_queries($query) {
	if (!is_admin() and is_post_type_archive('campus') and $query->is_main_query()) {
		$query->set('posts_per_page', -1);
	}

    if (!is_admin() and is_post_type_archive('program') and $query->is_main_query()) {
		$query->set('orderby', 'title');
		$query->set('order', 'ASC');
		$query->set('posts_per_page', -1);
	}

	if (!is_admin() and is_post_type_archive('event') and $query->is_main_query()) {
		$currentDate = date('Y-m-d');
		$query->set('meta_key', 'event_date');
		$query->set('orderby', 'meta_value_num');
		$query->set('order', 'ASC');
		$query->set('meta_query',  array(
			array(
				'key' => 'event_date',
				'compare' => '>=',
				'value' => $currentDate,
				'type'  => 'string',
			),
		));
	}
}
add_action('pre_get_posts', 'fictional_university_adjust_queries');

/*
===========================================
    Theme REST API
===========================================
 */
function fictional_university_add_custom_rest_api_fields() {
	register_rest_field('post', 'author_name', array(
		'get_callback' => function() { return get_the_author(); },
	));

	register_rest_field('note', 'note_count', array(
		'get_callback' => function() { return count_user_posts(get_current_user_id(), 'note'); },
	));
}
add_action('rest_api_init', 'fictional_university_add_custom_rest_api_fields');

/*
===========================================
    Google Map API
===========================================
 */
function fictional_university_map_api_key($api) {
	$api['key'] = 'AIzaSyCWqq_1fzA-lW5qJJWsUh8GfxcZewg4KMk';
	return $api;
}
add_filter('acf/fields/google_map/api', 'fictional_university_map_api_key');

/*
===========================================
    Redirect subscriber account
===========================================
 */
function fictional_university_redirect_user_url() {
    $currentUser = wp_get_current_user();
    if (count($currentUser->roles) == 1 and $currentUser->roles[0] == 'subscriber') {
        wp_redirect(site_url('/'));
        exit;
    }
}
add_action('admin_init', 'fictional_university_redirect_user_url');

/*
===========================================
    Hide admin bar for subscriber
===========================================
 */
function fictional_university_remove_admin_bar() {
	$currentUser = wp_get_current_user();
	if (count($currentUser->roles) == 1 and $currentUser->roles[0] == 'subscriber') {
		show_admin_bar(false);
	}
}
add_action('wp_loaded', 'fictional_university_remove_admin_bar');

/*
===========================================
    Customize login screen
===========================================
 */
function fictional_university_site_url() {
	return esc_url(site_url('/'));
}
add_filter('login_headerurl', 'fictional_university_site_url');

// Add custom css
function fictional_university_login_css() {
	wp_enqueue_style('fictional_university_main_styles', get_stylesheet_uri() );
	wp_enqueue_style('custom_google_fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
}
add_action('login_enqueue_scripts', 'fictional_university_login_css');

// Change Login text
function fictional_university_login_title() {
	return get_bloginfo('name');
}
add_filter('login_headertext', 'fictional_university_login_title');

/*
===========================================
    Note Post Type - PRIVATE
===========================================
 */
function fictional_university_private_note($data, $postarr) {
    if ($data['post_type'] == 'note') {
        if (count_user_posts(get_current_user_id(), 'note') > 5 and !$postarr['ID']) {
            die("You have reached your note limit.");
        }

        $data['post_content'] = sanitize_textarea_field($data['post_content']);
        $data['post_title'] = sanitize_text_field($data['post_title']);
    }

    if ($data['post_type'] == 'note' and $data['post_status'] != 'trash') {
	    $data['post_status'] = 'private';
    }

    return $data;
}
add_filter('wp_insert_post_data', 'fictional_university_private_note', 10, 2);















