<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }


/*
 * Add ACF form elements in the form elements select box
 */
function buddyforms_acf_elements_to_select( $elements_select_options ) {
	global $post;

	if ( $post->post_type != 'buddyforms' ) {
		return $elements_select_options;
	}
	$elements_select_options['acf']['label']               = 'ACF';
	$elements_select_options['acf']['class']               = 'bf_show_if_f_type_all';
	$elements_select_options['acf']['fields']['acf-field'] = array(
		'label' => __( 'ACF Field', 'buddyforms' ),
	);

	$elements_select_options['acf']['fields']['acf-group'] = array(
		'label' => __( 'ACF Group', 'buddyforms' ),
	);

	return $elements_select_options;
}

add_filter( 'buddyforms_add_form_element_select_option', 'buddyforms_acf_elements_to_select', 1, 2 );


/*
 * Create the new ACF Form Builder Form Elements
 *
 */
function buddyforms_acf_form_builder_form_elements( $form_fields, $form_slug, $field_type, $field_id ) {
	global $field_position, $buddyforms;

	$post_type = 'acf';
	if ( post_type_exists( 'acf-field-group' ) ) {
		$post_type = 'acf-field-group';
	}

	switch ( $field_type ) {
		case 'acf-field':
			unset( $form_fields );

			// get acf grups
			$posts = get_posts(
				array(
					'numberposts'      => - 1,
					'post_type'        => $post_type,
					'orderby'          => 'menu_order title',
					'order'            => 'asc',
					'suppress_filters' => false,
				)
			);

			$acf_groups['none'] = 'Select Group';
			if ( $posts ) {
				foreach ( $posts as $post ) {
					$acf_groups[ $post->ID ] = $post->post_title;
				}
			}

			$acf_group = 'false';
			if ( isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['acf_group'] ) ) {
				$acf_group = $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['acf_group'];
			}
			$form_fields['general']['acf_group'] = new Element_Select(
				'',
				'buddyforms_options[form_fields][' . $field_id . '][acf_group]',
				$acf_groups,
				array(
					'value'         => $acf_group,
					'class'         => 'bf_acf_field_group_select',
					'data-field_id' => $field_id,
				)
			);

			// load fields
			if ( post_type_exists( 'acf-field-group' ) ) {
				if ( $acf_group ) {
					$fields = acf_get_fields( $acf_group );
				}
			} else {
				if ( $acf_group ) {
					$fields = apply_filters( 'acf/field_group/get_fields', array(), $acf_group );
				}
			}

			$field_select = array();
			if ( $fields ) {
				foreach ( $fields as $field ) {
					if ( $field['name'] ) {
						$field_select[ $field['key'] ] = $field['label'];
					}
				}
			}

			$acf_field = 'false';
			if ( isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['acf_field'] ) ) {
				$acf_field = $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['acf_field'];
			}
			$form_fields['general']['acf_field'] = new Element_Select(
				'',
				'buddyforms_options[form_fields][' . $field_id . '][acf_field]',
				$field_select,
				array(
					'value' => $acf_field,
					'class' => 'bf_acf_fields_select bf_acf_' . $field_id,
				)
			);

			$name = 'ACF-Field';
			if ( $acf_field && $acf_field != 'false' ) {
				$name = 'ACF Field: ' . $acf_field;
			}
			$form_fields['general']['name'] = new Element_Hidden( 'buddyforms_options[form_fields][' . $field_id . '][name]', $name );

			$form_fields['general']['slug']  = new Element_Hidden( 'buddyforms_options[form_fields][' . $field_id . '][slug]', 'acf_' . $acf_field );
			$form_fields['general']['type']  = new Element_Hidden( 'buddyforms_options[form_fields][' . $field_id . '][type]', $field_type );
			$form_fields['general']['order'] = new Element_Hidden( 'buddyforms_options[form_fields][' . $field_id . '][order]', $field_position, array( 'id' => 'buddyforms/' . $form_slug . '/form_fields/' . $field_id . '/order' ) );
			break;
		case 'acf-group':
			unset( $form_fields );

			// get acf's
			$posts = get_posts(
				array(
					'numberposts'      => - 1,
					'post_type'        => $post_type,
					'orderby'          => 'menu_order title',
					'order'            => 'asc',
					'suppress_filters' => false,
				)
			);

			$acf_groups = array();
			if ( $posts ) {
				foreach ( $posts as $post ) {
					$acf_groups[ $post->ID ] = $post->post_title;
				}
			}

			$acf_group = 'false';
			if ( isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['acf_group'] ) ) {
				$acf_group = $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['acf_group'];
			}
			$form_fields['general']['acf_group'] = new Element_Select( '', 'buddyforms_options[form_fields][' . $field_id . '][acf_group]', $acf_groups, array( 'value' => $acf_group ) );

			$name = 'ACF-Group';
			if ( $acf_group != 'false' ) {
				$name = ' ACF Group: ' . $acf_group;
			}
			$form_fields['general']['name'] = new Element_Hidden( 'buddyforms_options[form_fields][' . $field_id . '][name]', $name );

			$form_fields['general']['slug']  = new Element_Hidden( 'buddyforms_options[form_fields][' . $field_id . '][slug]', 'acf-fields-group' );
			$form_fields['general']['type']  = new Element_Hidden( 'buddyforms_options[form_fields][' . $field_id . '][type]', $field_type );
			$form_fields['general']['order'] = new Element_Hidden( 'buddyforms_options[form_fields][' . $field_id . '][order]', $field_position, array( 'id' => 'buddyforms/' . $form_slug . '/form_fields/' . $field_id . '/order' ) );
			break;

	}

	return $form_fields;
}

