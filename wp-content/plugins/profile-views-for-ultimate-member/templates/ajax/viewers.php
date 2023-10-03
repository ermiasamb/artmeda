<ul>
<?php foreach( $viewers as $viewer): 
    $time = date("jS M Y h:i A", $viewer['viewed_on']); 
    $location = $viewer['location'];
    $profile_id = absint($viewer['viewer']);

    if( $profile_id ){
        um_fetch_user($profile_id);
        $display_name = um_user('display_name');
        $profile_link = um_user_profile_url($profile_id);
        $avatar_url = um_get_user_avatar_url($profile_id, 32);

    }else{
        $profile_link = 'javascript:void(0)';
        $display_name = __('Anonymous','rofile-views-um');
        $avatar_url = um_get_default_avatar_uri();
    }

    $new_status = '';
    $status_class = 'viewed';
    if($viewer['status']=='new'){
        $status_class = 'new';
        if( absint($viewer['number']) > 1 ){
            $new_status = '<small><sub style="font-weight:100;font-size:11px;">('.esc_html( $viewer['number'] ).')</sub></small>';
        }
        $new_status .= '<sup>&bull;</sup>';
    }

    $allowed_tags = array(
        'sup' => array(
            'style' => array(),
            'class' => array()
        ),
        'sub' => array(
            'style' => array(),
            'class' => array()
        ),
        'strong' => array(
            'style' => array(),
            'class' => array()
        ),
        'small' => array(
            'style' => array(),
            'class' => array()
        )
    );
?>
<li class="viewer-list-item <?php echo esc_attr( $status_class ); ?>">
    <div class="viewer-id">

        <a class="image-profile-link" href="<?php echo esc_url( $profile_link ); ?>">
        <img class="pvum-profile-image" src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_html( $display_name ); ?>">
        </a>

        <div class="pvum-viewer-list-item-details">
            <strong class="pvum-display-name">
                <a href="<?php echo esc_url( $profile_link ); ?>"><?php echo esc_html( $display_name ).wp_kses( $new_status, $allowed_tags ); ?></a>
            </strong>
            <small class="location-time">
                <span class="time"><?php echo esc_html( $time ); ?></span> <?php _e( 'near','profile-views-um'); ?> 
                <span class="location"><?php echo esc_html( $location ); ?></span>
            </small>
        </div>
    </div>
</li>
<?php endforeach; ?>

</ul>