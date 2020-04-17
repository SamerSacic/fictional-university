<?php
	if (!is_user_logged_in()) {
		wp_redirect(site_url('/'));
		exit;
	}
?>
<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>
	<?php pageBanner(); ?>

	<div class="container container--narrow page-section">
        <div class="create-note">
            <h2 class="headline headline--medium">Create New Note</h2>
            <input type="text" class="new-note-title" placeholder="Title">
            <textarea class="new-note-body" placeholder="Your note here..."></textarea>
            <span class="submit-note">Create Note</span>
            <span class="note-limit-message">Note limit reached: delete an existing one to make a room for a new one.</span>
        </div>
		<ul class="min-list link-list" id="my-notes">
            <?php
                $notes = new WP_Query(array(
                    'post_type' => 'note',
                    'posts_per_page' => -1,
                    'author' => get_current_user_id()
                ));
            ?>
            <?php while ($notes->have_posts()) : $notes->the_post(); ?>
                <li data-id="<?php the_ID(); ?>">
                    <input type="text" value="<?php echo str_replace('Private: ', '', esc_attr(get_the_title())); ?>" class="note-title-field" readonly>
                    <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</span>
                    <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</span>
                    <textarea class="note-body-field" readonly><?php echo esc_textarea(get_the_content()); ?></textarea>
                    <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i> Save</span>
                </li>
            <?php endwhile; ?>
        </ul>
	</div>
<?php endwhile; ?>

<?php get_footer(); ?>
