<?php
namespace UM_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	class UM_ELEMENTOR_LIST_MODULE extends Widget_Base {

		public function get_name() {
			return 'ele-um-user-list';
		}

		public function get_title() {
			return __( 'User Listings', 'um-elementor' );
		}

		public function get_icon() {
			return 'eicon-person';
		}

		public function get_categories() {
			return [ 'um-addons-elementor' ];
		}

		protected function register_controls() {
			$this->start_controls_section(
				'section_button',
				[
					'label' => __( 'Users Query', 'um-elementor' ),
				]
			);

			// Show members by role
			$this->add_control(
				'query_show_selected_roles',
				[
					'label' 		=> __( 'Select Users by Roles', 'um-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> false,
					'label_on' 		=> __( 'Show', 'um-elementor' ),
					'label_off' 	=> __( 'Hide', 'um-elementor' ),
					'return_value' 	=> 'yes',
				]
			);

			// User Roles - Select2
			$this->add_control(
				'query_user_roles',
				array(
					'label'       => __( 'Select Roles', 'um-elementor' ),
					'type'        => Controls_Manager::SELECT2,
					'default'     => '',
					'multiple'    => true,
					'label_block' => true,
					'options'     => user_elementor_um_get_user_roles(),
					'condition'   => array(
						'query_show_selected_roles' => 'yes',
					),
				)
			);

			// Number of Members to display
			$this->add_control(
			  'user_numbers',
			  [
			     'label'   => __( 'Max of Users to display', 'um-elementor' ),
			     'type'    => Controls_Manager::NUMBER,
			     'default' => 4,
			     'min'     => 1,
			     'max'     => 1000,
			     'step'    => 1,
			  ]
			);

			// Order Members by
			$this->add_control(
				'query_user_order_by',
				[
					'label' => esc_html__( 'Order by', 'um-elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'display_name',
					'options' => [
						'ID' 				=> esc_html__( 'ID', 'um-elementor' ),
						'display_name' 		=> esc_html__( 'Display Name', 'um-elementor' ),
						'user_name' 		=> esc_html__( 'User Name', 'um-elementor' ),
						'user_email' 		=> esc_html__( 'User Email', 'um-elementor' ),
						'user_registered' 	=> esc_html__( 'Registered', 'um-elementor' ),
						'post_count' 		=> esc_html__( 'Post Count', 'um-elementor' ),
						'last_login' 		=> esc_html__( 'Login', 'um-elementor' ),
						'first_name' 		=> esc_html__( 'First Name', 'um-elementor' ),
						'last_name' 		=> esc_html__( 'Last Name', 'um-elementor' ),
						'custom_field' 		=> esc_html__( 'Custom Field', 'um-elementor' ),
					],
				]
			);

	        // Meta Key
			$this->add_control(
				'user_query_by_meta_key',
	            [
	              'label' 		=> esc_html__( 'Meta Key', 'um-elementor' ),
	              'type' 		=> Controls_Manager::TEXT,
	              'label_block' => true,
	              'condition' 	=> [ 'query_user_order_by' => 'custom_field' ]
	            ]
			);

			// Members Order
			$this->add_control(
				'query_user_order',
				[
					'label' 	=> esc_html__( 'Order Direction', 'um-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'ASC',
					'options' 	=> [
						'ASC' 	=> esc_html__( 'Ascending', 'um-elementor' ),
						'DESC' 	=> esc_html__( 'Descending', 'um-elementor' ),
					],
				]
			);


			$this->end_controls_section();

			// User Title
			$this->start_controls_section(
				'user_title_section',
				[
					'label' => esc_html__( 'User Title', 'um-elementor' ),
				]
			);

				// Show User Name
				$this->add_control(
		            'um_elementor_show_member_name',
		            [
		                'label' 	=> esc_html__( 'Show User Title', 'um-elementor' ),
		                'type' 		=> Controls_Manager::CHOOSE,
		                'options' 	=> [
									1 => [
										'title' => esc_html__( 'Yes', 'um-elementor' ),
										'icon' => 'eicon-check',
									],
									0 => [
										'title' => esc_html__( 'No', 'um-elementor' ),
										'icon' => 'eicon-ban',
									]
						],
						'default' 	=> '1',
		            ]
		        );

				// User Name
				$this->add_control(
					'user_display_title',
					[
						'label' 	=> esc_html__( 'Users Name', 'um-elementor' ),
						'type' 		=> Controls_Manager::SELECT,
						'default' 	=> 'first_name',
						'options' 	=> [
							'first_name' 	=> esc_html__( 'First Name', 'um-elementor' ),
							'last_name' 	=> esc_html__( 'Last Name', 'um-elementor' ),
							'display_name' 	=> esc_html__( 'Display Name', 'um-elementor' ),
							'username' 		=> esc_html__( 'User Name', 'um-elementor' ),
						],
						'condition' => [ 'um_elementor_show_member_name' => '1' ]
					]
				);

			$this->end_controls_section();



			// User Image
			$this->start_controls_section(
				'user_image_section',
				[
					'label' => esc_html__( 'User Image', 'um-elementor' ),
				]
			);

				// Show User Image
				$this->add_control(
		            'um_elementor_show_user_image',
		            [
		                'label' 	=> esc_html__( 'Show User Image', 'um-elementor' ),
		                'type' 		=> Controls_Manager::CHOOSE,
		                'options' 	=> [
									1 => [
										'title' => esc_html__( 'Yes', 'um-elementor' ),
										'icon' => 'eicon-check',
									],
									0 => [
										'title' => esc_html__( 'No', 'um-elementor' ),
										'icon' => 'eicon-ban',
									]
						],
						'default' 	=> '1',
		            ]
		        );

				// Image Border Radius
				$this->add_control(
					'user_avatar_border_radius',
					[
						'label' 	=> esc_html__( 'Border Radius', 'um-elementor' ),
						'type' 		=> Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em' ],
						'default'    => [
							'top'    => '50',
							'bottom' => '50',
							'left'   => '50',
							'right'  => '50',
							'unit'   => '%',
						],						
						'selectors' => [
							'{{WRAPPER}} .um-elementor-member-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);


			$this->end_controls_section();

			// Ultimate Member Meta Fields
			$this->start_controls_section(
				'um_user_meta_section',
				[
					'label' => esc_html__( 'Ultimate Member Meta Fields', 'um-elementor' ),
				]
			);

				$repeater = new Repeater();

				// Meta Keys
				$repeater->add_control(
					'um_user_meta_field_value',
					[
						'label' 		=> esc_html__( 'Meta Key', 'um-elementor' ),
						'type' 			=> Controls_Manager::SELECT,
						'default' 		=> esc_html__( 'description', 'um-elementor' ),
						'label_block' 	=> true,
						'options'     	=> user_elementor_um_get_user_meta_keys_combine(),
					]
				);

				// Field Label
				$repeater->add_control(
					'um_user_meta_field_label',
					[
		                'label' 	=> __( 'Show Field Label', 'um-elementor' ),
		                'type' 		=> Controls_Manager::CHOOSE,
		                'options' 	=> [
									1 => [
										'title' => __( 'Yes', 'um-elementor' ),
										'icon' => 'eicon-check',
									],
									0 => [
										'title' => __( 'No', 'um-elementor' ),
										'icon' => 'eicon-ban',
									]
						],
						'default' 	=> '1',
					]
				);

				// User Meta
		  		$this->add_control(
					'um_user_meta_control',
					[
		                'label' 		=> esc_html__( 'User Fields', 'um-elementor' ),
						'type' 			=> Controls_Manager::REPEATER,
						'seperator' 	=> 'before',
						'fields' 		=> $repeater->get_controls(),
						'title_field' 	=> '{{{um_user_meta_field_value}}}',
					]
				);

			$this->end_controls_section();


			// Start Profile Layout Controls
	        $this->start_controls_section(
				'um_elementor_section_member_grid_layout',
				[
					'label' => __( 'Layout', 'um-elementor' ),
					'tab' 	=> Controls_Manager::TAB_STYLE
				]
			);

		        // Number of Columns
				$this->add_control(
					'grid_column_no',
					[
						'label' 	=> esc_html__( 'Columns', 'um-elementor' ),
						'type' 		=> Controls_Manager::SELECT,
						'default' 	=> '3',
						'options' 	=> [
							'1' 	=> esc_html__( '1', 'um-elementor' ),
							'2' 	=> esc_html__( '2', 'um-elementor' ),
							'3' 	=> esc_html__( '3', 'um-elementor' ),
							'4' 	=> esc_html__( '4', 'um-elementor' ),
							'5' 	=> esc_html__( '5', 'um-elementor' ),
							'6' 	=> esc_html__( '6', 'um-elementor' ),
							'7' 	=> esc_html__( '7', 'um-elementor' ),
							'8' 	=> esc_html__( '8', 'um-elementor' ),	
							'9' 	=> esc_html__( '9', 'um-elementor' ),
							'10' 	=> esc_html__( '10', 'um-elementor' ),
							'11' 	=> esc_html__( '11', 'um-elementor' ),
							'12' 	=> esc_html__( '12', 'um-elementor' ),														
						],
					]
				);

				// User Card Layout
				$this->add_control(
					'user_layout',
					[
						'label' 	=> esc_html__( 'Layouts', 'um-elementor' ),
						'type' 		=> Controls_Manager::SELECT,
						'default' 	=> 1,
						'options' 	=> [
							1 	=> esc_html__( 'Stylish Cards', 'um-elementor' ),
							2 	=> esc_html__( 'Slim Boxes', 'um-elementor' ),
						],
					]
				);

			$this->end_controls_section();

			// Start Style Controls
	        $this->start_controls_section(
	            'um_elementor_section_member_style',
	            [
	                'label' => __( 'User Card', 'um-elementor' ),
	                'tab' 	=> Controls_Manager::TAB_STYLE
	            ]
	        );

		        // Text Alignment
				$this->add_control(
		            'title_alignment',
		            [
		                'label' 	=> __( 'Text Alignment', 'um-elementor' ),
		                'type' 		=> Controls_Manager::CHOOSE,
		                'options' 	=> [
								'text-left' => [
									'title' => __( 'Left', 'um-elementor' ),
									'icon' 	=> 'eicon-text-align-left',
								],
								'text-center' => [
									'title' => __( 'Center', 'um-elementor' ),
									'icon' 	=> 'eicon-text-align-center',
								],
								'text-right' => [
									'title' => __( 'Right', 'um-elementor' ),
									'icon' 	=> 'eicon-text-align-right',
								]
						],
						'default' 	=> 'text-center'
		            ]
		        );

		        // Member Blocks Color
		        $this->add_control(
					'um_member_block_color',
					[
						'label' 	=> __( 'Member Blocks Color', 'um-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default' 	=> '#fff',
						'selectors' => [
							'{{WRAPPER}} .um-widget-member-holder' => 'background-color: {{VALUE}}',
						]
					]
				);

				// Margin Between Users
				$this->add_responsive_control(
					'um_member_block_margin',
					[
						'label' 		=> esc_html__( 'Margin Between Users', 'um-elementor' ),
						'type' 			=> Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em' ],
						'default'    => [
							'top'    => '6',
							'bottom' => '6',
							'left'   => '6',
							'right'  => '6',
							'unit'   => 'px',
						],						
						'selectors' 	=> [
							'{{WRAPPER}} .um-item-user' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				// Padding Between Users
				$this->add_responsive_control(
					'um_member_block_padding',
					[
						'label' 		=> esc_html__( 'Padding Between Users', 'um-elementor' ),
						'type' 			=> Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em' ],
						'default'    => [
							'top'    => '6',
							'bottom' => '6',
							'left'   => '6',
							'right'  => '6',
							'unit'   => 'px',
						],						
						'selectors' 	=> [
							'{{WRAPPER}} .um-widget-member-holder' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);			

				// Border Radius of User Block
				$this->add_control(
					'um_member_block_border_radius',
					[
						'label' 	=> esc_html__( 'Border Radius', 'um-elementor' ),
						'type' 		=> Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em' ],
						'selectors' => [
								'{{WRAPPER}} .um-widget-member-holder' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],					
					]
				);

				// Allow Box Shadow
				$this->add_control(
					'allow_box_shadow',
					[
						'label' 		=> _x( 'Box Shadow', 'Box Shadow Control', 'um-elementor' ),
						'type' 			=> Controls_Manager::SWITCHER,
						'label_on' 		=> esc_html__( 'Yes', 'um-elementor' ),
						'label_off' 	=> esc_html__( 'No', 'um-elementor' ),
						'return_value' 	=> 'yes',
						'separator' 	=> 'before',
						'render_type' 	=> 'ui',
					]
				);



				// Box Shadow of User Block
				$this->add_control(
					'um_user_block_box_shadow',
					[
						'label' 	=> esc_html__( 'Box Shadow', 'um-elementor' ),
						'type' 		=> Controls_Manager::BOX_SHADOW,
						'condition' => array(
							'allow_box_shadow!' => '',
						),
						'size_units' => [ 'px', '%', 'em' ],
						'selectors' => [
								'{{WRAPPER}} .um-widget-member-holder' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};',
						],					
					]
				);				

			$this->end_controls_section();

			// Start Typography Controls
	        $this->start_controls_section(
	            'um_ele_user_style_title_section',
	            [
	                'label' => __( 'Title', 'um-elementor' ),
	                'tab' 	=> Controls_Manager::TAB_STYLE
	            ]
	        );

		        // Member Name Color
		        $this->add_control(
					'um_member_block_title_color',
					[
						'label' 	=> __( 'Title Color', 'um-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '#303133',
						'selectors' => [
							'{{WRAPPER}} .um-elementor-member-name, {{WRAPPER}} .um-elementor-member-name a' => 'color: {{VALUE}};',
						]
					]
				);

		        // Member Name Hover Color
		        $this->add_control(
					'um_member_block_title_hover_color',
					[
						'label' 	=> __( 'Title Hover Color', 'um-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '#23527c',
						'selectors' => [
							'{{WRAPPER}} .um-elementor-member-name:hover, {{WRAPPER}} .um-elementor-member-name a:hover' => 'color: {{VALUE}};',
						]

					]
				);

		        // User Name Typography
		        $this->add_group_control(
		            Group_Control_Typography::get_type(),
		            [
		                'name' 		=> 'um_member_user_name_typography',
		                'label' 	=> esc_html__( 'Title Font', 'um-elementor' ),
		                'selector' 	=> '{{WRAPPER}} .um-elementor-member-name a',
		                'global'    => ['default' => Global_Typography::TYPOGRAPHY_PRIMARY,],
		            ]
		        );

			$this->end_controls_tabs();
			$this->end_controls_section();


			// Member Block Typography
	        $this->start_controls_section(
	            'um_elementor_section_member_typography',
	            [
	                'label' => __( 'Meta Fields', 'um-elementor' ),
	                'tab' 	=> Controls_Manager::TAB_STYLE
	            ]
	        );

		        // Member Meta Label Color
		        $this->add_control(
					'um_member_meta_field_title_color',
					[
						'label' 	=> __( 'Meta Label', 'um-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '#444444',
						'selectors' => [
							'{{WRAPPER}} .um-custom-label' => 'color: {{VALUE}};',
						],

					]
				);

		        // Member Meta Value Color
		        $this->add_control(
					'um_member_meta_field_value_color',
					[
						'label' 	=> __( 'Meta Value', 'um-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '#444444',
						'selectors' => [
							'{{WRAPPER}} .um-custom-field' => 'color: {{VALUE}};',
						],

					]
				);

		        // Member Meta Value Overlay Color
		        $this->add_control(
					'um_member_meta_field_value_overlay_color',
					[
						'label' 	=> __( 'Member Meta Value Overlay', 'um-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> 'rgba(63, 81, 181, 0)',
						'selectors' => [
							'{{WRAPPER}} .um-elementor-member-position, {{WRAPPER}} .um-hover-content' => 'background-color: {{VALUE}};',
						],
					]
				);

				// Other Text Typography
		        $this->add_group_control(
		            Group_Control_Typography::get_type(),
		            [
		                'name' => 'um_member_other_text_typography',
		                'label' => esc_html__( 'Other Text Font', 'um-elementor' ),
		                'selector' 	=> '{{WRAPPER}} .um-item-user',
		                'global'    => ['default' => Global_Typography::TYPOGRAPHY_TEXT,],
		            ]
		        );

		        // Meta Label Typography
		        $this->add_group_control(
		            Group_Control_Typography::get_type(),
		            [
		                'name' => 'um_member_meta_field_title_typography',
		                'label' => esc_html__( 'Meta Label Font', 'um-elementor' ),
		                'selector' 	=> '{{WRAPPER}} .um-custom-label',
		                'global'    => ['default' => Global_Typography::TYPOGRAPHY_TEXT,],
		            ]
		        );

				// Meta Value Typography
		        $this->add_group_control(
		            Group_Control_Typography::get_type(),
		            [
		                'name' => 'um_member_meta_field_field_typography',
		                'label' => esc_html__( 'Meta Value Font', 'um-elementor' ),
		                'selector' 	=> '{{WRAPPER}} .um-custom-field',
		                'global'    => ['default' => Global_Typography::TYPOGRAPHY_TEXT,],
		            ]
		        );

			$this->end_controls_section();


			// Member Block Hover
	        $this->start_controls_section(
	            'um_elementor_section_member_animation_entry',
	            [
	                'label' => __( 'Block Animation', 'um-elementor' ),
	                'tab' 	=> Controls_Manager::TAB_STYLE
	            ]
	        );

				// Member Blocks Hover Animation
				$this->add_control(
					'um_member_block_hover_animation',
					[
							'label' => esc_html__( 'Hover Animation', 'um-elementor' ),
							'type' => Controls_Manager::HOVER_ANIMATION
					]
				);

			$this->end_controls_section();
		}


		protected function render_um_user_meta( $settings ) {
		    if ( empty( $settings['um_user_meta_control'] ) ) {
		        return;
		    }

		    $user_id = um_user('ID'); // Get the user ID outside the loop
		    um_fetch_user( $user_id );

		    foreach ( $settings['um_user_meta_control'] as $item ) {
		        $meta_value = um_user( $item['um_user_meta_field_value'] );

		        if ( ! empty( $meta_value ) ) {
		            ?>
		            <span class="um-meta-field">
		                <?php if ( $item['um_user_meta_field_label'] ) : ?>
		                    <span class="um-custom-label"><?php echo esc_attr( UM()->Fields()->get_label( $item['um_user_meta_field_value'] ) ); ?>:</span>
		                <?php endif; ?>
		                <span class="um-custom-field">
		                    <?php
		                    if ( is_array( $meta_value ) ) {
		                        echo esc_attr( implode( ', ', $meta_value ) ); // Use implode to join array elements
		                    } else {
		                        echo esc_attr( $meta_value );
		                    }
		                    ?>
		                </span>
		            </span>
		            <?php
		        }
		    }
		}



		// Output of the Elementor Widget
		protected function render() {

			$settings 			= $this->get_settings();

			$member_name_type 	= isset( $settings['user_display_title'] ) ? $settings['user_display_title'] : 'first_name';
			$number_of_member 	= isset( $settings['user_numbers'] ) ? absint( $settings['user_numbers'] ) : 4;
			$member_order_by 	= isset( $settings['query_user_order_by'] ) ? $settings['query_user_order_by'] : 'display_name';
			$member_order 		= isset( $settings['query_user_order'] ) ? $settings['query_user_order'] : 'ASC';
			$member_role_multi 	= ! empty( $settings['query_user_roles'] ) ? $settings['query_user_roles'] : false;
			$selected_roles 	= ! empty( $settings['query_show_selected_roles'] ) ? $settings['query_show_selected_roles'] : false;
			
			$column 			= isset( $settings['grid_column_no'] ) ? $settings['grid_column_no'] : '3';
			$align 				= isset( $settings['title_alignment'] ) ? $settings['title_alignment'] : 'text-left';
			$layout 			= isset( $settings['user_layout'] ) ? absint( $settings['user_layout'] ) : 1;
			$hover_animate 		= $this->get_settings( 'um_member_block_hover_animation' );

			$args = array(
		    		'fields'      	=> 'ID',
		    		'count_total' 	=> false, // Disable SQL_CALC_FOUND_ROWS.
					'number'      	=> (int) $number_of_member,
					'orderby'     	=> $member_order_by,
					'order'       	=> $member_order,
			);

			if( $selected_roles == 'yes' ) {
				$args['role__in'] = $member_role_multi;
			}

			if( $member_order_by == 'custom_field' ) {
				$args['meta_key'] 	= $settings['user_query_by_meta_key'];
				$args['orderby'] 	= 'meta_value';
			}

			$members_query = new \WP_User_Query( $args );
			$users = (array) $members_query->get_results();


		if ( ! empty( $users ) ) :?>

			<div class="um-team-carousel-wrapper grid grid-cols-1 md:grid-cols-<?php echo esc_attr($column);?>">
				<?php
			  		foreach( $users as $user_id ) :
			    	um_fetch_user( $user_id );
			    	$user 			= get_user_by( "id", $user_id );
			    	?>

					<?php if ( $layout == 1 ) : ?>
						<div class="um-item-user elementor-animation-<?php echo esc_attr($hover_animate);?>">
						<div class="um-widget-member-holder">
							    		<?php if( $settings['um_elementor_show_user_image'] == 1 ) : ?>
								    		<div class="um-elementor-member-image <?php echo esc_attr($align);?>">
								    			<a title="<?php echo um_user('display_name')?>" href="<?php echo esc_url(um_user_profile_url());?>">
								    				<?php echo um_get_avatar( '', $user_id, 100 )?>
								    			</a>
								    		</div>
							    		<?php endif;?>

										<div class="um-elementor-member-position">
								    		<?php if ( $settings['um_elementor_show_member_name'] == 1 ) : ?>
									    		<div class="um-elementor-member-name <?php echo esc_attr($align);?>">
													<p>
														<a title="<?php echo esc_attr(um_user( 'first_name' ));?>" href="<?php echo esc_url(um_user_profile_url());?>">
															<?php echo um_user( "$member_name_type" );?>
														</a>
													</p>
									    		</div>
								    		<?php endif;?>
								    		<div class="um-custom-field-container <?php echo esc_attr($align);?>">
												<?php $this->render_um_user_meta( $settings );?>
								    		</div>

										</div>
							    </div>
							    </div>
							<?php endif;?>

					<?php if( $layout == 2 ) : ?>

					    	<div class="um-item-user elementor-animation-<?php echo esc_attr($hover_animate);?>">
					    	<div class="um-widget-member-holder">
						    	<div class="user-layout-spaced grid">
								    <div class="user-layout-spaced-one um-elementor-member-image <?php echo esc_attr($align);?>">
								    	<a title="<?php echo um_user('display_name')?>" href="<?php echo um_user_profile_url()?>">
								    		<?php echo um_get_avatar( '', $user_id, 100 )?>
								    	</a>
								    </div>

						    		<div class="user-layout-spaced-two um-elementor-member-info <?php echo esc_attr($align);?>">
						    			<div class="um-elementor-member-name <?php echo esc_attr($align);?>">
											<p>
												<a title="<?php echo esc_attr(um_user( 'first_name' ));?>" href="<?php echo esc_url(um_user_profile_url())?>">
													<?php echo um_user( "$member_name_type" );?>
												</a>
											</p>
										</div>
										<?php $this->render_um_user_meta( $settings );?>
									</div>
		
						    	</div>
						    </div>
						    </div>
					<?php endif;?>

			    <?php endforeach; ?>

				</div>

			<?php um_reset_user();
		else:
			esc_html_e( 'Not Found', 'um-elementor' );
		endif;
	}

		protected function content_template() {}
	}

	add_action( 'elementor/widgets/register', function ( $widgets_manager ) {
		$widgets_manager->register( new UM_ELEMENTOR_LIST_MODULE() );
	} );