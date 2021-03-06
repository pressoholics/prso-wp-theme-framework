	</div> <!-- end #content -->
		
	<footer role="contentinfo">
			
					<div class="twelve columns">

						<div class="row">

							<nav class="ten columns clearfix">
								<?php
									if( class_exists('footer_links_walker') ) {
										wp_nav_menu(
									    	array(
									    		'menu' 				=> 'footer_links', /* menu name */
									    		'menu_class' 		=> 'link-list',
									    		'theme_location' 	=> 'footer_links', /* where in the theme it's assigned */
									    		'container_class' 	=> 'footer-links clearfix', /* container class */
									    		'walker' 			=> new footer_links_walker(),
									    		'fallback_cb'		=> false
									    	)
										);
									}
								?>
							</nav>

							<p class="attribution two columns"><a href="http://320press.com" id="credit320" title="By the dudes of 320press">320press</a></p>

						</div>

					</div>
					
			</footer> <!-- end footer -->
		
		</div> <!-- end #container -->
		
		<!--[if lt IE 7 ]>
  			<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
  			<script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
		<![endif]-->
		
		<?php wp_footer(); // js scripts are inserted using this function ?>

	</body>

</html>