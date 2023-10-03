<?php
namespace UM_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	class UM_ELEMENTOR_LIST_FLIP_MODULE extends Widget_Base {

		public function get_name() {
			return 'ele-um-user-list-flip';
		}

		public function get_title() {
			return __( 'User Listings - Flip Box', 'um-elementor' );
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



		// Section: Front Section
		$this->start_controls_section(
			'um_flip_section_front',
			[
				'label' => esc_html__( 'Front', 'wpr-addons' ),
			]
		);

				$repeater = new Repeater();

				// Meta Keys
				$repeater->add_control(
					'um_user_meta_field_value_front',
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
					'um_user_meta_field_label_front',
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
					'um_user_meta_control_front',
					[
		                'label' 		=> esc_html__( 'User Fields', 'um-elementor' ),
						'type' 			=> Controls_Manager::REPEATER,
						'seperator' 	=> 'before',
						'fields' 		=> $repeater->get_controls(),
						'title_field' 	=> '{{{um_user_meta_field_value_front}}}',
						'default' => [
							[ 'um_user_meta_field_value_front' => 'birth_date' ],
							[ 'um_user_meta_field_value_front' => 'description' ],
						],						
					]
				);




		$this->end_controls_section();

		// Section: Back Section
		$this->start_controls_section(
			'um_flip_section_back',
			[
				'label' => esc_html__( 'Back', 'wpr-addons' ),
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
						'default' => [
							[ 'um_user_meta_field_value' => 'birth_date' ],
							[ 'um_user_meta_field_value' => 'description' ],
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
						'default' 	=> 'display_name',
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

				$this->add_group_control(
					Group_Control_Css_Filter::get_type(),
					[
						'name' => 'um_user_avatar_css_filter',
						'selector' => '{{WRAPPER}} .um-elementor-member-image img',
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

				$this->add_responsive_control(
					'flip_box_height',
					[
						'type' => Controls_Manager::SLIDER,
						'label' => esc_html__( 'Height', 'um-elementor' ),
						'size_units' => [ 'px', 'vh' ],
						'range' => [
							'px' => [
								'min' => 20,
								'max' => 1000,
							],
							'vh' => [
								'min' => 20,
								'max' => 100,
							],
						],
						'default' => [
							'unit' => 'px',
							'size' => 350,
						],
						'selectors' => [
							'{{WRAPPER}} .flip-card' => 'height: {{SIZE}}{{UNIT}};',
						],
					]
				);

				// User Card Layout
				$this->add_control(
					'user_layout',
					[
						'label' 	=> esc_html__( 'Layouts', 'um-elementor' ),
						'type' 		=> Controls_Manager::SELECT,
						'default' 	=> 'flip-left',
						'options' 	=> [
							'flip-left' 	=> esc_html__( 'Flip Left', 'um-elementor' ),
							'flip-right' 	=> esc_html__( 'Flip Right', 'um-elementor' ),
							'flip-top' 	=> esc_html__( 'Flip Top', 'um-elementor' ),
							'flip-bottom' 	=> esc_html__( 'Flip Bottom', 'um-elementor' ),
						],
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
							'top'    => '12',
							'bottom' => '12',
							'left'   => '12',
							'right'  => '12',
							'unit'   => 'px',
						],						
						'selectors' 	=> [
							'{{WRAPPER}} .flip-card-front, {{WRAPPER}} .flip-card-back' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
								'{{WRAPPER}} .flip-card-inner, {{WRAPPER}} .flip-card-front, {{WRAPPER}} .flip-card-back' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
								'{{WRAPPER}} .flip-card' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};',
						],					
					]
				);	



			$this->end_controls_section();

			// Start Style Controls
	        $this->start_controls_section(
	            'um_elementor_section_member_style',
	            [
	                'label' => __( 'Front', 'um-elementor' ),
	                'tab' 	=> Controls_Manager::TAB_STYLE
	            ]
	        );

		        // Text Alignment
				$this->add_control(
		            'title_alignment_front',
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
					'um_member_block_color_front',
					[
						'label' 	=> __( 'Member Blocks Color', 'um-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default' 	=> '#f5f4ed',
						'selectors' => [
							'{{WRAPPER}} .flip-card-front' => 'background-color: {{VALUE}}',
						]
					]
				);

		        // Member Meta Label Color
		        $this->add_control(
					'um_member_meta_field_title_color_front',
					[
						'label' 	=> __( 'Meta Label', 'um-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '#444444',
						'selectors' => [
							'{{WRAPPER}} .flip-card-front .um-custom-label' => 'color: {{VALUE}};',
						],

					]
				);

		        // Member Meta Value Color
		        $this->add_control(
					'um_member_meta_field_value_color_front',
					[
						'label' 	=> __( 'Meta Value', 'um-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '#5c534f',
						'selectors' => [
							'{{WRAPPER}} .flip-card-front .um-custom-field' => 'color: {{VALUE}};',
						],

					]
				);

		        // Meta Label Typography
		        $this->add_group_control(
		            Group_Control_Typography::get_type(),
		            [
		                'name' => 'um_member_meta_field_title_typography_front',
		                'label' => esc_html__( 'Typography', 'um-elementor' ),
		                'selector' 	=> '{{WRAPPER}} .flip-card-front .um-custom-label, {{WRAPPER}} .flip-card-front .um-custom-field',
		                'global'    => ['default' => Global_Typography::TYPOGRAPHY_TEXT,],
				        'fields_options' => [
				            'typography' => ['default' => 'yes'],
				            'font_weight' => ['default' => 500],
				        ],			                
		            ]
		        );


			$this->end_controls_section();


			// Start Style Controls
	        $this->start_controls_section(
	            'um_elementor_section_member_style_back',
	            [
	                'label' => __( 'Back', 'um-elementor' ),
	                'tab' 	=> Controls_Manager::TAB_STYLE
	            ]
	        );

		        // Text Alignment
				$this->add_control(
		            'title_alignment_back',
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
					'um_member_block_color_back',
					[
						'label' 	=> __( 'Member Blocks Color', 'um-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default' 	=> '#652f9c',
						'selectors' => [
							'{{WRAPPER}} .flip-card-back' => 'background-color: {{VALUE}}',
						]
					]
				);

		        // Member Meta Label Color
		        $this->add_control(
					'um_member_meta_field_title_color_back',
					[
						'label' 	=> __( 'Meta Label', 'um-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '#fff',
						'selectors' => [
							'{{WRAPPER}} .flip-card-back .um-custom-label' => 'color: {{VALUE}};',
						],

					]
				);

		        // Member Meta Value Color
		        $this->add_control(
					'um_member_meta_field_value_color_back',
					[
						'label' 	=> __( 'Meta Value', 'um-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '#fff',
						'selectors' => [
							'{{WRAPPER}} .flip-card-back .um-custom-field' => 'color: {{VALUE}};',
						],

					]
				);

		        // Meta Label Typography
		        $this->add_group_control(
		            Group_Control_Typography::get_type(),
		            [
		                'name' => 'um_member_meta_field_title_typography_back',
		                'label' => esc_html__( 'Typography', 'um-elementor' ),
		                'selector' 	=> '{{WRAPPER}} .flip-card-back .um-custom-label, {{WRAPPER}} .flip-card-back .um-custom-field',
		                'global'    => ['default' => Global_Typography::TYPOGRAPHY_TEXT,],
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

		        // User Name Typography
		        $this->add_group_control(
		            Group_Control_Typography::get_type(),
		            [
		                'name' 		=> 'um_member_user_name_typography',
		                'label' 	=> esc_html__( 'Title Font', 'um-elementor' ),
		                'selector' 	=> '{{WRAPPER}} .um-elementor-member-name a',
		                'global'    => ['default' => Global_Typography::TYPOGRAPHY_PRIMARY,],
				        'fields_options' => [
				            'typography' => ['default' => 'yes'],
				            'font_weight' => ['default' => 700],
				            'font_family' => ['default' => 'Domine'],
							'font_size' => [
								'default' => [
									'size' => '2',
									'unit' => 'rem',
								],
								'size_units' => [ 'rem' ],
							],
				        ],		                
		            ]
		        );

			$this->end_controls_tabs();
			$this->end_controls_section();
		}


		protected function render_um_user_meta( $settings ) {

			if( empty($settings['um_user_meta_control']) ) return;

				foreach( $settings['um_user_meta_control'] as $item ) :
						$user_id = um_user('ID');
						um_fetch_user( $user_id );
						$meta_value = um_user($item['um_user_meta_field_value']);


						//$meta_value = get_user_meta( um_user( 'ID' ), $item['um_user_meta_field_value'], true );

					if ( !empty( $meta_value ) ) : ?>
						<span class="um-meta-field">

							<?php if($item['um_user_meta_field_label']) :?>
								<span class="um-custom-label"><?php echo esc_attr(UM()->Fields()->get_label($item['um_user_meta_field_value']));?>:</span>
							<?php endif;?>

							<span class="um-custom-field">
							<?php
							// URL
							if( UM()->Fields()->get_field_type($item['um_user_meta_field_value']) == "url"){

								// Get the filename
								$link_url = esc_url(um_user( $item['um_user_meta_field_value'] ));

								echo "<a href='$link_url'>$link_url</a>";
								echo "<i class='um-faicon-link'></i>";
							}

							// Phone Number
							if( UM()->Fields()->get_field_type($item['um_user_meta_field_value']) == "tel"){

								// Get the Phone Number
								$link_tel = um_user( $item['um_user_meta_field_value'] );
								
								echo "<a href='tel:$link_tel'>$link_tel</a>";
								echo "<i class='um-faicon-phone'></i>";
							}

							// Image URL
							if( UM()->Fields()->get_field_type($item['um_user_meta_field_value']) == "image"){

								// Get the filename
								$image_filename = um_user( $item['um_user_meta_field_value'] );
										
								// Check if the file is there
								if(!empty($image_filename)){
									// Get the WordPress upload directory URL
									$upload_dir = wp_upload_dir();

									// Look in to Ultimate Member foler and construct the image URL
									$image_url = esc_url( $upload_dir['baseurl'] . '/ultimatemember/' . um_user( 'ID' ) . '/' . $image_filename );

									// Output the image
									echo "<img src='$image_url'>";
								}
							}

							// File URL
							if( UM()->Fields()->get_field_type($item['um_user_meta_field_value']) == "file"){

								// Get the filename
								$image_filename = um_user( $item['um_user_meta_field_value'] );
										
								// Check if the file is there
								if(!empty($image_filename)){
									// Get the WordPress upload directory URL
									$upload_dir = wp_upload_dir();

									// Look in to Ultimate Member foler and construct the image URL
									$image_url = esc_url( $upload_dir['baseurl'] . '/ultimatemember/' . um_user( 'ID' ) . '/' . $image_filename );

									// Output the File
									$file_info = um_user( $item['um_user_meta_field_value'] . "_metadata" );
									$file_name = $file_info['original_name'];

									echo "<a href='$image_url' target='_blank'>{$file_name}</a>";
									echo "<i class='um-faicon-file-text-o'></i>";

								}
							}


							// youtube_video
							if( UM()->Fields()->get_field_type($item['um_user_meta_field_value']) == "youtube_video"){
								// Get the YouTube URL
								$yt_url = esc_url(um_user( $item['um_user_meta_field_value'] ));	
								$code = substr($yt_url, strpos($yt_url,'watch?v=') + 8);?>
								
								<iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo $code;?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
							<?php
							}
							?>
								<?php 
								if( 
									UM()->Fields()->get_field_type($item['um_user_meta_field_value']) != "image"
									AND UM()->Fields()->get_field_type($item['um_user_meta_field_value']) != "youtube_video"
									AND UM()->Fields()->get_field_type($item['um_user_meta_field_value']) != "file"
									AND UM()->Fields()->get_field_type($item['um_user_meta_field_value']) != "url"
									AND UM()->Fields()->get_field_type($item['um_user_meta_field_value']) != "tel"
								){
									if(is_array($meta_value)){
										$meta_value_copy = $meta_value;
										foreach($meta_value as $individual){
											echo esc_attr($individual);
										    if (next($meta_value_copy )) {
	        									echo ', '; // Add comma for all elements instead of last
	    									}	
										}
									}else{
										echo esc_attr($meta_value);
									}
								}
								
								?>
							</span>
						</span>
					<?php endif;
				endforeach;
		}



		protected function render_um_user_meta_front( $settings ) {

			if( empty($settings['um_user_meta_control_front']) ) return;

				foreach( $settings['um_user_meta_control_front'] as $item ) :
						$user_id = um_user('ID');
						um_fetch_user( $user_id );
						$meta_value = um_user($item['um_user_meta_field_value_front']);


						//$meta_value = get_user_meta( um_user( 'ID' ), $item['um_user_meta_field_value'], true );

					if ( !empty( $meta_value ) ) : ?>
						<span class="um-meta-field">

							<?php if($item['um_user_meta_field_label_front']) :?>
								<span class="um-custom-label"><?php echo esc_attr(UM()->Fields()->get_label($item['um_user_meta_field_value_front']));?>:</span>
							<?php endif;?>

							<span class="um-custom-field">
							<?php
							// URL
							if( UM()->Fields()->get_field_type($item['um_user_meta_field_value_front']) == "url"){

								// Get the filename
								$link_url = esc_url(um_user( $item['um_user_meta_field_value_front'] ));

								echo "<a href='$link_url'>$link_url</a>";
								echo "<i class='um-faicon-link'></i>";
							}

							// Phone Number
							if( UM()->Fields()->get_field_type($item['um_user_meta_field_value_front']) == "tel"){

								// Get the Phone Number
								$link_tel = um_user( $item['um_user_meta_field_value_front'] );
								
								echo "<a href='tel:$link_tel'>$link_tel</a>";
								echo "<i class='um-faicon-phone'></i>";
							}

							// Image URL
							if( UM()->Fields()->get_field_type($item['um_user_meta_field_value_front']) == "image"){

								// Get the filename
								$image_filename = um_user( $item['um_user_meta_field_value_front'] );
										
								// Check if the file is there
								if(!empty($image_filename)){
									// Get the WordPress upload directory URL
									$upload_dir = wp_upload_dir();

									// Look in to Ultimate Member foler and construct the image URL
									$image_url = esc_url( $upload_dir['baseurl'] . '/ultimatemember/' . um_user( 'ID' ) . '/' . $image_filename );

									// Output the image
									echo "<img src='$image_url'>";
								}
							}

							// File URL
							if( UM()->Fields()->get_field_type($item['um_user_meta_field_value_front']) == "file"){

								// Get the filename
								$image_filename = um_user( $item['um_user_meta_field_value_front'] );
										
								// Check if the file is there
								if(!empty($image_filename)){
									// Get the WordPress upload directory URL
									$upload_dir = wp_upload_dir();

									// Look in to Ultimate Member foler and construct the image URL
									$image_url = esc_url( $upload_dir['baseurl'] . '/ultimatemember/' . um_user( 'ID' ) . '/' . $image_filename );

									// Output the File
									$file_info = um_user( $item['um_user_meta_field_value_front'] . "_metadata" );
									$file_name = $file_info['original_name'];

									echo "<a href='$image_url' target='_blank'>{$file_name}</a>";
									echo "<i class='um-faicon-file-text-o'></i>";

								}
							}


							// youtube_video
							if( UM()->Fields()->get_field_type($item['um_user_meta_field_value_front']) == "youtube_video"){
								// Get the YouTube URL
								$yt_url = esc_url(um_user( $item['um_user_meta_field_value_front'] ));	
								$code = substr($yt_url, strpos($yt_url,'watch?v=') + 8);?>
								
								<iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo $code;?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
							<?php
							}
							?>
								<?php 
								if( 
									UM()->Fields()->get_field_type($item['um_user_meta_field_value_front']) != "image"
									AND UM()->Fields()->get_field_type($item['um_user_meta_field_value_front']) != "youtube_video"
									AND UM()->Fields()->get_field_type($item['um_user_meta_field_value_front']) != "file"
									AND UM()->Fields()->get_field_type($item['um_user_meta_field_value_front']) != "url"
									AND UM()->Fields()->get_field_type($item['um_user_meta_field_value_front']) != "tel"
								){
									if(is_array($meta_value)){
										$meta_value_copy = $meta_value;
										foreach($meta_value as $individual){
											echo esc_attr($individual);
										    if (next($meta_value_copy )) {
	        									echo ', '; // Add comma for all elements instead of last
	    									}	
										}
									}else{
										echo esc_attr($meta_value);
									}
								}
								
								?>
							</span>
						</span>
					<?php endif;
				endforeach;
		}

		// Output of the Elementor Widget
		protected function render() {

			$settings 			= $this->get_settings();

			$member_name_type 	= isset( $settings['user_display_title'] ) ? $settings['user_display_title'] : 'display_name';
			$number_of_member 	= isset( $settings['user_numbers'] ) ? absint( $settings['user_numbers'] ) : 4;
			$member_order_by 	= isset( $settings['query_user_order_by'] ) ? $settings['query_user_order_by'] : 'display_name';
			$member_order 		= isset( $settings['query_user_order'] ) ? $settings['query_user_order'] : 'ASC';
			$member_role_multi 	= ! empty( $settings['query_user_roles'] ) ? $settings['query_user_roles'] : false;
			$selected_roles 	= ! empty( $settings['query_show_selected_roles'] ) ? $settings['query_show_selected_roles'] : false;
			
			$column 			= isset( $settings['grid_column_no'] ) ? $settings['grid_column_no'] : '3';
			$align 				= isset( $settings['title_alignment_front'] ) ? $settings['title_alignment_front'] : 'text-left';
			$align_back 		= isset( $settings['title_alignment_back'] ) ? $settings['title_alignment_back'] : 'text-left';
			$layout 			= isset( $settings['user_layout'] ) ? $settings['user_layout'] : 'flip-left';

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
					<div class="um-item-user">
						<div class="flip-card <?php echo $layout;?>">
						  <div class="flip-card-inner">

						    <div class="flip-card-front">
								<div class="um-widget-member-holder">
								<div class="um-elementor-member-content">
						      	<div class="um-custom-field-container <?php echo esc_attr($align);?>">

								<?php if( $settings['um_elementor_show_user_image'] == 1 ) : ?>
									<div class="um-elementor-member-image <?php echo esc_attr($align);?>">
										<a title="<?php echo um_user('display_name')?>" href="<?php echo esc_url(um_user_profile_url());?>">
										    <?php echo um_get_avatar( '', $user_id, 100 );?>
										</a>
									</div>
								<?php endif;?>

								<?php if ( $settings['um_elementor_show_member_name'] == 1 ) : ?>
									<div class="um-elementor-member-name <?php echo esc_attr($align);?>">
										<p>
											<a title="<?php echo esc_attr(um_user( 'first_name' ));?>" href="<?php echo esc_url(um_user_profile_url());?>">
												<?php echo um_user( "$member_name_type" );?>
											</a>
										</p>
									</div>
								<?php endif;?>

								<?php $this->render_um_user_meta_front( $settings );?>
								</div>
								</div>
								</div>
						    </div>

						    <div class="flip-card-back">
								<div class="um-widget-member-holder">
									<div class="um-elementor-member-content">
										<div class="um-custom-field-container <?php echo esc_attr($align_back);?>">
											<?php $this->render_um_user_meta( $settings );?>
										</div>
									</div>
								</div>
						    </div>

						  </div>
						</div>
					</div>
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
		$widgets_manager->register( new UM_ELEMENTOR_LIST_FLIP_MODULE() );
	} );