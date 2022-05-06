/**
 * Switches the correct input fields on the feed form on or off depending on the selected channel
 *
 * @param {string} channel
 * @param {boolean} isNew
 * @returns nothing
 */

// WPPFM_CHANNEL_RELATED
function wppfm_showChannelInputs( channel, isNew ) {
	var fName = {
		'1': 'switchToGoogleFeedFormMainInputs',
		'2': 'switchToBingFeedFormMainInputs',
		'3': 'switchToBeslisFeedFormMainInputs',
		'4': 'switchToPricegrabberFeedFormMainInputs',
		'5': 'switchToShoppingFeedFormMainInputs',
		'6': 'switchToAmazonFeedFormMainInputs',
		'7': 'switchToConnexityFeedFormMainInputs',
		'9': 'switchToNextagFeedFormMainInputs',
		'10': 'switchToKieskeurigFeedFormMainInputs',
		'11': 'switchToVergelijkFeedFormMainInputs',
		'12': 'switchToKoopjespakkerFeedFormMainInputs',
		'13': 'switchToAvantLinkFeedFormMainInputs',
		'14': 'switchToZboziFeedFormMainInputs',
		'15': 'switchToComconFeedFormMainInputs',
		'16': 'switchToFacebookFeedFormMainInputs',
		'17': 'switchToBolFeedFormMainInputs',
		'18': 'switchToAdtractionFeedFormMainInputs',
		'19': 'switchToRicardoFeedFormMainInputs',
		'20': 'switchToEbayFeedFormMainInputs',
		'21': 'switchToShopzillaFeedFormMainInputs',
		'22': 'switchToConvertoFeedFormMainInputs',
		'23': 'switchToIdealoFeedFormMainInputs',
		'24': 'switchToHeurekaFeedFormMainInputs',
		'25': 'switchToPepperjamFeedFormMainInputs',
		'26': 'switchToGalaxusProductDataFeedFormMainInputs',
		'27': 'switchToGalaxusProductPropertiesFeedFormMainInputs',
		'28': 'switchToGalaxusProductStockPricingFeedFormMainInputs',
		'29': 'switchToVivinoFeedFormMainInputs',
		'996': 'switchToMarketingrobotTsvFeedFormMainInputs',
		'997': 'switchToMarketingrobotTxtFeedFormMainInputs',
		'998': 'switchToMarketingrobotCsvFeedFormMainInputs',
		'999': 'switchToMarketingrobotFeedFormMainInputs',
	};

	// call the correct function
	if ( fName.hasOwnProperty( channel ) ) {
		window[ fName[ channel ] ]( isNew, channel );
	}

	// standard for all channels
	jQuery( '#update-schedule-row' ).show();
	jQuery( '#add-product-variations-row' ).show();

	if ( (
		null === jQuery( '#lvl_0' ).val() && '' === jQuery( '#selected-categories' ).html() ) || 0 === jQuery( '#wppfm-countries-selector' ).val() ) {
		wppfm_show_or_hide_category_map( channel );
	} else {
		jQuery( '#wppfm-category-map' ).show();
	}
}

/**
 * depending on channel show or hide the category map directly after channel selection
 * Add the channel to the "show" part when it does not have an own category list
 *
 * @param {string} channel
 * @returns nothing
 */
function wppfm_show_or_hide_category_map( channel ) {
	var category_map_selector = jQuery( '#wppfm-category-map' );

	switch ( channel ) {
		case '15': // Commerce Connector
		case '17': // Bol.com
		case '18': // Adtraction
		case '22': // Converto
		case '23': // Idealo
		case '25': // Pepperjam
		case '26': // Galaxus Product Data
		case '27': // Galaxus Product Stock Pricing
		case '28': // Galaxus Product Properties
		case '29': // Vivino
			category_map_selector.show();
			break;

		default:
			category_map_selector.hide();
			break;
	}
}

/**
 * Usually categories are separated by a > but some channels use other separators
 *
 * @since 2.2.0
 *
 * @param {string} channel
 * @returns {string} separator
 */
function wppfm_category_separator( channel ) {
	switch ( channel ) {
		case '14': // Commerce Connector
		case '24': // Commerce Connector
			return ' | ';

		default:
			return ' > ';
	}
}

