<?php get_header(); ?>
    <?php
        pageBanner(array(
            'title' => 'Past Events',
            'subtitle' => 'A recap of our past events.'
        ));
    ?>
	<div class="container container--narrow page-section">
		<?php
			$currentDate = date('Y-m-d');
			$pastEvents = new WP_Query(array(
				'paged' => get_query_var('paged', 1),
				'post_type' => 'event',
				'meta_key' => 'event_date',
				'orderby' => 'meta_value_num',
				'order' => 'ASC',
				'meta_query' => array(
					array(
						'key' => 'event_date',
						'compare' => '<',
						'value' => $currentDate,
						'type'  => 'string',
					),
				),
			));
		?>
		<?php while ($pastEvents->have_posts()) : $pastEvents->the_post(); ?>
			<?php get_template_part('template-parts/content', 'event'); ?>
		<?php wp_reset_postdata(); ?>
		<?php endwhile; ?>

		<?php
			echo paginate_links(array(
				'total' => $pastEvents->max_num_pages,
			));
		?>
	</div>
<?php get_footer(); ?>
