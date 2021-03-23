<?php

/**
 * WooCommerce Box Packer
 *
 * @version 1.0.2
 * @author WooThemes / Mike Jolley
 */
class WC_Boxpack {

	/**
	 * @since 1.0.2
	 */
	const VERSION = '1.0.2';

	private $boxes;
	private $items;
	private $packages;
	private $cannot_pack;

	/**
	 * @var bool Try to pack into envelopes and packets
	 */
	private $prefer_packets = false;

	/**
	 * __construct function.
	 *
	 * @since 1.0.2 Added `$options` parameter and '$prefer_packets' option.
	 *
	 * @param array $options {
	 *     Optional. An array of options.
	 *     @type bool $prefer_packets Pack into packets before boxes.
	 * }
	 * @return void
	 */
	public function __construct( array $options = array() ) {
		if ( isset( $options['prefer_packets'] ) ) {
			$this->prefer_packets = $options['prefer_packets'];
		}
		include_once( 'class-wc-boxpack-box.php' );
		include_once( 'class-wc-boxpack-item.php' );
	}

	/**
	 * clear_items function.
	 *
	 * @access public
	 * @return void
	 */
	public function clear_items() {
		$this->items = array();
	}

	/**
	 * clear_boxes function.
	 *
	 * @access public
	 * @return void
	 */
	public function clear_boxes() {
		$this->boxes = array();
	}

	/**
	 * add_item function.
	 *
	 * @access public
	 * @return void
	 */
	public function add_item( $length, $width, $height, $weight, $value = '', $meta = array() ) {
		$this->items[] = new WC_Boxpack_Item( $length, $width, $height, $weight, $value, $meta );
	}

	/**
	 * add_box function.
	 *
	 * @since 1.0.2 Add `$max_weight` and `$type` optional parameters.
	 *
	 * @param mixed $length
	 * @param mixed $width
	 * @param mixed $height
	 * @param mixed $weight
	 * @param float $max_weight
	 * @param string $type
	 *
	 * @return WC_Boxpack_Box
	 */
	public function add_box( $length, $width, $height, $weight = 0, $max_weight = 0.0, $type = '' ) {
		$new_box       = new WC_Boxpack_Box( $length, $width, $height, $weight, $max_weight, $type );
		$this->boxes[] = $new_box;
		return $new_box;
	}

	/**
	 * get_packages function.
	 *
	 * @access public
	 * @return array
	 */
	public function get_packages() {
		return $this->packages ? $this->packages : array();
	}

	/**
	 * pack function.
	 *
	 * @access public
	 * @return void
	 */
	public function pack() {
		try {
			// We need items
			if ( sizeof( $this->items ) == 0 ) {
				throw new Exception( 'No items to pack!' );
			}

			// Clear packages
			$this->packages = array();

			// Order the boxes by volume
			$this->boxes = $this->order_boxes( $this->boxes );

			if ( ! $this->boxes ) {
				$this->cannot_pack = $this->items;
				$this->items       = array();
			}

			// Keep looping until packed
			while ( sizeof( $this->items ) > 0 ) {
				$this->items       = $this->order_items( $this->items );
				$possible_packages = array();
				$best_package      = '';

				// Attempt to pack all items in each box
				foreach ( $this->boxes as $box ) {
					$possible_packages[] = $box->pack( $this->items );
				}

				// Find the best success rate
				$best_percent = 0;

				foreach ( $possible_packages as $package ) {
					if ( $package->percent > $best_percent ) {
						$best_percent = $package->percent;
					}
				}

				if ( $best_percent == 0 ) {
					$this->cannot_pack = $this->items;
					$this->items       = array();
				} else {
					// Get smallest box with best_percent
					$possible_packages = array_reverse( $possible_packages );

					foreach ( $possible_packages as $package ) {
						if ( $package->percent == $best_percent ) {
							$best_package = $package;
							break; // Done packing
						}
					}

					// Update items array
					$this->items = $best_package->unpacked;

					// Store package
					$this->packages[] = $best_package;
				}
			}

			// Items we cannot pack (by now) get packaged individually
			if ( $this->cannot_pack ) {
				foreach ( $this->cannot_pack as $item ) {
					$package           = new stdClass();
					$package->id       = '';
					$package->type     = 'box';
					$package->weight   = $item->get_weight();
					$package->length   = $item->get_length();
					$package->width    = $item->get_width();
					$package->height   = $item->get_height();
					$package->value    = $item->get_value();
					$package->unpacked = true;
					$this->packages[]  = $package;
				}
			}
		} catch ( Exception $e ) {

			// Display a packing error for admins
			if ( current_user_can( 'manage_options' ) ) {
				echo 'Packing error: ',  $e->getMessage(), "\n";
			}
		}
	}

	/**
	 * Order boxes by weight and volume
	 * $param array $sort
	 * @return array
	 */
	private function order_boxes( $sort ) {
		if ( ! empty( $sort ) ) {
			uasort( $sort, array( $this, 'box_sorting' ) );
		}
		return $sort;
	}

	/**
	 * Order items by weight and volume
	 * $param array $sort
	 * @return array
	 */
	private function order_items( $sort ) {
		if ( ! empty( $sort ) ) {
			uasort( $sort, array( $this, 'item_sorting' ) );
		}
		return $sort;
	}

	/**
	 * item_sorting function.
	 *
	 * @access public
	 * @param mixed $a
	 * @param mixed $b
	 * @return int
	 */
	public function item_sorting( $a, $b ) {
		if ( $a->get_volume() == $b->get_volume() ) {
			if ( $a->get_weight() == $b->get_weight() ) {
				return 0;
			}
			return ( $a->get_weight() < $b->get_weight() ) ? 1 : -1;
		}
		return ( $a->get_volume() < $b->get_volume() ) ? 1 : -1;
	}

	/**
	 * box_sorting function.
	 *
	 * @access public
	 * @param mixed $a
	 * @param mixed $b
	 * @return int
	 */
	public function box_sorting( $a, $b ) {

		if ( $this->prefer_packets ) {
			// check 'envelope', 'packet' first as they are cheaper even if their volume is more
			$a_cheaper_packaging = in_array( $a->get_type(), array( 'envelope', 'packet' ) );
			$b_cheaper_packaging = in_array( $b->get_type(), array( 'envelope', 'packet' ) );

			if ( $a_cheaper_packaging && ! $b_cheaper_packaging ) {
				return 1;
			}

			if ( $b_cheaper_packaging && ! $a_cheaper_packaging ) {
				return - 1;
			}
		}

		if ( $a->get_volume() == $b->get_volume() ) {
			if ( $a->get_max_weight() == $b->get_max_weight() ) {
				return 0;
			}
			return ( $a->get_max_weight() < $b->get_max_weight() ) ? 1 : -1;
		}
		return ( $a->get_volume() < $b->get_volume() ) ? 1 : -1;
	}
}
