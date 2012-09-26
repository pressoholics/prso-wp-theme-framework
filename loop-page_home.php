<div id="main" class="twelve columns" role="main">
					
	<article role="article">
		
		<header><?php do_action( 'prso_orbit_banner' ); ?></header>

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<section class="row post_content">
		
			<div class="home-main eight columns">
		
				<?php the_content(); ?>
				
			</div>
			
			<?php get_sidebar( 'home' ); //Get homepage dynamic sidebars ?>
									
		</section> <!-- end article header -->
		
		<footer>

			<p class="clearfix"><?php the_tags('<span class="tags">Tags: ', ', ', '</span>'); ?></p>
			
		</footer> <!-- end article footer -->
	
	</article> <!-- end article -->
	
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