<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="um-admin-metabox">

	<?php
	$role = $object['data'];

	UM()->admin_forms(
		array(
			'class'     => 'um-role-pvum um-half-column',
			'prefix_id' => 'role',
			'fields'    => array(
				array(
					'id'      => '_um_enable_profile_views',
					'type'    => 'checkbox',
					'default' => 0,
					'label'   => __( 'Enable profile views feature?', 'profile-views-um' ),
					'tooltip' => __( 'Can this role see list of profile viewers?', 'profile-views-um' ),
					'value'   => isset( $role['_um_enable_profile_views'] ) ? $role['_um_enable_profile_views'] : 0,
				)
			),
		)
	)->render_form();
	?>

	<div class="um-admin-clear"></div>
</div>