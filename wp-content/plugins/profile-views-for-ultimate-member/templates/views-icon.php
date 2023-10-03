<?php $profile_id = um_profile_id(); ?>
<div class="um-profile-headericon profile-views-indicator">
	<a href="javascript:void(0);" class="um-profile-edit-a <?php if($new_views){ echo 'flashing'; } ?>" data-profile="<?php echo esc_attr( $profile_id ); ?>" id="profile-viewers-icon">
        <i class="um-icon-eye"></i>
        <span data-count="<?php echo esc_attr( $new_views ); ?>" id="counter">
            <?php echo esc_attr( $new_views ); ?>
        </span>
    </a>
</div>