add_action( 'acf/input/admin_enqueue_scripts', 'buddyforms_acf_form_builder_form_elements_enqueue_scripts' );

function buddyforms_acf_form_builder_form_elements_enqueue_scripts() {
	if ( ! is_admin() ) {
		wp_enqueue_script(
			'buddyforms-acf-js',
			BUDDYFORMS_ACF_PLUGIN_URL . '/assets/js/buddyforms-acf.js',
			array(
				'jquery',
				'acf-input',
				'buddyforms-js',
			),
			BuddyFormsACF::getVersion()
		);
	}
}

add_filter( 'buddyforms_form_element_add_field', 'buddyforms_acf_form_builder_form_elements', 1, 5 );


function buddyforms_acf_manipulate_labels( &$tmp, &$acf_form_field, $field, $form_slug, $form ) {
	global $buddyforms;
	$labels_layout    = isset( $buddyforms[ $form_slug ]['layout']['labels_layout'] ) ? $buddyforms[ $form_slug ]['layout']['labels_layout'] : 'inline';
	$inline_is_output = false;
	// Define how look the label or the placeholder
	if ( $labels_layout === 'inline' ) {
		if ( in_array( $field['type'], array( 'text', 'textarea', 'number', 'email', 'url', 'password', 'wysiwyg', 'message' ) ) ) {
			$placeholder = $field['label'];
			if ( $field['required'] ) {
				$acf_form_field = str_replace( 'type=', 'required="required" type=', $acf_form_field );
				$placeholder   .= ' ' . $form->getRequiredPlainSignal();
			}
			$replace_placeholder = sprintf( 'placeholder="%s"', $placeholder );
			$acf_form_field      = str_replace( 'type=', $replace_placeholder . ' type=', $acf_form_field );
			$inline_is_output    = true;
		}
	}
	if ( ! $inline_is_output ) {
		$label_string = sprintf( "<label class='acf-label' for=\"_%s\"> %s", esc_attr( 'acf-' . $field['key'] ), $field['label'] );
		if ( ! empty( $field['required'] ) ) {
			$label_string .= sprintf( "<span class='required is-required' aria-required='true'>%s</span>", $form->getRequiredSignal() );
		}
		$label_string .= '</label>';
		$tmp          .= $label_string;
	}
}

/*
 * Display the new ACF Fields in the frontend form
 *
 * @param Form $form
 * @param array $form_args
 *
 * @return mixed
 */
