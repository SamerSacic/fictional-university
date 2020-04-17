<?php
/*
===========================================
    Register REST API route
===========================================
 */
function fictional_university_register_search() {
	register_rest_route('university/v1', 'search', array(
		'methods' => WP_REST_SERVER::READABLE,
		'callback' => 'fictional_university_search_results'
	));
}
add_action('rest_api_init', 'fictional_university_register_search');

/*
===========================================
    Methods
===========================================
 */
function fictional_university_search_results($data) {
	$mainQuery = new WP_Query(array(
		'post_type' => array('post', 'page', 'professor', 'program', 'campus', 'event'),
		's' => sanitize_text_field($data['term'])
	));

	$results = array(
		'general_info'  => array(),
		'professors'    => array(),
		'programs'      => array(),
		'events'        => array(),
		'campuses'      => array()
	);

	while ($mainQuery->have_posts()) {
		$mainQuery->the_post();

		if (get_post_type() == 'post' or get_post_type() == 'page') {
			array_push($results['general_info'], array(
				'title' => get_the_title(),
				'post_type' => get_post_type(),
				'author_name' => get_the_author(),
				'permalink' => get_the_permalink()
			));
		}

		if (get_post_type() == 'program') {
			$relatedCampuses = get_field('related_campuses');
			if ($relatedCampuses) {
				foreach ($relatedCampuses as $campus) {
					array_push($results['campuses'], array(
						'title' => get_the_title($campus),
						'permalink' => get_the_permalink($campus)
					));
				}
			}
			array_push($results['programs'], array(
				'id' => get_the_ID(),
				'title' => get_the_title(),
				'permalink' => get_the_permalink()
			));
		}

		if (get_post_type() == 'professor') {
			array_push($results['professors'], array(
				'title' => get_the_title(),
				'permalink' => get_the_permalink(),
				'image_url' => get_the_post_thumbnail_url(0, 'professor_portrait')
			));
		}

		if (get_post_type() == 'campus') {
			array_push($results['campuses'], array(
				'title' => get_the_title(),
				'permalink' => get_the_permalink()
			));
		}

		if (get_post_type() == 'event') {
			try {
				$eventDate = new DateTime( get_field( 'event_date' ) );
			} catch (Exception $e) {
				die($e->getMessage());
			}

			$excerpt = null;
			if (has_excerpt()) {
				$excerpt = get_the_excerpt();
			} else {
				$excerpt = wp_trim_words(get_the_content(), 16);
			}
			array_push($results['events'], array(
				'title' => get_the_title(),
				'month' => $eventDate->format('M'),
				'day' => $eventDate->format('d'),
				'excerpt' => $excerpt,
				'permalink' => get_the_permalink()
			));
		}
	}

	if ($results['programs']) {
		$programsMetaQuery = array('relation' => 'OR');
		foreach ($results['programs'] as $program) {
			array_push($programsMetaQuery, array(
				'key' => 'related_programs',
				'compare' => 'LIKE',
				'value' => '"' . $program['id'] . '"',
			));
		}
		$programRelationshipQuery = new WP_Query(array(
			'post_type' => array('professor', 'event'),
			'meta_query' => $programsMetaQuery,
		));

		while ($programRelationshipQuery->have_posts()) {
			$programRelationshipQuery->the_post();

			if (get_post_type() == 'professor') {
				array_push($results['professors'], array(
					'title' => get_the_title(),
					'permalink' => get_the_permalink(),
					'image_url' => get_the_post_thumbnail_url(0, 'professor_portrait')
				));
			}

			if (get_post_type() == 'event') {
				try {
					$eventDate = new DateTime( get_field( 'event_date' ) );
				} catch (Exception $e) {
					die($e->getMessage());
				}

				$excerpt = null;
				if (has_excerpt()) {
					$excerpt = get_the_excerpt();
				} else {
					$excerpt = wp_trim_words(get_the_content(), 16);
				}
				array_push($results['events'], array(
					'title' => get_the_title(),
					'month' => $eventDate->format('M'),
					'day' => $eventDate->format('d'),
					'excerpt' => $excerpt,
					'permalink' => get_the_permalink()
				));
			}
		}

		$results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
		$results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
	}
	
	return $results;
}
