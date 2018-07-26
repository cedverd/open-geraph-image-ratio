function create_new_og_image( $image_url ) {

	// Get Dimensions of given Image
	list($width, $height) 	= getimagesize($image_url);

	// Get New Dimensions According to the 'OPEN GRAPH' Recomended Ratio 
	$ratio  = (float)($width / $height);
	if( $ratio < 1.9000) {

		// Calculate New 'WIDTH' for Image (increase in the width)
		$ratio_diff 		= 1.9000 - $ratio;
		$ratio_per_pixel	= (float)( $ratio_diff / $ratio );
		$no_pixel_needed	= (float)($ratio_per_pixel  * $width );
		$final_width 		= (int)( $no_pixel_needed + $width );
		$final_height		= $height;
		$padding			= 'horizontal';
	} elseif ($ratio > 1.9100 ) {

		// Calculate New 'HEIGHT' for Image (increase in the height)
		$ratio_diff 		= $ratio - 1.9100;
		$ratio_per_pixel	= (float)( $ratio_diff / $ratio );
		$no_pixel_needed	= (float)( $ratio_per_pixel  * $height );
		$final_height 		= (int)( $no_pixel_needed + $height );
		$final_width		= $width;
		$padding			= 'vertial';
	} else {

		// The Image Already is in Required Ratio Copy the original Dimensions to New Dimensions
		$final_width 		= $width;
		$final_height		= $height;
		$padding			= 'no padding';
	}

	// Create New Image With new Dimensions
	$final_image 			= imagecreatetruecolor( $final_width, $final_height );

	// Create Transparent Color to make the Image Transparent
	$color 					= imagecolorallocatealpha( $final_image, 0, 0, 0, 127 );
	imagesavealpha($final_image, true);

	// fill background colour
	imagefill($final_image, 0, 0, $color ); 
	 
	// Determine offset coordinates so that new image is centered
	if( $padding == 'horizontal' ) {
		$width_diff			= $final_width - $width;
		$offest_x			= (int)( $width_diff / 2 );
		$offest_y			= 0;
	} elseif ( $padding == 'vertial' ) {
		$height_diff		= $final_height - $height;
		$offest_x			= 0;
		$offest_y			= (int)( $height_diff / 2 ); 
	} else {
		$offest_x			= 0;
		$offest_y			= 0;
	}

	// Get the Content of the Original Image
	$o_img = imagecreatefromstring( file_get_contents( $image_url ) );

	// Copy the original image into center of New Image;
	imagecopy( $final_image, $o_img, $offest_x, $offest_y, 0, 0, $width, $height );

	// Create New Image Name from the GIven Image Name
	$info 					= new SplFileInfo( $image_url );
	$ext 					=  $info->getExtension();
	$name 					= get_the_ID().'_og_image'. '.' . 'png';
	$upload_dir				= wp_upload_dir();

	// upload directory path name
	$path_name			    = $upload_dir['path'].'/'.$name;

	// New Image URL
	$url 					= $upload_dir['url'] .'/'.$name;

	// Save the Image to upload directory
	imagepng( $final_image, $path_name );

	// link the Image to the database
	update_post_meta( get_the_ID(), 'og_image', $url );

	return $url ;
}		
