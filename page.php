<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>
    <?php pageBanner(); ?>

    <div class="container container--narrow page-section">
        <?php $theParentPageID = wp_get_post_parent_id(get_the_ID()); ?>
        <?php if ($theParentPageID) : ?>
            <div class="metabox metabox--position-up metabox--with-home-link">
                <p><a class="metabox__blog-home-link" href="<?php echo get_permalink($theParentPageID) ?>"><i class="fa fa-home" aria-hidden="true"></i> Back to <?php echo get_the_title($theParentPageID); ?></a> <span class="metabox__main"><?php the_title(); ?></span></p>
            </div>
        <?php endif; ?>

	    <?php
	        $thePageID = get_pages(array(
	        	'child_of'  => get_the_ID(),
	        ));
	    ?>
		<?php if ($theParentPageID or $thePageID) : ?>
	        <div class="page-links">
	            <h2 class="page-links__title"><a href="<?php echo get_permalink($theParentPageID); ?>"><?php echo get_the_title($theParentPageID) ?></a></h2>
	            <ul class="min-list">
	                <?php
	                    if ($theParentPageID) :
	                        $findChildrenOf = $theParentPageID;
	                    else :
	                        $findChildrenOf = get_the_ID();
	                    endif;

	                    wp_list_pages(array(
	                        'title_li'  => NULL,
	                        'child_of'  => $findChildrenOf,
		                    'sort_column' => 'menu_order'
	                    ));
	                ?>
	            </ul>
	        </div>
        <?php endif; ?>


        <div class="generic-content">
            <?php the_content(); ?>
        </div>

    </div>
<?php endwhile; ?>

<?php get_footer(); ?>
