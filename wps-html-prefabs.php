<?php

function return_wordpress_media_files($image_class = '', $image_id = '', $media_id = 0)
{
	if (intval($media_id) > 0) {
		// Change with the image size you want to use
		$image = wp_get_attachment_image($media_id, 'medium', false, array('id' => 'wps-' . $image_id));
	} else {
		// Some default image
		$image = '<img id="wps-' . $image_id . '" class="' . $image_class . '" src="/wordpress/wp-content/uploads/woocommerce-placeholder.png" />';
	}

	echo $image; ?>
	<input type="hidden" id=<?php echo 'wps_image_id_' . $image_id ?> value="<?php echo esc_attr($media_id); ?>" name="<?php echo 'id-' . $image_id ?>" class="regular-text" />
	<input type='button' class="button-primary" value="<?php esc_attr_e('Select a image', 'mytextdomain'); ?>" id="wps_media_manager" data-image-id="<?php echo $image_id ?>" />
<?php
}
