<?php

/**
 * WooCommerce Box Packer
 */
class PH_FedEx_Boxpack {

	private $boxes;
	private $items;
	private $packages;
	private $cannot_pack;
	private $mode='volume_based';
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct($mode=null) {
	$this->mode=$mode;
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
		$this->items[] = new PH_FedEx_Boxpack_Item( $length, $width, $height, $weight, $value, $meta );
	}

	/**
	 * add_box function.
	 *
	 * @access public
	 * @param mixed $length
	 * @param mixed $width
	 * @param mixed $height
	 * @param mixed $weight
	 * @return void
	 */
	public function add_box( $length, $width, $height, $weight = 0, $packtype = '' ) {
		$new_box = new PH_FedEx_Boxpack_Box( $length, $width, $height, $packtype, $weight );
		$this->boxes[] = $new_box;
		return $new_box;
	}

	/**
	 * get_packages function.
	 *
	 * @access public
	 * @return void
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
			if ( empty($this->items) || sizeof( $this->items ) == 0 ) {
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
					$possible_packages[] = $box->pack( $this->items ,$this->mode );
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
					$package->weight   = $item->get_weight();
					$package->length   = $item->get_length();
					$package->width    = $item->get_width();
					$package->height   = $item->get_height();
					$package->value    = $item->get_value();
                    $package->packtype = '';
					$package->unpacked = true;
					$this->packages[]  = $package;
				}
			}

		} catch (Exception $e) {
			//echo 'Packing error: ',  $e->getMessage(), "\n";
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
	 * order_by_volume function.
	 *
	 * @access private
	 * @return void
	 */
	private function order_by_volume( $sort ) {
		if ( ! empty( $sort ) ) {
			uasort( $sort, array( $this, 'volume_based_sorting' ) );
		}
		return $sort;
	}

	/**
	 * item_sorting function.
	 *
	 * @access public
	 * @param mixed $a
	 * @param mixed $b
	 * @return void
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
	 * @return void
	 */
	public function box_sorting( $a, $b ) {
		if ( $a->get_volume() == $b->get_volume() ) {
	        if ( $a->get_max_weight() == $b->get_max_weight() ) {
		        return 0;
		    }
		    return ( $a->get_max_weight() < $b->get_max_weight() ) ? 1 : -1;
	    }
	    return ( $a->get_volume() < $b->get_volume() ) ? 1 : -1;
	}

	/**
	 * volume_based_sorting function.
	 *
	 * @access public
	 * @param mixed $a
	 * @param mixed $b
	 * @return void
	 */
	public function volume_based_sorting( $a, $b ) {
		if ( $a->get_volume() == $b->get_volume() ) {
	        return 0;
	    }
	    return ( $a->get_volume() < $b->get_volume() ) ? 1 : -1;
	}

}

/**
 * PH_FedEx_Boxpack_Box class.
 */
class PH_FedEx_Boxpack_Box {

	/** @var string ID of the box - given to packages */
	private $id = '';

	/** @var float Weight of the box itself */
	private $weight;

	/** @var float Max allowed weight of box + contents */
	private $max_weight = 0;

	/** @var float Outer dimension of box sent to shipper */
	private $outer_height;

	/** @var float Outer dimension of box sent to shipper */
	private $outer_width;

	/** @var float Outer dimension of box sent to shipper */
	private $outer_length;

	/** @var float Inner dimension of box used when packing */
	private $height;

	/** @var float Inner dimension of box used when packing */
	private $width;

	/** @var float Inner dimension of box used when packing */
	private $length;

	/** @var float Dimension is stored here if adjusted during packing */
	private $packed_height;
	private $maybe_packed_height = null;

	/** @var float Dimension is stored here if adjusted during packing */
	private $packed_width;
	private $maybe_packed_width = null;

	/** @var float Dimension is stored here if adjusted during packing */
	private $packed_length;
	private $maybe_packed_length = null;

	/** @var float Volume of the box */
	private $volume;

	/** @var Array Valid box types which affect packing */
	private $valid_types = array( 'box', 'tube', 'envelope', 'packet' );

	/** @var string This box type */
	private $type = 'box';

	/** @var string This box pack type */
	private $packtype;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct( $length, $width, $height, $packtype, $weight = 0 ) {
		$dimensions = array( $length, $width, $height );

		sort( $dimensions );

		$this->outer_length = $this->length = $dimensions[2];
		$this->outer_width  = $this->width  = $dimensions[1];
		$this->outer_height = $this->height = $dimensions[0];
		$this->weight       = $weight;
        $this->packtype     = $packtype;
	}

	/**
	 * set_id function.
	 *
	 * @access public
	 * @param mixed $weight
	 * @return void
	 */
	public function set_id( $id ) {
		$this->id = $id;
	}