function buddyforms_acf_frontend_form_elements( $form, $form_args ) {
	$form_slug   = '';
	$customfield = array();
	$post_id     = 0;

	extract( $form_args );

	if ( ! empty( $customfield ) && $customfield['type'] == 'acf-group' || $customfield['type'] == 'acf-field' ) {
		global $buddyforms, $nonce;

		$post_type = $buddyforms[ $form_slug ]['post_type'];

		if ( ! $post_type ) {
			return $form;
		}

		if ( ! isset( $customfield['type'] ) ) {
			return $form;
		}

		acf_form_head();

		acf_localize_data(
			array(
				'screen'     => 'buddyforms_form_acf-test-requires',
				'post_id'    => $post_id,
				'validation' => true,
			)
		);

		$form_type = '';
		if ( ! empty( $buddyforms ) && ! empty( $form_slug ) && ! empty( $buddyforms[ $form_slug ] ) ) {
			$form_type = ! empty( $buddyforms[ $form_slug ]['form_type'] ) ? $buddyforms[ $form_slug ]['form_type'] : '';
		}

		if ( ! empty( $form_type ) && $form_type === 'registration' ) {
			$post_id = sprintf( 'user_%s', get_current_user_id() );
		}

		$post_id = empty( $post_id ) ? 'new_post' : $post_id;

		switch ( $customfield['type'] ) {
			case 'acf-field':
				$tmp = '';

				if ( ! $nonce ) {
					$tmp .= '<input type="hidden" name="_acfnonce" value="' . wp_create_nonce( 'input' ) . '" />';
				}

				if ( ! isset( $customfield['acf_field'] ) ) {
					return $form;
				}

				$field = get_field_object( $customfield['acf_field'], $post_id, false );

				// make sure we have a field key. If user switch from free to pro ACF this can happen so we need to catch it...
				if ( ! isset( $field['key'] ) ) {
					return $form;
				}

				$field['name'] = 'fields[' . $field['key'] . ']';
				ob_start();

				if ( post_type_exists( 'acf-field-group' ) ) {
					create_field( $field );
				} else {
					do_action( 'acf/create_field', $field, $post_id );
				}
				$acf_form_field = ob_get_clean();

				if ( empty( $acf_form_field ) ) {
					return $form;
				}

				$required_class = '';

				$acf_wrapper = array( 'class' => '' );
				if ( isset( $field['wrapper'] ) ) {
					$acf_wrapper = $field['wrapper'];
				}

				// if the field type is not set for any reason, make it a text field. This check is again in tplace for people how switch from pro to free and have some elements with no type
				$field_type = isset( $field['type'] ) ? $field['type'] : 'text';

				// Create the BuddyForms Form Element Structure
				if ( post_type_exists( 'acf-field-group' ) ) {
					// Create the BuddyForms Form Element Structure
					$tmp .= sprintf( '<div data-target="acf-%s" class="bf_field bf_field_group acf-field acf-field-%s acf-%s %s %s" data-name="%s" data-key="%s" data-type="%s">', $field['key'], str_replace( '_', '-', $field_type ), str_replace( '_', '-', $field['key'] ), $acf_wrapper['class'], $required_class, $field['name'], $field['key'], $field['type'] );
				} else {
					// Create the BuddyForms Form Element Structure
					$tmp .= sprintf( '<div data-target="acf-%s" class="bf_field_group field field_type-%s field_key-%s %s %s" data-field_name="%s" data-field_key="%s" data-field_type="%s">', $field['key'], $field_type, $field['key'], $acf_wrapper['class'], $required_class, $field['name'], $field['key'], $field_type );
				}

				buddyforms_acf_manipulate_labels( $tmp, $acf_form_field, $field, $form_slug, $form );

				// Not sure what this is for, but it causes data fields in repeaters fail
				// $acf_form_field = str_replace( ' type=', 'data-form="' . $form_slug . '" type=', $acf_form_field );

				// Ensure data-types are kept where appropriate so rich fields (such as date_pickers, selct, etc) have their expected behaviour
				$acf_form_field = str_replace( ' type="text"', ' type="text" data-type="text"', $acf_form_field );
				$acf_form_field = str_replace( ' type="textarea"', ' type="textarea" data-type="textarea"', $acf_form_field );
				$acf_form_field = str_replace( ' type="number"', ' type="number" data-type="number"', $acf_form_field );
				$acf_form_field = str_replace( ' type="range"', ' type="range" data-type="range"', $acf_form_field );
				$acf_form_field = str_replace( ' type="email"', ' type="email" data-type="email"', $acf_form_field );
				$acf_form_field = str_replace( ' type="url"', ' type="url" data-type="url"', $acf_form_field );
				$acf_form_field = str_replace( ' type="password"', ' type="password" data-type="password"', $acf_form_field );
				$acf_form_field = str_replace( ' type="image"', ' type="image" data-type="image"', $acf_form_field );
				$acf_form_field = str_replace( ' type="file"', ' type="file" data-type="file"', $acf_form_field );
				$acf_form_field = str_replace( ' type="wysiwyg"', ' type="wysiwyg" data-type="wysiwyg"', $acf_form_field );
				$acf_form_field = str_replace( ' type="oembed"', ' type="oembed" data-type="oembed"', $acf_form_field );
				$acf_form_field = str_replace( ' type="select"', ' type="select" data-type="select"', $acf_form_field );
				$acf_form_field = str_replace( ' type="checkbox"', ' type="checkbox" data-type="checkbox"', $acf_form_field );
				$acf_form_field = str_replace( ' type="button"', ' type="button" data-type="button"', $acf_form_field );
				$acf_form_field = str_replace( ' type="true_false"', ' type="true_false" data-type="true_false"', $acf_form_field );
				$acf_form_field = str_replace( ' type="link"', ' type="link" data-type="link"', $acf_form_field );
				$acf_form_field = str_replace( ' type="post_object"', ' type="post_object" data-type="post_object"', $acf_form_field );
				$acf_form_field = str_replace( ' type="page_link"', ' type="page_link" data-type="page_link"', $acf_form_field );
				$acf_form_field = str_replace( ' type="relationship"', ' type="relationship" data-type="relationship"', $acf_form_field );
				$acf_form_field = str_replace( ' type="taxonomy"', ' type="taxonomy" data-type="taxonomy"', $acf_form_field );
				$acf_form_field = str_replace( ' type="user"', ' type="user" data-type="user"', $acf_form_field );
				$acf_form_field = str_replace( ' type="google"', ' type="google" data-type="google"', $acf_form_field );
				$acf_form_field = str_replace( ' type="date_picker"', ' type="date_picker" data-type="date_picker"', $acf_form_field );
				$acf_form_field = str_replace( ' type="date_time_picker"', ' type="date_time_picker" data-type="date_time_picker"', $acf_form_field );
				$acf_form_field = str_replace( ' type="time_picker"', ' type="time_picker" data-type="time_picker"', $acf_form_field );
				$acf_form_field = str_replace( ' type="color_picker"', ' type="color_picker" data-type="color_picker"', $acf_form_field );
				$acf_form_field = str_replace( ' type="message"', ' type="message" data-type="message"', $acf_form_field );
				$acf_form_field = str_replace( ' type="accordion"', ' type="accordion" data-type="accordion"', $acf_form_field );
				$acf_form_field = str_replace( ' type="tab"', ' type="tab" data-type="tab"', $acf_form_field );
				$acf_form_field = str_replace( ' type="group"', ' type="group" data-type="group"', $acf_form_field );

				$acf_form_field = str_replace( 'acf-input-wrap', 'bf_inputs acf-input acf-input-wrap', $acf_form_field );

				if ( $field['instructions'] ) {
					$tmp .= '<span class="help-inline">' . $field['instructions'] . '</span>';
				}
				$tmp .= $acf_form_field;
				$tmp .= '</div>';

				$form->addElement( new Element_HTML( $tmp ) );

				break;
			case 'acf-group':
				// load fields
				if ( post_type_exists( 'acf-field-group' ) ) {
					$parent = (int) $customfield['acf_group'];
					$fields = acf_get_fields( $parent );
				} else {
					$fields = apply_filters( 'acf/field_group/get_fields', array(), $customfield['acf_group'] );
				}
				if ( ! isset( $fields ) || ! is_array( $fields ) ) {
					return $form;
				}

				$tmp = '';

				if ( ! $nonce ) {
					$tmp .= '<input type="hidden" name="_acfnonce" value="' . wp_create_nonce( 'input' ) . '" />';
				}

				foreach ( $fields as $field ) {
					$field_output = '';
					// set value
					if ( ! isset( $field['value'] ) ) {
						$field['value'] = get_field( $field['name'], $post_id, false );
					}

					// make sure we have a field key. If user switch from free to pro ACF this can happen so we need to catch it...
					if ( ! isset( $field['key'] ) ) {
						return $form;
					}

					$field['name'] = 'fields[' . $field['key'] . ']';

					ob_start();
					if ( post_type_exists( 'acf-field-group' ) ) {
						create_field( $field );
					} else {
						do_action( 'acf/create_field', $field, $post_id );
					}
					$acf_form_field = ob_get_clean();

					if ( empty( $acf_form_field ) ) {
						continue;
					}
					if ( $field['type'] == 'accordion' ) {

						$acf_form_field = str_replace( 'acf-fields', 'acf-fields acf-accordion-content ', $acf_form_field );
					}

					$required_class = '';

					$acf_wrapper = array( 'class' => '' );
					if ( isset( $field['wrapper'] ) ) {
						$acf_wrapper = $field['wrapper'];
					}

					// if the field type is not set for any reason, make it a text field. This check is again in tplace for people how switch from pro to free and have some elements with no type
					$field_type = isset( $field['type'] ) ? $field['type'] : 'text';

					// Create the BuddyForms Form Element Structure
					if ( post_type_exists( 'acf-field-group' ) ) {
						// Create the BuddyForms Form Element Structure

						if ( ! empty( $field['conditional_logic'] ) ) {
							$rule          = esc_html( json_encode( $field['conditional_logic'] ) );
							$field_output .= sprintf( '<div data-target="acf-%s" class="bf_field bf_field_group acf-field acf-field-%s acf-%s %s %s" data-name="%s" data-key="%s" data-type="%s" data-conditions="%s"  >', $field['key'], str_replace( '_', '-', $field_type ), str_replace( '_', '-', $field['key'] ), $acf_wrapper['class'], $required_class, $field['name'], $field['key'], $field['type'], $rule );
						} else {
							$field_output .= sprintf( '<div data-target="acf-%s" class="bf_field bf_field_group acf-field acf-field-%s acf-%s %s %s" data-name="%s" data-key="%s" data-type="%s"  >', $field['key'], str_replace( '_', '-', $field_type ), str_replace( '_', '-', $field['key'] ), $acf_wrapper['class'], $required_class, $field['name'], $field['key'], $field['type'] );
						}
					} else {
						// Create the BuddyForms Form Element Structure
						$field_output .= sprintf( '<div id="acf-%s" class="bf_field_group field field_type-%s field_key-%s %s %s" data-field_name="%s" data-field_key="%s" data-field_type="%s">', $field['key'], $field_type, $field['key'], $acf_wrapper['class'], $required_class, $field['name'], $field['key'], $field_type );
					}

					buddyforms_acf_manipulate_labels( $field_output, $acf_form_field, $field, $form_slug, $form );

					$acf_form_field = str_replace( 'acf-input-wrap', 'bf_inputs acf-input acf-input-wrap ', $acf_form_field );

					if ( $field['instructions'] ) {
						$field_output .= '<span class="help-inline">' . $field['instructions'] . '</span>';
					}

					$field_output .= $acf_form_field;
					ob_start();
					echo $field_output . '</div>';
					$tmp .= ob_get_clean();
				}

				$form->addElement( new Element_HTML( $tmp ) );
				break;
		}
	}

	return $form;
}