/**
 * calls the correct channel function that makes sure the correct input fields are shown after the user has changed the title or channel
 *
 * @param {string} channel
 * @param {string} feedId
 * @param {boolean} categoryChanged
 * @returns nothing
 */
function wppfm_reactOnChannelInputChanged( channel, feedId, categoryChanged ) {
	var functionName;
	var tabId = wppfm_getUrlVariable( 'tab' ); // identify the feed type

	if ( 'product-feed' === tabId ) { // handle product feeds from different merchants
		var fName = {
			'1': 'googleInputChanged',
			'2': 'bingInputChanged',
			'3': 'beslisInputChanged',
			'4': 'pricegrabberInputChanged',
			'5': 'shoppingInputChanged',
			'6': 'amazonInputChanged',
			'7': 'connexityInputChanged',
			'9': 'nextagInputChanged',
			'10': 'kieskeurigInputChanged',
			'11': 'vergelijkInputChanged',
			'12': 'koopjespakkerInputChanged',
			'13': 'avantlinkInputChanged',
			'14': 'zboziInputChanged',
			'15': 'comconInputChanged',
			'16': 'facebookInputChanged',
			'17': 'bolInputChanged',
			'18': 'adtractionInputChanged',
			'19': 'ricardoInputChanged',
			'20': 'ebayInputChanged',
			'21': 'shopzillaInputChanged',
			'22': 'convertoInputChanged',
			'23': 'idealoInputChanged',
			'24': 'heurekaInputChanged',
			'25': 'pepperjamInputChanged',
			'26': 'galaxusProductDataInputChanged',
			'27': 'galaxusProductStockPricingInputChanged',
			'28': 'galaxusProductPropertiesInputChanged',
			'29': 'vivinoInputChanged',
			'996': 'marketingrobotTsvInputChanged',
			'997': 'marketingrobotTxtInputChanged',
			'998': 'marketingrobotCsvInputChanged',
			'999': 'marketingrobotInputChanged',
		};

		functionName = fName[ channel ];
	} else { // handle special feeds from add-ons
		var functionString = wppfm_convertToCamelCase( tabId.split( '-' ) );
		functionName   = functionString + 'Changed';
	}

	// call the correct function
	if ( functionName ) {
		window[ functionName ]( feedId, categoryChanged );
	}
}

/**
 * Returns txt, csv, tsv or xml depending on the feed type that needs to be made. Default is xml.
 *
 * @param {string} channel
 * @returns {string} with the channel feed type
 */
function wppfm_getChannelFeedType( channel ) {
	switch ( channel ) {
		case '2': // bing
		case '4': // pricegrabber
		case '6': // amazon
		case '7': // connexity
		case '9': // nextag
		case '12': // koopjespakker.nl
		case '21': // shopzilla
		case '25': // pepperjam
		case '29': // Vivino
		case '997': // Custom TXT Feed
			return 'txt';

		case '15': // Commerce Connector
		case '17': // Bol.com
		case '19': // Ricardo.ch
		case '22': // Converto
		case '23': // Idealo
		case '26': // Galaxus Product Data
		case '27': // Galaxus Product Stock Pricing
		case '28': // Galaxus Product Properties
		case '998': // Custom CSV Feed
			return 'csv';

		case '996': // Custom TSV feed
			return 'tsv';

		default:
			return 'xml';
	}
}

/**
 * Returns the correct country code for the channel specific category text file. en-US is the default.
 *
 * @param {string} channel
 * @returns {String} with the channels country code
 */
function wppfm_channelCountryCode( channel ) {
	var language = 'en-US';

	// WPPFM_CHANNEL_RELATED
	switch ( channel ) {
		case '3': // Beslist
		case '10': // Kieskeurig
		case '11': // Vergelijk
		case '12': // Koopjespakker
		case '17': // Bol.com
			language = 'nl-NL';
			break;

		case '14': // Zbozi
		case '24': // Heureka
			language = 'cs-CZ';
			break;

		case '19': // Ricardo.ch
			language = 'de-CH';
			break;

		case '23': // Idealo
			language = 'de-DE';
			break;
	}

	return language;
}

/**
 * Returns true if the specified channel does not have its own categories but uses the users shop
 * categories instead.
 *
 * @param {string} channel
 * @returns {Boolean} true when this channel uses categories from the shop
 */
