<?php
if ( ! defined( 'CFW_DEV_MODE' ) ) {
	// Dev Mode
	define( 'CFW_DEV_MODE', getenv( 'CFW_DEV_MODE' ) == 'true' ? true : false );
}