add_filter( 'buddyforms_forms_classes', 'buddyforms_acf_form_classes', 10, 3 );
function buddyforms_acf_form_classes( $classes, $instance, $form_slug ) {
	return 'acf-form ' . $classes;
}

add_filter( 'buddyforms_create_edit_form_display_element', 'buddyforms_acf_frontend_form_elements', 1, 2 );

/*
 * Save ACF Fields
 *
 */
function buddyforms_acf_update_post_meta( $customfield, $post_id ) {
	if ( $customfield['type'] == 'acf-group' || $customfield['type'] == 'acf-field' ) {

		global $buddyforms, $form_slug;

		$form_type = '';
		if ( ! empty( $buddyforms ) && ! empty( $form_slug ) && ! empty( $buddyforms[ $form_slug ] ) ) {
			$form_type = ! empty( $buddyforms[ $form_slug ]['form_type'] ) ? $buddyforms[ $form_slug ]['form_type'] : '';
		}

		if ( ! empty( $form_type ) && $form_type === 'registration' ) {
			$post_id = sprintf( 'user_%s', get_current_user_id() );
		}

		if ( $customfield['type'] == 'acf-group' ) {

			$group_ID = $customfield['acf_group'];

			$fields = array();

			// load fields
			if ( post_type_exists( 'acf-field-group' ) ) {
				$fields = acf_get_fields( $group_ID );

				if ( $fields ) {
					foreach ( $fields as $field ) {
						if ( isset( $_POST['acf'][ $field['key'] ] ) ) {
							update_field( $field['key'], $_POST['acf'][ $field['key'] ], $post_id );
						}
					}
				}
			} else {
				$fields = apply_filters( 'acf/field_group/get_fields', $fields, $group_ID );
				if ( $fields ) {
					foreach ( $fields as $field ) {
						if ( isset( $_POST[ $field['name'] ] ) ) {
							update_field( $field['key'], $_POST[ $field['name'] ], $post_id );
						}
					}
				}
			}
		}

		if ( $customfield['type'] == 'acf-field' ) {
			if ( post_type_exists( 'acf-field-group' ) ) {
				if ( isset( $_POST['acf'][ $customfield['acf_field'] ] ) ) {
					update_field( $customfield['acf_field'], $_POST['acf'][ $customfield['acf_field'] ], $post_id );
				}
			} else {
				if ( isset( $_POST['fields'][ $customfield['acf_field'] ] ) ) {
					update_field( $customfield['acf_field'], $_POST['fields'][ $customfield['acf_field'] ], $post_id );
				}
			}
		}
	}
}