function wppfm_channelUsesOwnCategories( channel ) {
	// only add the channel when it uses the shop categories in stead of specific channel categories
	switch ( channel ) {
		case '10': // kieskeurig.nl
		case '15': // Commerce Connector
		case '17': // Bol.com
		case '18': // Adtraction
		case '22': // Converto
		case '23': // Idealo
		case '25': // Pepperjam
		case '26': // Galaxus Product Data
		case '27': // Galaxus Product Stock Pricing
		case '28': // Galaxus Product Properties
		case '29': // Vivino
			return true;

		default:
			return false;
	}
}

/**
 * If required for that channel, this function activates the correct function that will prepare the global category
 * variables in the channel specific javascript file. Does nothing when not required for the channel.
 *
 * @param {string} channel
 * @param {string} selectedCategory
 * @param {string} currentLevelId
 * @returns nothing
 */
function wppfm_fillCategoryVariables(
	channel, selectedCategory, currentLevelId ) {
	var fName = {
		'1': 'fillGoogleCategoryVariables',
		'2': 'fillBingCategoryVariables',
		'4': 'fillPricegrabberCategoryVariables',
		'5': 'fillShoppingCategoryVariables',
		'6': 'fillAmazonCategoryVariables',
		'7': 'fillConnexityCategoryVariables',
		'9': 'fillNextagCategoryVariables',
		'13': 'fillAvantLinkCategoryVariables',
		'14': 'fillZboziCategoryVariables',
	};

	// call the correct function
	if ( fName.hasOwnProperty( channel ) && 'function' === typeof window[ fName[ channel ] ] ) {
		// call the correct switch  main form inputs function
		window[ fName[ channel ] ]( selectedCategory, currentLevelId );
	}
}

/**
 * Some fields require specific allowed inputs. This function gets the correct options for given field
 *
 * @param {string} rowId
 * @param {string} queryLevel
 * @param {string} combinationLevel
 * @param {string} channel
 * @param {string} fieldName
 * @param {string} selectedValue
 * @returns {String} containing the allowed options
 */
function wppfm_displayCorrectStaticField(
	rowId, queryLevel, combinationLevel, channel, fieldName, selectedValue ) {
	var html    = '';
	var options = wppfm_restrictedStaticFields( channel, fieldName );

	if ( options !== undefined ) {
		if ( options.length === 0 ) {
			// show the standard text type input field
			html = wppfm_staticInputField( rowId, queryLevel, combinationLevel,	selectedValue );
		} else {
			// show the standard selector with the correct allowed options
			html = wppfm_staticInputSelect( rowId, queryLevel, combinationLevel, options, selectedValue );
		}
	}

	return html;
}

/**
 * Gets the advised input fields
 *
 * @param {string} channel
 * @returns {array} array containing the advised inputs
 */
function wppfm_getAdvisedInputs( channel ) {
	var fName = {
		'1': 'woocommerceToGoogleFields',
		'2': 'woocommerceToBingFields',
		'3': 'woocommerceToBeslisFields',
		'4': 'woocommerceToPricegrabberFields',
		'5': 'woocommerceToShoppingFields',
		'6': 'woocommerceToAmazonFields',
		'7': 'woocommerceToConnexityFields',
		'9': 'woocommerceToNextagFields',
		'10': 'woocommerceToKieskeurigFields',
		'11': 'woocommerceToVergelijkFields',
		'12': 'woocommerceToKoopjespakkerFields',
		'13': 'woocommerceToAvantLinkFields',
		'14': 'woocommerceToZboziFields',
		'15': 'woocommerceToComconFields',
		'16': 'woocommerceToFacebookFields',
		'17': 'woocommerceToBolFields',
		'18': 'woocommerceToAdtractionFields',
		'19': 'woocommerceToRicardoFields',
		'20': 'woocommerceToeBayFields',
		'21': 'woocommerceToShopzillaFields',
		'22': 'woocommerceToConvertoFields',
		'23': 'woocommerceToIdealoFields',
		'24': 'woocommerceToHeurekaFields',
		'25': 'woocommerceToPepperjamFields',
		'26': 'woocommerceToGalaxusProductDataFields',
		'27': 'woocommerceToGalaxusProductStockPricingFields',
		'28': 'woocommerceToGalaxusProductPropertiesFields',
		'29': 'woocommerceToVivinoFields',
	};

	if ( fName.hasOwnProperty( channel ) ) {
		// call the correct function
		return window[ fName[ channel ] ]();
	} else {
		return [];
	}
}

