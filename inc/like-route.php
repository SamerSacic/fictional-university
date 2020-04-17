<?php
/*
===========================================
    Register REST API route
===========================================
 */
function fictional_university_register_like() {
	register_rest_route('university/v1', 'like', array(
		'methods' => 'POST',
		'callback' => 'fictional_university_create_like',
	));

	register_rest_route('university/v1', 'like', array(
		'methods' => 'DELETE',
		'callback' => 'fictional_university_delete_like',
	));
}
add_action('rest_api_init', 'fictional_university_register_like');

/*
===========================================
    Methods
===========================================
 */
function fictional_university_create_like($data) {
	if (is_user_logged_in()) {
		$professor = sanitize_text_field($data['professor_id']);

		$existQuery = new WP_Query(array(
			'author' => get_current_user_id(),
			'post_type' => 'like',
			'meta_query' => array(
				array(
					'key' => 'liked_professor_id',
					'compare' => '=',
					'value' => $professor,
				)
			),
		));

		if ($existQuery->found_posts == 0 and get_post_type($professor) == 'professor') {
			return wp_insert_post(array(
				'post_type' => 'like',
				'post_status' => 'publish',
				'post_title' => 'Third PHP Post Test',
				'meta_input' => array(
					'liked_professor_id' => $professor,
				),
			));
		} else {
			die("Invalid professor ID.");
		}
	} else {
		die("Only logged in users can create a like.");
	}
}

function fictional_university_delete_like($data) {
	$likeId = sanitize_text_field($data['like']);

	if (get_current_user_id() == get_post_field('post_author', $likeId) and get_post_type($likeId) == 'like') {
		wp_delete_post($likeId, true);
		return 'Congrats, like deleted.';
	} else {
		die("You do not have permission to delete that.");
	}
}