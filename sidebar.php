<div id="sidebar1" class="sidebar four columns" role="complementary">

	<div class="panel">
	
	<!-- Select the correct sidebar for this page type !-->
	<?php if( wp_is_mobile() ): ?>
		
		<?php if ( is_active_sidebar( 'sidebar_mobile' ) ) : ?>

			<?php dynamic_sidebar( 'sidebar_mobile' ); ?>
		
		<?php elseif( is_active_sidebar( 'sidebar_main' ) ) : ?>
			
			<?php dynamic_sidebar( 'sidebar_main' ); ?>
			
		<?php else : ?>

			<!-- This content shows up if there are no widgets defined in the backend. -->
			
			<div class="alert-box">Please activate some Widgets.</div>

		<?php endif; ?>
		
	<?php elseif( is_home() ): ?>
		
		<?php if ( is_active_sidebar( 'sidebar_blog_home' ) ) : ?>

			<?php dynamic_sidebar( 'sidebar_blog_home' ); ?>
			
		<?php elseif( is_active_sidebar( 'sidebar_blog' ) ) : ?>
			
			<?php dynamic_sidebar( 'sidebar_blog' ); ?>
			
		<?php elseif( is_active_sidebar( 'sidebar_main' ) ) : ?>
			
			<?php dynamic_sidebar( 'sidebar_main' ); ?>
			
		<?php else : ?>

			<!-- This content shows up if there are no widgets defined in the backend. -->
			
			<div class="alert-box">Please activate some Widgets.</div>

		<?php endif; ?>
		
	<?php elseif( is_single() ): ?>
		
		<?php if ( is_active_sidebar( 'sidebar_blog' ) ) : ?>

			<?php dynamic_sidebar( 'sidebar_blog' ); ?>
		
		<?php elseif( is_active_sidebar( 'sidebar_main' ) ) : ?>
			
			<?php dynamic_sidebar( 'sidebar_main' ); ?>
			
		<?php else : ?>

			<!-- This content shows up if there are no widgets defined in the backend. -->
			
			<div class="alert-box">Please activate some Widgets.</div>

		<?php endif; ?>
	
	<?php elseif( is_search() ): ?>
		
		<?php if ( is_active_sidebar( 'sidebar_search' ) ) : ?>

			<?php dynamic_sidebar( 'sidebar_search' ); ?>
		
		<?php elseif( is_active_sidebar( 'sidebar_main' ) ) : ?>
			
			<?php dynamic_sidebar( 'sidebar_main' ); ?>
			
		<?php else : ?>

			<!-- This content shows up if there are no widgets defined in the backend. -->
			
			<div class="alert-box">Please activate some Widgets.</div>

		<?php endif; ?>
		
	<?php else: ?>
		
		<?php if ( is_active_sidebar( 'sidebar_main' ) ) : ?>

			<?php dynamic_sidebar( 'sidebar_main' ); ?>

		<?php else : ?>

			<!-- This content shows up if there are no widgets defined in the backend. -->
			
			<div class="alert-box">Please activate some Widgets.</div>

		<?php endif; ?>

	<?php endif; ?>	

	</div>

</div>