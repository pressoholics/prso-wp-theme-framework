<div id="main" class="eight columns clearfix" role="main">

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
	<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
		
		<header>
			
			<h1 class="single-title" itemprop="headline"><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><?php echo get_the_title($post->post_parent); ?></a> &raquo; <?php the_title(); ?></h1>
			
			<p class="meta"><?php _e("Posted", "bonestheme"); ?> <time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php the_time('F jS, Y'); ?></time> <?php _e("by", "bonestheme"); ?> <?php the_author_posts_link(); ?>.</p>
		
		</header> <!-- end article header -->
	
		<section class="post_content clearfix" itemprop="articleBody">
			
			<!-- To display current image in the photo gallery -->
			<div class="attachment-img">
			      <a href="<?php echo wp_get_attachment_url($post->ID); ?>">
			      							      
			      <?php 
			      	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' ); 
			       
				      if ($image) : ?>
				          <img src="<?php echo $image[0]; ?>" alt="" />
				      <?php endif; ?>
			      
			      </a>
			</div>
			
			<!-- To display thumbnail of previous and next image in the photo gallery -->
			<ul class="block-grid two-up">
				<li class="next pull-left"><?php next_image_link() ?></li>
				<li class="previous pull-right"><?php previous_image_link() ?></li>
			</ul>
			
		</section> <!-- end article section -->
		
		<footer>

			<?php the_tags('<p class="tags"><span class="tags-title">Tags:</span> ', ' ', '</p>'); ?>
			
		</footer> <!-- end article footer -->
	
	</article> <!-- end article -->
	
	<?php comments_template(); ?>
	
	<?php endwhile; ?>			
	
	<?php else : ?>
	
	<article id="post-not-found">
	    <header>
	    	<h1>Not Found</h1>
	    </header>
	    <section class="post_content">
	    	<p>Sorry, but the requested resource was not found on this site.</p>
	    </section>
	    <footer>
	    </footer>
	</article>
	
	<?php endif; ?>

</div> <!-- end #main -->