/**
 * Sets the attributes to the correct levels depending on several variables.
 *
 * @param   {string}    channel         Channel id.
 * @param   {object}    feedHolder      Feed Holder containing feed data items.
 * @param   {string}    selectArgument
 *
 * @returns {object}    feed holder with the correct attribute levels
 */
// ALERT has a relation with the set_output_attribute_levels() function in the class-wppfm-data.php file
function wppfm_setOutputAttributeLevels( channel, feedHolder, selectArgument ) {
	switch ( channel ) {
		case '1':
			return setGoogleOutputAttributeLevels( feedHolder, selectArgument );

		case '2':
			return setBingOutputAttributeLevels( feedHolder, selectArgument );

		case '3':
			return setBeslisOutputAttributeLevels( feedHolder );

		case '4':
			return setPricegrabberOutputAttributeLevels( feedHolder );

		case '5':
			return setShoppingOutputAttributeLevels( feedHolder );

		case '6':
			return setAmazonOutputAttributeLevels( feedHolder );

		case '7':
			return setConnexityOutputAttributeLevels( feedHolder );

		case '9':
			return setNextagOutputAttributeLevels( feedHolder );

		case '10':
			return setKieskeurigOutputAttributeLevels( feedHolder );

		case '11':
			return setVergelijkOutputAttributeLevels( feedHolder );

		case '13':
			return setAvantLinkOutputAttributeLevels( feedHolder,
				selectArgument );

		case '14':
			return setZboziOutputAttributeLevels( feedHolder, selectArgument );

		case '26':
			return setGalaxusProductDataAttributeLevels( feedHolder,
				selectArgument );

		case '27':
			return setGalaxusProductStockPricingAttributeLevels( feedHolder );

		case '28':
			return setGalaxusProductPropertiesAttributeLevels( feedHolder );

		case '996':
			return setMarketingrobotTsvOutputAttributeLevels( feedHolder );

		case '997':
			return setMarketingrobotTxtOutputAttributeLevels( feedHolder );

		case '998':
			return setMarketingrobotCsvOutputAttributeLevels( feedHolder );

		case '999':
			return setMarketingrobotOutputAttributeLevels( feedHolder );

		default:
			return feedHolder;
	}
}

/**
 * returns an array with the channel specific fields with restricted input options
 *
 * @param {string} channel
 * @param {string} fieldName
 * @returns {array}
 */
function wppfm_restrictedStaticFields( channel, fieldName ) {
	var fName = {
		'1': 'googleStaticFieldOptions',
		'2': 'bingStaticFieldOptions',
		'3': 'beslisStaticFieldOptions',
		'4': 'pricegrabberStaticFieldOptions',
		'5': 'shoppingStaticFieldOptions',
		'6': 'amazonStaticFieldOptions',
		'7': 'connexityStaticFieldOptions',
		'9': 'nextagStaticFieldOptions',
		'10': 'kieskeurigStaticFieldOptions',
		'11': 'vergelijkStaticFieldOptions',
		'12': 'koopjespakkerStaticFieldOptions',
		'13': 'avantlinkStaticFieldOptions',
		'14': 'zboziStaticFieldOptions',
		'15': 'comconStaticFieldOptions',
		'16': 'facebookStaticFieldOptions',
		'17': 'bolStaticFieldOptions',
		'18': 'adtractionStaticFieldOptions',
		'19': 'ricardoStaticFieldOptions',
		'20': 'ebayStaticFieldOptions',
		'21': 'shopzillaStaticFieldOptions',
		'23': 'idealoStaticFieldOptions',
		'25': 'pepperjamStaticFieldOptions',
		'26': 'galaxusProductDataStaticFieldOptions',
		'27': 'galaxusProductStockPricingStaticFieldOptions',
		'28': 'galaxusProductPropertiesStaticFieldOptions',
	};

	if ( fName.hasOwnProperty( channel ) ) {
		// call the correct function
		return window[ fName[ channel ] ]( fieldName );
	} else {

		return [];
	}
}

