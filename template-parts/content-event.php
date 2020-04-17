<div class="event-summary">
	<a class="event-summary__date t-center" href="<?php the_permalink(); ?>">
		<?php
		try {
			$eventDate = new DateTime( get_field( 'event_date' ) );
		} catch (Exception $e) {
			die($e->getMessage());
		}
		?>
		<span class="event-summary__month"><?php echo $eventDate->format('M'); ?></span>
		<span class="event-summary__day"><?php echo $eventDate->format('d'); ?></span>
	</a>
	<div class="event-summary__content">
		<h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
		<p>
			<?php if (has_excerpt()) : ?>
				<?php echo get_the_excerpt(); ?>
			<?php else : ?>
				<?php echo wp_trim_words(get_the_content(), 18); ?>
			<?php endif; ?>
			<a href="<?php the_permalink(); ?>" class="nu gray">Learn more</a>
		</p>
	</div>
</div>