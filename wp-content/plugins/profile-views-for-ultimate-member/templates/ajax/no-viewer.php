<?php
$empty_message = __('No one has viewed your profile lately.','profile-views-um');
$message = apply_filters( 'viewers_empty_list_msg',$empty_message );
?>
<p class="pvum-empty-list"><?php echo esc_html( $message ); ?></p>