	/**
	 * Set the volume to a specific value, instead of calculating it.
	 * @param float $volume
	 */
	public function set_volume( $volume ) {
		$this->volume = floatval( $volume );
	}

	/**
	 * Set the type of box
	 * @param string $type
	 */
	public function set_type( $type ) {
		if ( in_array( $type, $this->valid_types ) ) {
			$this->type = $type;
		}
	}

	/**
	 * Get max weight.
	 *
	 * @return float
	 */
	public function get_max_weight() {
		return floatval( $this->max_weight );
	}

	/**
	 * set_max_weight function.
	 *
	 * @access public
	 * @param mixed $weight
	 * @return void
	 */
	public function set_max_weight( $weight ) {
		$this->max_weight = $weight;
	}

	/**
	 * set_inner_dimensions function.
	 *
	 * @access public
	 * @param mixed $length
	 * @param mixed $width
	 * @param mixed $height
	 * @return void
	 */
	public function set_inner_dimensions( $length, $width, $height ) {
		$dimensions = array( $length, $width, $height );

		sort( $dimensions );

		$this->length = $dimensions[2];
		$this->width  = $dimensions[1];
		$this->height = $dimensions[0];
	}

	/**
	 * See if an item fits into the box.
	 *
	 * @param object $item
	 * @return bool
	 */
	public function can_fit( $item ) {
		switch ( $this->type ) {
			// Tubes are designed for long thin items so see if the item meets that criteria here.
			case 'tube' :
				$can_fit = ( $this->get_length() >= $item->get_length() && $this->get_width() >= $item->get_width() && $this->get_height() >= $item->get_height() && $item->get_volume() < $this->get_volume() ) ? true : false;
				$can_fit = $can_fit && $item->get_length() >= ( ( $item->get_width() + $this->get_height() ) * 2 );
			break;
			// Packets are flexible
			case 'packet' :
				$can_fit = ( $this->get_packed_length() >= $item->get_length() && $this->get_packed_width() >= $item->get_width() && $item->get_volume() < $this->get_volume() ) ? true : false;

				if ( $can_fit && $item->get_height() > $this->get_packed_height() ) {
					$this->maybe_packed_height = $item->get_height();
					$this->maybe_packed_length = $this->get_packed_length() - ( $this->maybe_packed_height - $this->get_height() );
					$this->maybe_packed_width  = $this->get_packed_width()  - ( $this->maybe_packed_height - $this->get_height() );

					$can_fit = ( $this->maybe_packed_height < $this->maybe_packed_width && $this->maybe_packed_length >= $item->get_length() && $this->maybe_packed_width >= $item->get_width() ) ? true : false;
				}
			break;
			// Boxes are easy
			default :
				$can_fit = ( $this->get_length() >= $item->get_length() && $this->get_width() >= $item->get_width() && $this->get_height() >= $item->get_height() && $item->get_volume() <= $this->get_volume() ) ? true : false;
			break;
		}

		return $can_fit;
	}

	/**
	 * Reset packed dimensions to originals
	 */
	private function reset_packed_dimensions() {
		$this->packed_length = $this->length;
		$this->packed_width  = $this->width;
		$this->packed_height = $this->height;
	}

	/**
	 * pack function.
	 *
	 * @access public
	 * @param mixed $items
	 * @return object Package
	 */
	public function pack( $items ,$mode=null) {
		$packed        = array();
		$unpacked      = array();
		$packed_weight = $this->get_weight();
		$packed_volume = 0;
		$packed_value  = 0;

		$this->reset_packed_dimensions();

		while ( sizeof( $items ) > 0 ) {
			$item = array_shift( $items );

			// Check dimensions
			if ( ! $this->can_fit( $item ) ) {
				$unpacked[] = $item;
				continue;
			}

			// Check max weight
			if ( ( $packed_weight + $item->get_weight() ) > $this->get_max_weight() && $this->get_max_weight() > 0 ) {
				$unpacked[] = $item;
				continue;
			}

			// Check volume
			if ( ( $packed_volume + $item->get_volume() ) > $this->get_volume() ) {
				$unpacked[] = $item;
				continue;
			}

			// Pack
			$packed[]      = $item;
			$packed_volume += $item->get_volume();
			$packed_weight += $item->get_weight();
			$packed_value  += $item->get_value();

			// Adjust dimensions if needed, after this item has been packed inside
			if ( ! is_null( $this->maybe_packed_height ) ) {
				$this->packed_height       = $this->maybe_packed_height;
				$this->packed_length       = $this->maybe_packed_length;
				$this->packed_width        = $this->maybe_packed_width;
				$this->maybe_packed_height = null;
				$this->maybe_packed_length = null;
				$this->maybe_packed_width  = null;
			}
		}

		// Get weight of unpacked items
		$unpacked_weight = 0;
		$unpacked_volume = 0;
		foreach ( $unpacked as $item ) {
			$unpacked_weight += $item->get_weight();
			$unpacked_volume += $item->get_volume();
		}

		$package           = new stdClass();
		$package->id       = $this->id;
		$package->packed   = $packed;
		$package->unpacked = $unpacked;
		$package->weight   = $packed_weight;
		$package->volume   = $packed_volume;
		$package->length   = $this->get_outer_length();
		$package->width    = $this->get_outer_width();
		$package->height   = $this->get_outer_height();
        $package->packtype = $this->get_packtype();
		$package->value    = $packed_value;

		// Calculate packing success % based on % of weight and volume of all items packed
		if($mode=='new_algorithm')
		{	
			$box_volume =$this->packed_length * $this->packed_width * $this->packed_height ;
			$package->percent =(( $packed_volume / $box_volume ) * 100  * sizeof($packed) );
		}
		else
		{
			$package->percent = ( $packed_weight / ( $packed_weight + $unpacked_weight ) ) * ( $packed_volume / ( $packed_volume + $unpacked_volume ) ) * 100;		
		}

		
		

		return $package;
	}