add_action( 'buddyforms_update_post_meta', 'buddyforms_acf_update_post_meta', 10, 2 );

function buddyforms_acf_get_fields() {

	// load fields
	$fields = array();
	if ( post_type_exists( 'acf-field-group' ) ) {
		if ( ! empty( $_POST['fields_group_id'] ) ) {
			$fields = acf_get_fields( $_POST['fields_group_id'] );
		}
	} else {
		if ( ! empty( $_POST['fields_group_id'] ) ) {
			$fields = apply_filters( 'acf/field_group/get_fields', array(), $_POST['fields_group_id'] );
		}
	}

	$field_select = array();
	if ( ! empty( $fields ) ) {
		foreach ( $fields as $field ) {
			if ( $field['name'] ) {
				$field_select[ $field['key'] ] = $field['label'];
			}
		}
	}
	echo json_encode( $field_select );
	die();
}

add_action( 'wp_ajax_buddyforms_acf_get_fields', 'buddyforms_acf_get_fields' );

function buddyforms_acf_process_submission_end( $args ) {
	extract( $args );

	if ( ! isset( $user_id ) ) {
		return;
	}

	global $buddyforms;

	if ( isset( $buddyforms[ $form_slug ] ) ) {
		if ( isset( $buddyforms[ $form_slug ]['form_fields'] ) ) {
			foreach ( $buddyforms[ $form_slug ]['form_fields'] as $field_key => $field ) {
				if ( isset( $field['mapped_xprofile_field'] ) && $field['mapped_xprofile_field'] != 'none' ) {
					$xfield = new BP_XProfile_Field( $field['mapped_xprofile_field'] );
					if ( function_exists( 'xprofile_set_field_data' ) ) {
						if ( $field['type'] == 'acf-group' || $field['type'] == 'acf-field' ) {
							if ( $field['type'] == 'acf-field' ) {
								if ( post_type_exists( 'acf-field-group' ) ) {
									$field_value = isset( $_POST['acf'][ $field['acf_field'] ] ) ? $_POST['acf'][ $field['acf_field'] ] : '';
								} else {
									$field_value = isset( $_POST['fields'][ $field['acf_field'] ] ) ? $_POST['fields'][ $field['acf_field'] ] : '';
								}
								if ( isset( $field_value ) ) {
									xprofile_set_field_data( $field['mapped_xprofile_field'], $user_id, $field_value );
								}
							}
							if ( $field['type'] == 'acf-group' ) {
								$group_ID = $field['acf_group'];
								$fields   = array();
								// load fields
								if ( post_type_exists( 'acf-field-group' ) ) {
									$fields = acf_get_fields( $group_ID );
									if ( $fields ) {
										foreach ( $fields as $acf_field ) {
											if ( isset( $_POST['acf'][ $acf_field['key'] ] ) ) {
												xprofile_set_field_data( $acf_field['mapped_xprofile_field'], $user_id, $_POST['acf'][ $acf_field['key'] ] );
											}
										}
									}
								} else {
									$fields = apply_filters( 'acf/field_group/get_fields', $fields, $group_ID );
									if ( $fields ) {
										foreach ( $fields as $acf_field ) {
											if ( isset( $_POST[ $acf_field['name'] ] ) ) {
												xprofile_set_field_data( $acf_field['mapped_xprofile_field'], $user_id, $_POST[ $acf_field['name'] ] );
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}

add_action( 'buddyforms_process_submission_end', 'buddyforms_acf_process_submission_end', 99, 1 );
