<?php
/**
 * Handles all CSV generation functions.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    makewebbetter-hubspot-for-woocommerce
 * @subpackage makewebbetter-hubspot-for-woocommerce/includes
 */

/**
 * Manage all csv import.
 *
 * Provide a list of functions to manage all the information
 * about import csv handling.
 *
 * @package    makewebbetter-hubspot-for-woocommerce
 * @subpackage makewebbetter-hubspot-for-woocommerce/includes
 */
class Hubwoo_Csv_Handler {

	/**
	 * Working dircetory path( Predefined Server properties ).
	 *
	 * @var string
	 */
	private $working_path = '';

	/**
	 * Base dirctory path( Predefined Server properties ).
	 *
	 * @var string
	 */
	private $base_dir = HUBWOO_ABSPATH;

	/**
	 * Activity folder( Data holding properties ).
	 *
	 * @var string
	 */
	private $folder;

	/**
	 * Path validation.
	 *
	 * @var bool|array
	 */
	public $dirpath;

	/**
	 * Constructor function.
	 *
	 * @param string $folder Activity folder name.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $folder = 'hubwoo-temp-dir' ) {

		$this->folder = $folder;

		// Create Base Activity Directory.
		$this->working_path = $this->base_dir . $this->folder;
		$this->dirpath      = $this->check_and_create_folder( $this->working_path );

	}

	/**
	 * Library functions :: Check/create folder.
	 *
	 * @param string $path Path to create folder.
	 *
	 * @since 1.0.0
	 */
	public function check_and_create_folder( $path = '' ) {

		if ( ! empty( $path ) && ! is_dir( $path ) && is_writable( $this->base_dir ) ) {

			$directory = mkdir( $path, 0755, true );

			if ( $directory ) {
				return array(
					'status' => $directory,
				);
			}
		} else {
			return array(
				'status'  => false,
				'message' => 'We do not have write permission to create dir/file.',
			);
		}
	}

	/**
	 * Generate Contacts csv.
	 *
	 * @param array $contacts An array of all users properties data.
	 */
	public function generate_contacts_csv( $contacts = array() ) {

		$contact_file = fopen( $this->working_path . '/contacts-import.csv', 'w' );

		fputcsv(
			$contact_file,
			array(
				'Firstname',
				'Lastname',
				'Email Address',
				'Company',
				'Phone Number',
				'Mobile Number',
				'Address',
				'City',
				'State',
				'Country',
				'Postcode',
			)
		);

		foreach ( $contacts as $key => $value ) {
			$contact_cells = $this->format_contact_csv_cells( $value );

			fputcsv(
				$contact_file,
				array(
					$contact_cells['firstname'],
					$contact_cells['lastname'],
					$contact_cells['email'],
					$contact_cells['company'],
					$contact_cells['phone'],
					$contact_cells['mobilephone'],
					$contact_cells['address'],
					$contact_cells['city'],
					$contact_cells['state'],
					$contact_cells['country'],
					$contact_cells['zip'],
				)
			);
		}

		return fclose( $contact_file );

	}

	/**
	 * Returns a set of contact propertieis to be mapped in CSV.
	 *
	 * @param array $contact An array of user data.
	 *
	 * @return array An array of set of contact properties
	 */
	public function format_contact_csv_cells( $contact = array() ) {

		$email = ! empty( $contact['email'] ) ? $contact['email'] : false;
		unset( $contact['email'] );

		$properties          = ! empty( $contact['properties'] ) ? $this->format_contact_properties( $contact['properties'] ) : array();
		$properties['email'] = ! empty( $email ) ? $email : false;

		return $properties;
	}

	/**
	 * Format properties of an array of contact data.
	 *
	 * @param array $properties Set of properties to format.
	 *
	 * @return string Resultant of an array key.
	 */
	public function format_contact_properties( $properties = array() ) {

		$resultant = array();

		if ( ! empty( $properties ) && is_array( $properties ) ) {

			foreach ( $properties as $key => $value ) {
				$resultant[ $value['property'] ] = ! empty( $value['value'] ) ? $value['value'] : false;
			}
		}

		return $resultant;
	}


}
