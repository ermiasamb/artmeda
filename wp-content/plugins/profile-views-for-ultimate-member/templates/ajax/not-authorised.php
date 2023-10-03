<?php
$empty_message = __('You are not authorised to clear view list.','profile-views-um');
$message = apply_filters( 'unauthorised_viewers_clear_msg',$empty_message );
?>
<p class="pvum-empty-list"><?php echo esc_html( $message ); ?></p>
