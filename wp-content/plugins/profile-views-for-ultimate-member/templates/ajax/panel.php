<?php $profile_id = um_profile_id(); ?>
<div id="profile-view-list" data-profile="<?php echo esc_attr( $profile_id ); ?>">
	<div class="profile-view-header">
		<button type="button" class="pvum-panel-close">&times;</button> 
		<strong class="panel-title"><?php echo __( 'Profile Views','profile-views-um' ); ?></strong> 
		<a data-id="pvum-clear-all-views" class="clear-all-link" data-profile="<?php echo esc_attr( $profile_id ); ?>" data-count="" href="#"><?php echo __('Clear All','profile-views-um'); ?></a>
	</div>
	<div class="profile-view-body"></div>
</div>