	/**
	 * get_volume function.
	 * @return float
	 */
	public function get_volume() {
		if ( $this->volume ) {
			return $this->volume;
		} else {
			return floatval( $this->get_height() * $this->get_width() * $this->get_length() );
		}
	}

	/**
	 * get_height function.
	 * @return float
	 */
	public function get_height() {
		return $this->height;
	}

    /**
	 * get_packtype function.
	 * @return string
	 */
    public function get_packtype() {
		return $this->packtype;
	}
    /**
	 * set_packtype function.
	 * @return string
	 */
	public function set_packtype( $packtype ) {
		$this->packtype = $packtype;
	}

	/**
	 * get_width function.
	 * @return float
	 */
	public function get_width() {
		return $this->width;
	}

	/**
	 * get_width function.
	 * @return float
	 */
	public function get_length() {
		return $this->length;
	}

	/**
	 * get_weight function.
	 * @return float
	 */
	public function get_weight() {
		return $this->weight;
	}

	/**
	 * get_outer_height
	 * @return float
	 */
	public function get_outer_height() {
		return $this->outer_height;
	}

	/**
	 * get_outer_width
	 * @return float
	 */
	public function get_outer_width() {
		return $this->outer_width;
	}

	/**
	 * get_outer_length
	 * @return float
	 */
	public function get_outer_length() {
		return $this->outer_length;
	}

	/**
	 * get_packed_height
	 * @return float
	 */
	public function get_packed_height() {
		return $this->packed_height;
	}

	/**
	 * get_packed_width
	 * @return float
	 */
	public function get_packed_width() {
		return $this->packed_width;
	}

	/**
	 * get_width get_packed_length.
	 * @return float
	 */
	public function get_packed_length() {
		return $this->packed_length;
	}
}

/**
 * PH_FedEx_Boxpack_Item class.
 */
class PH_FedEx_Boxpack_Item {

	public $weight;
	public $height;
	public $width;
	public $length;
	public $volume;
	public $value;
	public $meta;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct( $length, $width, $height, $weight, $value = '', $meta = array() ) {
		$dimensions = array( $length, $width, $height );

		sort( $dimensions );

		$this->length = $dimensions[2];
		$this->width  = $dimensions[1];
		$this->height = $dimensions[0];

		$this->volume = $width * $height * $length;
		$this->weight = $weight;
		$this->value  = $value;
		$this->meta   = $meta;
	}

	/**
	 * get_volume function.
	 *
	 * @access public
	 * @return void
	 */
	function get_volume() {
		return $this->volume;
	}

	/**
	 * get_height function.
	 *
	 * @access public
	 * @return void
	 */
	function get_height() {
		return $this->height;
	}

	/**
	 * get_width function.
	 *
	 * @access public
	 * @return void
	 */
	function get_width() {
		return $this->width;
	}

	/**
	 * get_width function.
	 *
	 * @access public
	 * @return void
	 */
	function get_length() {
		return $this->length;
	}

	/**
	 * get_width function.
	 *
	 * @access public
	 * @return void
	 */
	function get_weight() {
		return $this->weight;
	}

	/**
	 * get_value function.
	 *
	 * @access public
	 * @return void
	 */
	function get_value() {
		return $this->value;
	}

	/**
	 * get_meta function.
	 *
	 * @access public
	 * @return void
	 */
	function get_meta( $key = '' ) {
		if ( $key ) {
			if ( isset( $this->meta[ $key ] ) ) {
				return $this->meta[ $key ];
			} else {
				return null;
			}
		} else {
			return array_filter( (array) $this->meta );
		}
	}
}
