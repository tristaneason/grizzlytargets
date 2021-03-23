<?php

if (!class_exists('WPSE_WC_Importer')) {

	class WPSE_WC_Importer extends WC_Product_CSV_Importer {

		function __construct($params) {
			$default_args = array(
				'data' => array(),
				'mapping' => array(), // Column mapping. csv_heading => schema_heading.
				'update_existing' => false, // Whether to update existing items.
				
				// Unused
				'delimiter' => ',', // CSV delimiter.
				'start_pos' => 0, // File pointer start.
				'end_pos' => -1, // File pointer end.
				'lines' => -1, // Max lines to read.
				'parse' => false, // Whether to sanitize and format data.
				'prevent_timeouts' => false, // Check memory and time usage and abort if reaching limit.
				'enclosure' => '"', // The character used to wrap text in the CSV.
				'escape' => "\0", // PHP uses '\' as the default escape character. This is not RFC-4180 compliant. This disables the escape character.
			);

			$this->params = wp_parse_args($params, $default_args);
			// We don't use this file parameter
			$this->file = 'xxxx';
			$this->raw_data = array_map('array_values', $params['data']);
			$this->raw_keys = array_keys($this->params['mapping']);
			$this->set_mapped_keys();
			$this->set_parsed_data();			
		}

	/**
	 * Parse the ID field.
	 *
	 * If we're not doing an update, create a placeholder product so mapping works
	 * for rows following this one.
	 *
	 * @param string $value Field value.
	 *
	 * @return int
	 */
	public function parse_id_field( $value ) {
		// We already parsed the ID before executing the import
		return (int) $value;
	}
	}

}