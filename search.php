<?php get_header(); ?>
<?php
    pageBanner(array(
        'title' => 'Search Results',
        'subtitle' => 'You searched for &ldquo;' . esc_html(get_search_query(false)) . '&rdquo;'
    ));
?>
<div class="container container--narrow page-section">
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<?php get_template_part('template-parts/content', get_post_type()); ?>
		<?php endwhile; ?>
	<?php else : ?>
		<?php echo '<h2 class="headline headline--small-plus">No results match that search.</h2>'?>
	<?php endif; ?>

	<?php get_search_form(); ?>

	<?php echo paginate_links(); ?>
</div>
<?php get_footer(); ?>
