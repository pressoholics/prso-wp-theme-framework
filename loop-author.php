<div id="main" class="eight columns clearfix" role="main">
				
	<h1 class="author_title h2">
		<span><?php _e("Posts By:", "bonestheme"); ?></span> 
		<!-- google+ rel=me function -->
		<?php $curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
		$google_profile = get_the_author_meta( 'google_profile', $curauth->ID );
		if ( $google_profile ) {
			echo '<a href="' . esc_url( $google_profile ) . '" rel="me">' . $curauth->display_name . '</a>'; ?></a>
		<?php } else { ?>
		<?php echo ucwords($curauth->display_name); ?>
		<?php } ?>
	</h1>
	
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
	<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">
		
		<header>
			
			<h3 class="h2"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
			
			<p class="meta"><?php _e("Posted", "bonestheme"); ?> <time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php the_time('F jS, Y'); ?></time> <?php _e("by", "bonestheme"); ?> <?php the_author_posts_link(); ?> <span class="amp">&</span> <?php _e("filed under", "bonestheme"); ?> <?php the_category(', '); ?>.</p>
		
		</header> <!-- end article header -->
	
		<section class="post_content">
		
			<?php the_post_thumbnail( 'bones-thumb-300' ); ?>
		
			<?php the_excerpt(); ?>
	
		</section> <!-- end article section -->
		
		<footer>
			
		</footer> <!-- end article footer -->
	
	</article> <!-- end article -->
	
	<?php endwhile; ?>	
	
	<?php do_action('prso_pagination'); ?>
				
	
	<?php else : ?>
	
	<article id="post-not-found">
	    <header>
	    	<h1><?php _e("No Posts Yet", "bonestheme"); ?></h1>
	    </header>
	    <section class="post_content">
	    	<p><?php _e("Sorry, What you were looking for is not here.", "bonestheme"); ?></p>
	    </section>
	    <footer>
	    </footer>
	</article>
	
	<?php endif; ?>

</div> <!-- end #main -->