/**
 * Set a preset condition, other than the advised input, for fields for a specific channel (eg. condition = static field with 'new' selected).
 *
 * @param {array} outputsField
 * @param {string} channel
 * @returns {array}
 */
function wppfm_setChannelRelatedPresets( outputsField, channel ) {
	// WPPFM_CHANNEL_RELATED
	switch ( channel ) {

		// @since 2.27.0 added sale_price-effective_date.
		case '1': // Google
			if ( outputsField[ 'field_label' ] === 'condition' || outputsField[ 'field_label' ] === 'availability' || outputsField[ 'field_label' ] === 'identifier_exists'
				|| outputsField[ 'field_label' ] === 'adult' || outputsField[ 'field_label' ] === 'price' || outputsField[ 'field_label' ] === 'sale_price'
				|| outputsField[ 'field_label'] === 'sale_price_effective_date' ) {

				// only switch to the 'preset' value if no user value is set
				if ( ! outputsField[ 'value' ] ) {
					outputsField[ 'value' ] = setGooglePresets(	outputsField[ 'field_label' ] );
				}
			}
			break;

		case '2': // Bing
			if ( outputsField[ 'field_label' ] === 'seller_name' ) {
				// only switch to the 'preset' value if no user value is set
				if ( ! outputsField[ 'value' ] ) {
					outputsField[ 'value' ] = setBingPresets( outputsField[ 'field_label' ] );
				}
			}
			break;

		case '3': // Beslist
			if ( outputsField[ 'field_label' ] === 'Conditie' || outputsField[ 'field_label' ] === 'Levertijd' ) {
				// only switch to the 'preset' value if no user value is set
				if ( ! outputsField[ 'value' ] ) {
					outputsField[ 'value' ] = setBeslisPresets( outputsField[ 'field_label' ] );
				}
			}
			break;

		case '13': // Avant Link
			if ( outputsField[ 'field_label' ] === 'condition' || outputsField[ 'field_label' ] === 'availability' || outputsField[ 'field_label' ] === 'identifier_exists' ) {

				// only switch to the 'preset' value if no user value is set
				if ( ! outputsField[ 'value' ] ) {
					outputsField[ 'value' ] = setAvantLinkPresets( outputsField[ 'field_label' ] );
				}
			}
			break;

		case '14': // Zbozi
			if ( outputsField[ 'field_label' ] === 'EROTIC' || outputsField[ 'field_label' ] === 'VISIBILITY' ) {
				// only switch to the 'preset' value if no user value is set
				if ( ! outputsField[ 'value' ] ) {
					outputsField[ 'value' ] = setZboziPresets( outputsField[ 'field_label' ] );
				}
			}
			break;

		case '15': // Commerce Connector
			if ( outputsField[ 'field_label' ] === 'Delivery time' ) {
				// only switch to the 'preset' value if no user value is set
				if ( ! outputsField[ 'value' ] ) {
					outputsField[ 'value' ] = setComconPresets( outputsField[ 'field_label' ] );
				}
			}
			break;

		// @since 2.27.0 Updated to the same settings as for Google.
		case '16': // Facebook
			if ( outputsField[ 'field_label' ] === 'condition' || outputsField[ 'field_label' ] === 'availability' || outputsField[ 'field_label' ] === 'identifier_exists'
				|| outputsField[ 'field_label' ] === 'adult' || outputsField[ 'field_label' ] === 'price' || outputsField[ 'field_label' ] === 'sale_price'
				|| outputsField[ 'field_label'] === 'sale_price_effective_date' ) {

				// only switch to the 'preset' value if no user value is set
				if ( ! outputsField[ 'value' ] ) {
					outputsField[ 'value' ] = setFacebookPresets( outputsField[ 'field_label' ] );
				}
			}
			break;

		case '17': // Bol.com
			if ( outputsField[ 'field_label' ] === 'Condition' || outputsField[ 'field_label' ] === 'Deliverycode' ) {
				// only switch to the 'preset' value if no user value is set
				if ( ! outputsField[ 'value' ] ) {
					outputsField[ 'value' ] = setBolPresets( outputsField[ 'field_label' ] );
				}
			}
			break;

		case '18': // Adtraction
			if ( outputsField[ 'field_label' ] === 'instock' ) {
				// only switch to the 'preset' value if no user value is set
				if ( ! outputsField[ 'value' ] ) {
					outputsField[ 'value' ] = setAdtractionPresets( outputsField[ 'field_label' ] );
				}
			}
			break;

		case '19': // Ricardo
			if ( outputsField[ 'field_label' ] === 'Descriptions[0].LanguageNr' || outputsField[ 'field_label' ] === 'Increment' || outputsField[ 'field_label' ] === 'AvailabilityId' || outputsField[ 'field_label' ] === 'Condition' ) {

				// only switch to the 'preset' value if no user value is set
				if ( ! outputsField[ 'value' ] ) {
					outputsField[ 'value' ] = setRicardoPresets( outputsField[ 'field_label' ] );
				}
			}
			break;

		case '20': // eBay
			break;

		case '21': // Shopzilla
			if ( outputsField[ 'field_label' ] === 'Availability' || outputsField[ 'field_label' ] === 'Condition' ) {
				// only switch to the 'preset' value if no user value is set
				if ( ! outputsField[ 'value' ] ) {
					outputsField[ 'value' ] = setShopzillaPresets( outputsField[ 'field_label' ] );
				}
			}
			break;

		case '22': // Converto
			if ( outputsField[ 'field_label' ] === 'Availability' || outputsField[ 'field_label' ] === 'Condition' ) {
				// only switch to the 'preset' value if no user value is set
				if ( ! outputsField[ 'value' ] ) {
					outputsField[ 'value' ] = setShopzillaPresets( outputsField[ 'field_label' ] );
				}
			}
			break;

		case '24': // Heureka
			if ( 'ITEM_TYPE' === outputsField[ 'field_label' ] ) {
				if ( ! outputsField[ 'value' ] ) {
					outputsField[ 'value' ] = setHeurekaPresets( outputsField[ 'field_label' ] );
				}
			}
			break;

		case '25': // Pepperjam
			if ( 'ITEM_TYPE' === outputsField[ 'discontinued' ] ) {
				if ( ! outputsField[ 'value' ] ) {
					outputsField[ 'value' ] = setPepperjamPresets( outputsField[ 'field_label' ] );
				}
			}
			break;

		case '26': // Galaxus Product Data
			if ( 'ITEM_TYPE' === outputsField[ 'discontinued' ] ) {
				if ( ! outputsField[ 'value' ] ) {
					outputsField[ 'value' ] = setGalaxusProductDataPresets( outputsField[ 'field_label' ] );
				}
			}
			break;

		case '27': // Galaxus Product Stock Pricing
			if ( 'ITEM_TYPE' === outputsField[ 'discontinued' ] ) {
				if ( ! outputsField[ 'value' ] ) {
					outputsField[ 'value' ] = setGalaxusProductStockPricingPresets( outputsField[ 'field_label' ] );
				}
			}
			break;

		case '28': // Galaxus Product Properties
			if ( 'ITEM_TYPE' === outputsField[ 'discontinued' ] ) {
				if ( ! outputsField[ 'value' ] ) {
					outputsField[ 'value' ] = setGalaxusProductPropertiesPresets( outputsField[ 'field_label' ] );
				}
			}
			break;

		default:
			break;
	}
}

function wppfm_requiresLanguageInput( channel ) {
	switch ( channel ) {
		case '26':
			return true; // Galaxus Product Data

		default:
			return false;
	}
}

/**
 * returns if a channel is a custom feed channel
 *
 * @param {string} channel
 * @returns {boolean}
 */
function wppfm_isCustomChannel( channel ) {
	switch ( channel ) {
		case '996': // Custom TSV Feed
		case '997': // Custom TXT Feed
		case '998': // Custom CSV Feed
		case '999': // Custom XML Feed
			return true;

		default:
			return false;
	}
}

// ALERT! has a php equivalent in class-feed-master.php called set_attribute_status();
function setAttributeStatus( fieldLevel, fieldValue ) {
	if ( fieldLevel > 0 && fieldLevel < 3 ) {
		return true;
	}

	return !!fieldValue;
}
