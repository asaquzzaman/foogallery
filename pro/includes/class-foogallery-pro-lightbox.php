<?php
/**
 * FooGallery Pro Lightbox class
 */
if ( ! class_exists( 'FooGallery_Pro_Lightbox' ) ) {

	class FooGallery_Pro_Lightbox {

		function __construct() {
			//add lightbox custom fields
			add_filter( 'foogallery_override_gallery_template_fields', array( $this, 'lightbox_custom_fields' ), 10, 2 );

			//add the data options needed for lightbox
			add_filter( 'foogallery_build_container_data_options', array( $this, 'add_data_options' ), 10, 3 );

			//build up any preview arguments
			add_filter( 'foogallery_preview_arguments', array( $this, 'preview_arguments' ), 10, 3 );

			//set the settings icon for lightbox
			add_filter( 'foogallery_gallery_settings_metabox_section_icon', array( $this, 'add_section_icons' ) );

			//add the FooGallery lightbox option
			add_filter( 'foogallery_gallery_template_field_lightboxes', array($this, 'add_lightbox') );

			//alter the default lightbox to be FooGallery PRO Lightbox
			add_filter( 'foogallery_alter_gallery_template_field', array( $this, 'make_foogallery_default_lightbox' ), 99, 2 );

			//add specific lightbox data attribute to the container div
			add_filter( 'foogallery_build_container_attributes', array( $this, 'add_lightbox_data_attributes' ), 10, 2 );
		}

		/**
		 * Add fields to all galleries.
		 *
		 * @param $fields
		 * @param $template
		 *
		 * @return mixed
		 * @uses "foogallery_override_gallery_template_fields"
		 *
		 */
		public function lightbox_custom_fields( $fields, $template ) {
			$use_lightbox = true;
			if ( $template && array_key_exists( 'panel_support', $template ) && true === $template['panel_support'] ) {
				$use_lightbox = false;
			}

			$section = $use_lightbox ? __( 'Lightbox', 'foogallery' ) : __( 'Panel', 'foogallery' );

			if ( $use_lightbox ) {
                $field[] = array(
                    'id'      => 'lightbox_help',
                    'title'   => __( 'Lightbox Help', 'foogallery' ),
                    'desc'    => __( 'The below settings are only applied and used if you have your lightbox set to "FooGallery PRO Lightbox"', 'foogallery' ),
                    'section' => $section,
                    'type'    => 'help'
                );
            }

			$field[] = array(
				'id'      => 'lightbox_theme',
				'title'   => __( 'Theme', 'foogallery' ),
				'desc'    => __( 'The overall appearance including background and button color. By default it will inherit from Appearance -> Theme', 'foogallery' ),
				'section' => $section,
				'spacer'  => '<span class="spacer"></span>',
				'type'    => 'radio',
				'default' => '',
				'choices' => apply_filters( 'foogallery_gallery_template_lightbox_theme_choices', array(
					''  => __( 'Inherit', 'foogallery' ),
					'fg-light'  => __( 'Light', 'foogallery' ),
					'fg-dark'   => __( 'Dark', 'foogallery' )
				) ),
				'row_data'=> array(
					'data-foogallery-change-selector' => 'input',
					'data-foogallery-preview' => 'shortcode',
					'data-foogallery-value-selector' => 'input:checked',
				)
			);

			$field[] = array(
				'id'      => 'lightbox_button_theme',
				'title'   => __( 'Control Color', 'foogallery' ),
				'desc'    => __( 'You can override the button controls color. By default it will inherit from the theme.', 'foogallery' ),
				'section' => $section,
				'spacer'  => '<span class="spacer"></span>',
				'type'    => 'radio',
				'default' => '',
				'choices' => apply_filters( 'foogallery_gallery_template_lightbox_button_theme_choices', array(
					''                    => __( 'Same As Theme', 'foogallery' ),
					'custom' => __( 'Custom Color',   'foogallery' ),
				) ),
				'row_data'=> array(
					'data-foogallery-change-selector' => 'input',
					'data-foogallery-preview' => 'shortcode',
					'data-foogallery-value-selector' => 'input:checked',
				)
			);

			$field[] = array(
				'id'      => 'lightbox_custom_button_theme',
				'title'   => __( 'Custom Control Color', 'foogallery' ),
				'desc'    => __( 'You can override the button controls color by selecting a color.', 'foogallery' ),
				'section' => $section,
				'spacer'  => '<span class="spacer"></span>',
				'type'    => 'htmlicon',
				'default' => 'fg-button-light',
				'choices' => apply_filters( 'foogallery_gallery_template_lightbox_custom_button_theme_choices', array(
					'fg-button-light'   => array( 'label' => __( 'Light',   'foogallery' ), 'html' => '<div class="foogallery-setting-panel_theme fg-light"></div>' ),
					'fg-button-dark'    => array( 'label' => __( 'Dark',    'foogallery' ), 'html' => '<div class="foogallery-setting-panel_theme fg-dark"></div>' ),
					'fg-button-blue'    => array( 'label' => __( 'Blue',    'foogallery' ), 'html' => '<div class="foogallery-setting-panel_theme fg-blue"></div>' ),
					'fg-button-purple'  => array( 'label' => __( 'Purple',  'foogallery' ), 'html' => '<div class="foogallery-setting-panel_theme fg-purple"></div>' ),
					'fg-button-green'   => array( 'label' => __( 'Green',   'foogallery' ), 'html' => '<div class="foogallery-setting-panel_theme fg-green"></div>' ),
					'fg-button-red'     => array( 'label' => __( 'Red',     'foogallery' ), 'html' => '<div class="foogallery-setting-panel_theme fg-red"></div>' ),
					'fg-button-orange'  => array( 'label' => __( 'Orange',  'foogallery' ), 'html' => '<div class="foogallery-setting-panel_theme fg-orange"></div>' ),
				) ),
				'row_data'=> array(
					'data-foogallery-change-selector'          => 'input',
					'data-foogallery-preview'                  => 'shortcode',
					'data-foogallery-value-selector'           => 'input:checked',
					'data-foogallery-hidden'                   => true,
					'data-foogallery-show-when-field'          => 'lightbox_button_theme',
					'data-foogallery-show-when-field-operator' => '===',
					'data-foogallery-show-when-field-value'    => 'custom',
				)
			);

			$field[] = array(
				'id'      => 'lightbox_button_highlight',
				'title'   => __( 'Control Hover Color', 'foogallery' ),
				'desc'    => __( 'You can override the button controls hover color. By default it will inherit from the theme.', 'foogallery' ),
				'section' => $section,
				'spacer'  => '<span class="spacer"></span>',
				'type'    => 'radio',
				'default' => '',
				'choices' => apply_filters( 'foogallery_gallery_template_lightbox_button_highlight_choices', array(
                    ''       => __( 'Same As Theme', 'foogallery' ),
                    'custom' => __( 'Custom Color', 'foogallery' ),
				) ),
				'row_data'=> array(
					'data-foogallery-change-selector' => 'input',
					'data-foogallery-preview' => 'shortcode',
					'data-foogallery-value-selector' => 'input:checked',
				)
			);

			$field[] = array(
				'id'      => 'lightbox_custom_button_highlight',
				'title'   => __( 'Custom Control Hover Color', 'foogallery' ),
				'desc'    => __( 'You can override the button controls hover color by selecting a color.', 'foogallery' ),
				'section' => $section,
				'spacer'  => '<span class="spacer"></span>',
				'type'    => 'htmlicon',
				'default' => 'fg-highlight-light',
				'choices' => apply_filters( 'foogallery_gallery_template_lightbox_custom_button_highlight_choices', array(
					'fg-highlight-light'   => array( 'label' => __( 'Light',   'foogallery' ), 'html' => '<div class="foogallery-setting-panel_theme fg-light"></div>' ),
					'fg-highlight-dark'    => array( 'label' => __( 'Dark',    'foogallery' ), 'html' => '<div class="foogallery-setting-panel_theme fg-dark"></div>' ),
					'fg-highlight-blue'    => array( 'label' => __( 'Blue',    'foogallery' ), 'html' => '<div class="foogallery-setting-panel_theme fg-blue"></div>' ),
					'fg-highlight-purple'  => array( 'label' => __( 'Purple',  'foogallery' ), 'html' => '<div class="foogallery-setting-panel_theme fg-purple"></div>' ),
					'fg-highlight-green'   => array( 'label' => __( 'Green',   'foogallery' ), 'html' => '<div class="foogallery-setting-panel_theme fg-green"></div>' ),
					'fg-highlight-red'     => array( 'label' => __( 'Red',     'foogallery' ), 'html' => '<div class="foogallery-setting-panel_theme fg-red"></div>' ),
					'fg-highlight-orange'  => array( 'label' => __( 'Orange',  'foogallery' ), 'html' => '<div class="foogallery-setting-panel_theme fg-orange"></div>' ),
				) ),
				'row_data'=> array(
					'data-foogallery-change-selector'          => 'input',
					'data-foogallery-preview'                  => 'shortcode',
					'data-foogallery-value-selector'           => 'input:checked',
					'data-foogallery-hidden'                   => true,
					'data-foogallery-show-when-field'          => 'lightbox_button_highlight',
					'data-foogallery-show-when-field-operator' => '===',
					'data-foogallery-show-when-field-value'    => 'custom',
				)
			);

            $hide_thumbs_by_default = false;
            //only hide the thumbs by default if we are using the Grid PRO template
            if ( $template && array_key_exists('slug', $template) && $template['slug'] === 'foogridpro' ) {
                $hide_thumbs_by_default = true;
            }

			$field[] = array(
				'id'       => 'lightbox_thumbs',
				'title'    => __( 'Thumbnail Strip', 'foogallery' ),
				'desc'     => __( 'You can enable or disable thumbnails within the lightbox.', 'foogallery' ),
				'section'  => $section,
				'spacer'   => '<span class="spacer"></span>',
				'type'     => 'radio',
				'default'  => $hide_thumbs_by_default ? 'none' : 'bottom',
				'choices'  => apply_filters( 'foogallery_gallery_template_lightbox_thumbs_choices', array(
					'bottom' => __( 'Bottom', 'foogallery' ),
					'top'    => __( 'Top', 'foogallery' ),
					'left'   => __( 'Left', 'foogallery' ),
					'right'  => __( 'Right', 'foogallery' ),
					'none'  => __( 'Hidden', 'foogallery' ),
				) ),
				'row_data' => array(
					'data-foogallery-change-selector' => 'input',
					'data-foogallery-preview'         => 'shortcode',
					'data-foogallery-value-selector'  => 'input:checked',
				)
			);

			$field[] = array(
				'id'      => 'lightbox_thumbs_captions',
				'title'   => __( 'Thumbnail Strip Captions', 'foogallery' ),
				'desc'    => __( 'Whether or not the thumbnail strip should contain captions.', 'foogallery' ),
				'section' => $section,
				'spacer'  => '<span class="spacer"></span>',
				'type'    => 'radio',
				'default' => 'no',
				'choices' => apply_filters( 'foogallery_gallery_template_lightbox_thumbs_captions_choices', array(
					'yes' => __( 'Show Captions', 'foogallery' ),
					'no'  => __( 'No Captions', 'foogallery' ),
				) ),
				'row_data'=> array(
					'data-foogallery-hidden'                   => true,
					'data-foogallery-show-when-field'          => 'lightbox_thumbs',
					'data-foogallery-show-when-field-operator' => '!==',
					'data-foogallery-show-when-field-value'    => 'none',
					'data-foogallery-change-selector'          => 'input:radio',
					'data-foogallery-preview'                  => 'shortcode',
					'data-foogallery-value-selector'           => 'input:checked',
				)
			);

			$field[] = array(
				'id'      => 'lightbox_transition',
				'title'   => __( 'Transition', 'foogallery' ),
				'desc'    => __( 'The transition to apply to the main content area when switching between items.', 'foogallery' ),
				'section' => $section,
				'spacer'  => '<span class="spacer"></span>',
				'type'    => 'radio',
				'default' => 'fade',
				'choices' => apply_filters( 'foogallery_gallery_template_lightbox_button_highlight_choices', array(
					'fade'       => __( 'Fade', 'foogallery' ),
					'horizontal' => __( 'Horizontal', 'foogallery' ),
					'vertical'   => __( 'Vertical', 'foogallery' ),
					'none'       => __( 'None', 'foogallery' )
				) ),
				'row_data'=> array(
					'data-foogallery-change-selector' => 'input',
					'data-foogallery-preview' => 'shortcode',
					'data-foogallery-value-selector' => 'input:checked',
				)
			);

			$field[] = array(
				'id'      => 'lightbox_info_position',
				'title'   => __( 'Caption Position', 'foogallery' ),
				'desc'    => __( 'The position of the captions within the lightbox.', 'foogallery' ),
				'section' => $section,
				'spacer'  => '<span class="spacer"></span>',
				'type'    => 'radio',
				'default' => 'bottom',
				'choices' => apply_filters( 'foogallery_gallery_template_lightbox_info_position_choices', array(
					'bottom' => __( 'Bottom', 'foogallery' ),
					'top'    => __( 'Top', 'foogallery' ),
					'left'   => __( 'Left', 'foogallery' ),
					'right'  => __( 'Right', 'foogallery' ),
					'none'  => __( 'Hidden', 'foogallery' ),
				) ),
				'row_data'=> array(
					'data-foogallery-change-selector' => 'input:radio',
					'data-foogallery-preview'         => 'shortcode',
					'data-foogallery-value-selector'  => 'input:checked'
				)
			);

			$field[] = array(
				'id'      => 'lightbox_info_overlay',
				'title'   => __( 'Caption Display', 'foogallery' ),
				'desc'    => __( 'Whether or not the caption is overlaid on top of the content, or is inline (outside of the content).', 'foogallery' ),
				'section' => $section,
				'spacer'  => '<span class="spacer"></span>',
				'type'    => 'radio',
				'default' => 'yes',
				'choices' => apply_filters( 'foogallery_gallery_template_lightbox_info_choices', array(
					'yes' => __( 'Overlaid', 'foogallery' ),
					'no'  => __( 'Inline', 'foogallery' ),
				) ),
				'row_data'=> array(
					'data-foogallery-hidden'                   => true,
					'data-foogallery-show-when-field'          => 'lightbox_info_position',
					'data-foogallery-show-when-field-operator' => '!==',
					'data-foogallery-show-when-field-value'    => 'none',
					'data-foogallery-change-selector'          => 'input:radio',
					'data-foogallery-preview'                  => 'shortcode',
					'data-foogallery-value-selector'           => 'input:checked',
				)
			);

			$field[] = array(
				'id'      => 'lightbox_auto_progress',
				'title'   => __( 'Auto Progress', 'foogallery' ),
				'desc'    => __( 'Auto progress to the next item after a specified time.', 'foogallery' ),
				'section' => $section,
				'spacer'  => '<span class="spacer"></span>',
				'type'    => 'radio',
				'default' => 'no',
				'choices' => apply_filters( 'foogallery_gallery_template_lightbox_auto_progress_choices', array(
					'yes' => __( 'Yes', 'foogallery' ),
					'no'  => __( 'No', 'foogallery' ),
				) ),
				'row_data'=> array(
					'data-foogallery-change-selector'          => 'input:radio',
					'data-foogallery-preview'                  => 'shortcode',
					'data-foogallery-value-selector'           => 'input:checked',
				)
			);

			$field[] = array(
				'id'      => 'lightbox_auto_progress_seconds',
				'title'   => __( 'Auto Progress Seconds', 'foogallery' ),
				'desc'    => __( 'The time in seconds to display content before auto progressing to the next item.', 'foogallery' ),
				'section' => $section,
				'type'    => 'number',
				'default' => '10',
				'row_data'=> array(
					'data-foogallery-hidden'                   => true,
					'data-foogallery-show-when-field'          => 'lightbox_auto_progress',
					'data-foogallery-show-when-field-operator' => '===',
					'data-foogallery-show-when-field-value'    => 'yes',
					'data-foogallery-change-selector'          => 'input:radio',
					'data-foogallery-preview'                  => 'shortcode',
					'data-foogallery-value-selector'           => 'input:checked',
				)
			);

			$field[] = array(
				'id'      => 'lightbox_fit_media',
				'title'   => __( 'Fit Media', 'foogallery' ),
				'desc'    => __( 'Whether or not to force images to fill the content area. Aspect ratios are maintained, the image is simply scaled so it covers the entire available area.', 'foogallery' ),
				'section' => $section,
				'spacer'  => '<span class="spacer"></span>',
				'type'    => 'radio',
				'default' => 'no',
				'choices' => apply_filters( 'foogallery_gallery_template_lightbox_fit_media_choices', array(
					'yes' => __( 'Yes', 'foogallery' ),
					'no'  => __( 'No', 'foogallery' ),
				) ),
				'row_data'=> array(
					'data-foogallery-change-selector'          => 'input:radio',
					'data-foogallery-preview'                  => 'shortcode',
					'data-foogallery-value-selector'           => 'input:checked',
				)
			);

			$field[] = array(
				'id'      => 'lightbox_no_scrollbars',
				'title'   => __( 'Scroll Bars', 'foogallery' ),
				'desc'    => __( 'Whether or not to hide the page scrollbars when maximizing.', 'foogallery' ),
				'section' => $section,
				'spacer'  => '<span class="spacer"></span>',
				'type'    => 'radio',
				'default' => 'no',
				'choices' => apply_filters( 'foogallery_gallery_template_lightbox_no_scrollbars_choices', array(
					'yes' => __( 'Default', 'foogallery' ),
					'no'  => __( 'Hidden', 'foogallery' ),
				) ),
				'row_data'=> array(
					'data-foogallery-change-selector'          => 'input:radio',
					'data-foogallery-preview'                  => 'shortcode',
					'data-foogallery-value-selector'           => 'input:checked',
				)
			);

			$field[] = array(
				'id'      => 'lightbox_buttons_display',
				'title'   => __( 'Controls Display', 'foogallery' ),
				'desc'    => __( 'Whether or not the control buttons are overlaid on top of the content, or are inline (outside of the content).', 'foogallery' ),
				'section' => $section,
				'spacer'  => '<span class="spacer"></span>',
				'type'    => 'radio',
				'default' => 'no',
				'choices' => apply_filters( 'foogallery_gallery_template_lightbox_buttons_display', array(
					'yes' => __( 'Overlaid', 'foogallery' ),
					'no'  => __( 'Inline', 'foogallery' ),
				) ),
				'row_data'=> array(
					'data-foogallery-change-selector' => 'input:radio',
					'data-foogallery-preview'         => 'shortcode',
					'data-foogallery-value-selector'  => 'input:checked',
				)
			);

			$field[] = array(
				'id'      => 'lightbox_hover_buttons',
				'title'   => __( 'Show Controls On Hover', 'foogallery' ),
				'desc'    => __( 'Only show the control buttons when you hover the mouse over.', 'foogallery' ),
				'section' => $section,
				'spacer'  => '<span class="spacer"></span>',
				'type'    => 'radio',
				'default' => 'no',
				'choices' => apply_filters( 'foogallery_gallery_template_lightbox_hover_buttons_choices', array(
					'yes' => __( 'Yes', 'foogallery' ),
					'no'  => __( 'No', 'foogallery' ),
				) ),
				'row_data'=> array(
					'data-foogallery-change-selector'          => 'input:radio',
					'data-foogallery-preview'                  => 'shortcode',
					'data-foogallery-value-selector'           => 'input:checked',
				)
			);

			//find the index of the first Hover Effect field
			$index = $this->find_index_of_section( $fields, __( 'Hover Effects', 'foogallery' ) );

			array_splice( $fields, $index, 0, $field );

			return $fields;
		}

		/**
		 * Return the index of the requested section
		 *
		 * @param $fields
		 * @param $section
		 *
		 * @return int
		 */
		private function find_index_of_section( $fields, $section ) {
			$index = 0;
			foreach ( $fields as $field ) {
				if ( isset( $field['section'] ) && $section === $field['section'] ) {
					return $index;
				}
				$index++;
			}
			return $index;
		}

		/**
		 * Add the required data options if needed
		 *
		 * @param $options
		 * @param $gallery    FooGallery
		 *
		 * @param $attributes array
		 *
		 * @return array
		 */
		function add_data_options($options, $gallery, $attributes) {
			$template = foogallery_get_gallery_template( $gallery->gallery_template );
			if ( $template && array_key_exists( 'panel_support', $template ) && true === $template['panel_support'] ) {

				$options['template'] = $this->get_options_from_settings();

			}

			return $options;
		}

		/**
		 * Build up a arguments used in the preview of the gallery
		 * @param $args
		 * @param $post_data
		 *
		 * @return mixed
		 */
		function preview_arguments( $args, $post_data, $template ) {
			if ( array_key_exists( $template . '_lightbox_theme', $post_data[FOOGALLERY_META_SETTINGS] ) ) {
				$args['lightbox_theme'] = $post_data[FOOGALLERY_META_SETTINGS][$template . '_lightbox_theme'];
			}

			if ( array_key_exists( $template . '_lightbox_button_theme', $post_data[FOOGALLERY_META_SETTINGS] ) ) {
				$args['lightbox_button_theme'] = $post_data[FOOGALLERY_META_SETTINGS][$template . '_lightbox_button_theme'];
			}

			if ( array_key_exists( $template . '_lightbox_custom_button_theme', $post_data[FOOGALLERY_META_SETTINGS] ) ) {
				$args['lightbox_custom_button_theme'] = $post_data[FOOGALLERY_META_SETTINGS][$template . '_lightbox_custom_button_theme'];
			}

			if ( array_key_exists( $template . '_lightbox_button_highlight', $post_data[FOOGALLERY_META_SETTINGS] ) ) {
				$args['lightbox_button_highlight'] = $post_data[FOOGALLERY_META_SETTINGS][$template . '_lightbox_button_highlight'];
			}

			if ( array_key_exists( $template . '_lightbox_custom_button_highlight', $post_data[FOOGALLERY_META_SETTINGS] ) ) {
				$args['lightbox_custom_button_highlight'] = $post_data[FOOGALLERY_META_SETTINGS][$template . '_lightbox_custom_button_highlight'];
			}

			if ( array_key_exists( $template . '_lightbox_thumbs', $post_data[FOOGALLERY_META_SETTINGS] ) ) {
				$args['lightbox_thumbs'] = $post_data[FOOGALLERY_META_SETTINGS][$template . '_lightbox_thumbs'];
			}

			if ( array_key_exists( $template . '_lightbox_thumbs_captions', $post_data[FOOGALLERY_META_SETTINGS] ) ) {
				$args['lightbox_thumbs_captions'] = $post_data[FOOGALLERY_META_SETTINGS][$template . '_lightbox_thumbs_captions'];
			}

			if ( array_key_exists( $template . '_lightbox_info_position', $post_data[FOOGALLERY_META_SETTINGS] ) ) {
				$args['lightbox_info_position'] = $post_data[FOOGALLERY_META_SETTINGS][$template . '_lightbox_info_position'];
			}

			if ( array_key_exists( $template . '_lightbox_info_overlay', $post_data[FOOGALLERY_META_SETTINGS] ) ) {
				$args['lightbox_info_overlay'] = $post_data[FOOGALLERY_META_SETTINGS][$template . '_lightbox_info_overlay'];
			}

			if ( array_key_exists( $template . '_lightbox_hover_buttons', $post_data[FOOGALLERY_META_SETTINGS] ) ) {
				$args['lightbox_hover_buttons'] = $post_data[FOOGALLERY_META_SETTINGS][$template . '_lightbox_hover_buttons'];
			}

			if ( array_key_exists( $template . '_lightbox_transition', $post_data[FOOGALLERY_META_SETTINGS] ) ) {
				$args['lightbox_transition'] = $post_data[FOOGALLERY_META_SETTINGS][$template . '_lightbox_transition'];
			}

			if ( array_key_exists( $template . '_lightbox_auto_progress', $post_data[FOOGALLERY_META_SETTINGS] ) ) {
				$args['lightbox_auto_progress'] = $post_data[FOOGALLERY_META_SETTINGS][$template . '_lightbox_auto_progress'];
			}

			if ( array_key_exists( $template . '_lightbox_auto_progress_seconds', $post_data[FOOGALLERY_META_SETTINGS] ) ) {
				$args['lightbox_auto_progress_seconds'] = $post_data[FOOGALLERY_META_SETTINGS][$template . '_lightbox_auto_progress_seconds'];
			}

			if ( array_key_exists( $template . '_lightbox_fit_media', $post_data[FOOGALLERY_META_SETTINGS] ) ) {
				$args['lightbox_fit_media'] = $post_data[FOOGALLERY_META_SETTINGS][$template . '_lightbox_fit_media'];
			}

			if ( array_key_exists( $template . '_lightbox_no_scrollbars', $post_data[FOOGALLERY_META_SETTINGS] ) ) {
				$args['lightbox_no_scrollbars'] = $post_data[FOOGALLERY_META_SETTINGS][$template . '_lightbox_no_scrollbars'];
			}

			if ( array_key_exists( $template . '_lightbox_buttons_display', $post_data[FOOGALLERY_META_SETTINGS] ) ) {
				$args['lightbox_buttons_display'] = $post_data[FOOGALLERY_META_SETTINGS][$template . '_lightbox_buttons_display'];
			}

			return $args;
		}

		/**
		 * Returns the Dashicon that can be used in the settings tabs
		 *
		 * @param $section_slug
		 *
		 * @return string
		 */
		function add_section_icons( $section_slug ) {
			if ( 'lightbox' === $section_slug || 'panel' === $section_slug ) {
				return 'dashicons-grid-view';
			}

			return $section_slug;
		}

		/**
		 * Add the FooGallery PRO lightbox
		 * @param $lightboxes
		 *
		 * @return mixed
		 */
		function add_lightbox($lightboxes) {
			$lightboxes['foogallery'] = __( 'FooGallery PRO Lightbox', 'foogallery' );
			return $lightboxes;
		}

		/**
		 * Change the default for lightbox
		 *
		 * @param $field
		 * @param $gallery_template
		 * @return mixed
		 */
		function make_foogallery_default_lightbox( $field, $gallery_template ) {
			if (isset($field['lightbox']) && true === $field['lightbox']) {
				$field['default'] = 'foogallery';
			}

			return $field;
		}

		/**
		 * Append the needed data attributes to the container div for the lightbox settings
		 *
		 * @param $attributes
		 * @param $gallery
		 *
		 * @return array
		 */
		function add_lightbox_data_attributes( $attributes, $gallery ) {
			$template = foogallery_get_gallery_template( $gallery->gallery_template );
			if ( $template && !array_key_exists( 'panel_support', $template ) ) {

				$options = $this->get_options_from_settings();

				if ( defined( 'JSON_UNESCAPED_UNICODE' ) ) {
					$encoded_options = json_encode( $options, JSON_UNESCAPED_UNICODE );
				} else {
					$encoded_options = json_encode( $options );
				}

				//check if lightbox set to foogallery
                $lightbox = foogallery_gallery_template_setting( 'lightbox', '' );

				if ( 'foogallery' === $lightbox ) {
                    $attributes['data-foogallery-lightbox'] = $encoded_options;
                }
			}

			return $attributes;
		}

		/**
		 * return the options for the lightbox/panel
		 *
		 * @return array
		 */
		private function get_options_from_settings() {
            global $current_foogallery_template;
			$options = array();

            $hide_thumbs_by_default = false;
            //only hide the thumbs by default if we are using the Grid PRO template
            if ( $current_foogallery_template && 'foogridpro' === $current_foogallery_template ) {
                $hide_thumbs_by_default = true;
            }

			$theme = foogallery_gallery_template_setting( 'lightbox_theme', '' );
			if ( !empty( $theme ) ) {
				$options['theme'] = $theme;
			}

			$button_theme = foogallery_gallery_template_setting( 'lightbox_button_theme', '' );
			if ( !empty( $button_theme ) && 'custom' === $button_theme ) {
				$button_theme = foogallery_gallery_template_setting( 'lightbox_custom_button_theme', 'fg-button-light' );
				$options['button'] = $button_theme;
			}

			$button_highlight = foogallery_gallery_template_setting( 'lightbox_button_highlight', '' );
			if ( !empty( $button_highlight ) && 'custom' === $button_highlight ) {
				$button_highlight = foogallery_gallery_template_setting( 'lightbox_custom_button_highlight', 'fg-highlight-light' );
				$options['highlight'] = $button_highlight;
			}

			$thumbs = foogallery_gallery_template_setting( 'lightbox_thumbs', $hide_thumbs_by_default ? 'none' : 'bottom' );
			$options['thumbs'] = $thumbs;
			if ( 'none' !== $thumbs ) {
				$options['thumbsCaptions'] = foogallery_gallery_template_setting( 'lightbox_thumbs_captions', 'no' ) === 'yes';
			}

			$info_position = foogallery_gallery_template_setting( 'lightbox_info_position', 'bottom' );
			$options['info'] = $info_position;
			if ( 'none' !== $info_position ) {
				$options['infoVisible'] = true;
				$options['infoOverlay'] = foogallery_gallery_template_setting( 'lightbox_info_overlay', 'yes' ) === 'yes';
			}

			$options['transition'] = foogallery_gallery_template_setting( 'lightbox_transition', 'fade' );

			$auto_progress = foogallery_gallery_template_setting( 'lightbox_auto_progress', 'no' ) === 'yes';
			if ( $auto_progress ) {
				$options['autoProgress'] = intval( foogallery_gallery_template_setting( 'lightbox_auto_progress_seconds', '10' ) );
			}

			$options['hoverButtons'] = foogallery_gallery_template_setting( 'lightbox_hover_buttons', 'no' ) === 'yes';
			$options['fitMedia'] = foogallery_gallery_template_setting( 'lightbox_fit_media', 'no' ) === 'yes';
			$options['noScrollbars'] = foogallery_gallery_template_setting( 'lightbox_no_scrollbars', 'no' ) !== 'yes';
			$options['preserveButtonSpace'] = foogallery_gallery_template_setting( 'lightbox_buttons_display', 'no' ) === 'no';

			return $options;
		}
	}
}