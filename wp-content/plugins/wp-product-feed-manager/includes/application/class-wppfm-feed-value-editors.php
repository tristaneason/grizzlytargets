<?php

/**
 * WP Product Feed Value Editors Class.
 *
 * @package WP Product Feed Manager/Application/Classes
 * @version 1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'WPPFM_Feed_Value_Editors' ) ) :

	/**
	 * Feed Value Editors Class
	 */
	class WPPFM_Feed_Value_Editors {

		public function overwrite_value( $condition ) {
			return $condition[2];
		}

		public function replace_value( $condition, $current_value ) {
			return str_replace( $condition[2], $condition[3], $current_value );
		}

		public function convert_to_element( $element_name, $current_value ) {
			return "!sub:$element_name[2]|$current_value";
		}

		public function remove_value( $condition, $current_value ) {
			return str_replace( $condition[2], '', $current_value );
		}

		public function add_prefix_value( $condition, $current_value ) {
			return $condition[2] . $current_value;
		}

		public function add_suffix_value( $condition, $current_value ) {
			return $current_value . $condition[2];
		}

		public function recalculate_value( $condition, $current_value, $combination_string, $combined_data_elements, $feed_language ) {
			if ( ! $combination_string ) {
				$values           = $this->make_recalculate_inputs( $current_value, $condition[3] );
				$calculated_value = $this->recalculate( $condition[2], floatval( $values['main_val'] ), floatval( $values['sub_val'] ) );

				return $this->is_money_value( $current_value ) ? wppfm_prep_money_values( $calculated_value, $feed_language ) : $calculated_value;

			} else {
				if ( count( $combined_data_elements ) > 1 ) {
					$combined_string_values = array();

					foreach ( $combined_data_elements as $element ) {
						$values = $this->make_recalculate_inputs( $element, $condition[3] );

						$reg_match = '/[0-9.,]/'; // only numbers and decimals

						$calculated_value = preg_match( $reg_match, $values['main_val'] ) && preg_match( $reg_match, $values['sub_val'] ) ?
							$this->recalculate( $condition[2], floatval( $values['main_val'] ), floatval( $values['sub_val'] ) ) : $values['main_val'];

						$end_value = $this->is_money_value( $element ) ? wppfm_prep_money_values( $calculated_value, $feed_language ) : $calculated_value;

						array_push( $combined_string_values, $end_value );
					}

					return $this->make_combined_result_string( $combined_string_values, $combination_string );
				} else {
					return '';
				}
			}
		}

		private function make_combined_result_string( $values, $combination_string ) {
			$separators    = $this->combination_separators();
			$result_string = $values[0];

			$combinations = explode( '|', $combination_string );

			for ( $i = 1; $i < count( $combinations ); $i ++ ) {
				$sep            = explode( '#', $combinations[ $i ] );
				$result_string .= $separators[ (int) $sep[0] ];
				$result_string .= $values[ $i ];
			}

			return $result_string;
		}

		public function combination_separators() {
			return array(
				'',
				' ',
				', ',
				'.',
				'; ',
				':',
				'-',
				'/',
				'\\',
				'||',
				'_',
			); // should correspond with wppfm_getCombinedSeparatorList()
		}

		private function make_recalculate_inputs( $current_value, $current_sub_value ) {
			if ( ! preg_match( '/[a-zA-Z]/', $current_value ) ) { // only remove the commas if the current value has no letters
				$main_value = wppfm_number_format_parse( $current_value );
			} else {
				$main_value = $current_value;
			}

			$sub_value = wppfm_number_format_parse( $current_sub_value );

			return array(
				'main_val' => $main_value,
				'sub_val'  => $sub_value,
			);
		}

		public function prep_meta_values( $meta_data, $feed_language ) {
			$result = $meta_data->meta_value;

			if ( wppfm_meta_key_is_money( $meta_data->meta_key ) ) {
				$result = wppfm_prep_money_values( $result, $feed_language );
			}

			return is_string( $result ) ? trim( $result ) : $result;
		}

		/**
		 * Checks is a certain value could be a money value or not
		 *
		 * @param int or string $value
		 *
		 * @return boolean true if it is a money value
		 */
		public function is_money_value( $value ) {
			// replace a comma separator with a period so it can be recognized as numeric
			$possible_number = wppfm_number_format_parse( $value );

			// if its not a number it cannot be a money value
			if ( ! is_numeric( $possible_number ) ) {
				return false;
			}

			$last_pos     = strrpos( (string) $value, get_option( 'woocommerce_price_decimal_sep' ) );
			$value_length = strlen( (string) $value );

			$actual_decimals = $value_length - $last_pos - 1;

			return absint( get_option( 'woocommerce_price_num_decimals', 2 ) ) === $actual_decimals ? true : false;
		}

		private function recalculate( $math, $main_value, $sub_value ) {
			$result = 0;

			if ( is_numeric( $main_value ) && is_numeric( $sub_value ) ) {
				switch ( $math ) {
					case 'add':
						$result = $main_value + $sub_value;
						break;

					case 'subtract':
						$result = $main_value - $sub_value;
						break;

					case 'multiply':
						$result = $main_value * $sub_value;
						break;

					case 'divide':
						$result = 0 !== $sub_value ? $main_value / $sub_value : 0;
						break;
				}
			}

			return $result;
		}
	}


	// End of WPPFM_Feed_Value_Editors class

endif;
