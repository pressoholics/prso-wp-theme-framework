<!-- Sidebar Content !-->
<div id="sidebar1" class="four columns clearfix" role="complementary">
				
	<?php if ( !empty($post->post_excerpt) ) { ?> 
	<div class="caption panel"><?php echo get_the_excerpt(); ?></div>
	<?php } ?>
				
	<!-- Using WordPress functions to retrieve the extracted EXIF information from database -->
	<div class="panel">
		<h3>Image metadata</h3>

		<p>
		   <?php
		      $imgmeta = wp_get_attachment_metadata( $id );
		
		// Convert the shutter speed retrieve from database to fraction
		      if($imgmeta['image_meta']['shutter_speed'] > 0){
			      if ((1 / $imgmeta['image_meta']['shutter_speed']) > 1)
			      {
			         if ((number_format((1 / $imgmeta['image_meta']['shutter_speed']), 1)) == 1.3
			         or number_format((1 / $imgmeta['image_meta']['shutter_speed']), 1) == 1.5
			         or number_format((1 / $imgmeta['image_meta']['shutter_speed']), 1) == 1.6
			         or number_format((1 / $imgmeta['image_meta']['shutter_speed']), 1) == 2.5){
			            $pshutter = "1/" . number_format((1 / $imgmeta['image_meta']['shutter_speed']), 1, '.', '') . " second";
			         }
			         else{
			           $pshutter = "1/" . number_format((1 / $imgmeta['image_meta']['shutter_speed']), 0, '.', '') . " second";
			         }
			      }
			      else{
			         $pshutter = $imgmeta['image_meta']['shutter_speed'] . " seconds";
			       }
			   }
			   else
			   		$pshutter = 'n/a';
	
		
		// Start to display EXIF and IPTC data of digital photograph
		       echo "Date Taken: " . date("d-M-Y H:i:s", $imgmeta['image_meta']['created_timestamp'])."<br />";
		       echo "Copyright: " . $imgmeta['image_meta']['copyright']."<br />";
		       echo "Credit: " . $imgmeta['image_meta']['credit']."<br />";
		       echo "Title: " . $imgmeta['image_meta']['title']."<br />";
		       echo "Caption: " . $imgmeta['image_meta']['caption']."<br />";
		       echo "Camera: " . $imgmeta['image_meta']['camera']."<br />";
		       echo "Focal Length: " . $imgmeta['image_meta']['focal_length']."mm<br />";
		       echo "Aperture: f/" . $imgmeta['image_meta']['aperture']."<br />";
		       echo "ISO: " . $imgmeta['image_meta']['iso']."<br />";
		       echo "Shutter Speed: " . $pshutter . "<br />"
		   ?>
		</p>
	</div>
	
</div>
<!-- /Sidebar Content !-->