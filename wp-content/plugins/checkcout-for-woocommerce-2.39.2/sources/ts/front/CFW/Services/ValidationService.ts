import { Main }                                         from "../Main";
import { EasyTabService }                               from "./EasyTabService";
import { EasyTabDirection }                             from "./EasyTabService";
import { EasyTab }                                      from "./EasyTabService";
import { Alert, AlertInfo }                             from "../Elements/Alert";
import { AccountExistsAction }                          from "../Actions/AccountExistsAction";

declare var cfwEventData: any;

/**
 * Validation Sections Enum
 */
export enum EValidationSections {
	SHIPPING,
	BILLING,
	ACCOUNT
}

/**
 *
 */
export class ValidationService {

	/**
	 * @type {EValidationSections}
	 * @private
	 */
	private static _currentlyValidating: EValidationSections;

	/**
	 * @param easyTabsWrap
	 */
	constructor( easyTabsWrap: any ) {
		this.validateSectionsBeforeSwitch( easyTabsWrap );
		this.validateBillingFieldsBeforeSubmit();

		ValidationService.validateShippingOnLoadIfNotCustomerTab();
	}

	/**
	 * Execute validation checks before each easy tab easy tab switch.
	 *
	 * @param {any} easyTabsWrap
	 */
	validateSectionsBeforeSwitch( easyTabsWrap: any ): void {

		easyTabsWrap.bind( 'easytabs:before', function( event, clicked, target ) {
			// Where are we going?
			let easyTabDirection: EasyTabDirection = EasyTabService.getTabDirection( target );

			// If we are moving forward in the checkout process and we are currently on the customer tab
			if ( easyTabDirection.current === EasyTab.CUSTOMER && easyTabDirection.target > easyTabDirection.current ) {

				let validated: boolean = ValidationService.validateSectionsForCustomerTab();
				let login_required_error: boolean = false;
				let tabId: string = EasyTabService.getTabId( easyTabDirection.current );

				if ( ! cfwEventData.settings.user_logged_in && cfwEventData.settings.is_registration_required && AccountExistsAction.checkBox ) {
					login_required_error = true;
					validated = false;
				}

				// If a login required error happened, add it here so it happens after the hash jump above
				if ( login_required_error ) {
					let alert: Alert = new Alert( Main.instance.alertContainer, <AlertInfo> {
						type: "error",
						message: cfwEventData.settings.account_already_registered_notice,
						cssClass: "cfw-alert-error cfw-login-required-error"
					} );

					alert.addAlert();
				}

				if ( ! validated ) {
					event.stopImmediatePropagation();
				}

				// Return the validation
				return validated;
			}

			// If we are moving forward / backwards, have a shipping easy tab, and are not on the customer tab then allow
			// the tab switch
			return true;
		}.bind( this ));
	}

	validateBillingFieldsBeforeSubmit(): void {
		let checkoutForm: any = Main.instance.checkoutForm;

		checkoutForm.on( 'submit', function( e ) {
			let validated = false;

			if ( cfwEventData.settings.needs_shipping_address == 1 && checkoutForm.find( 'input[name="bill_to_different_address"]:checked' ).val() !== "same_as_shipping" ) {
				validated = ValidationService.validate( EValidationSections.BILLING );
			} else {
				validated = true; // If digital only order, billing address was handled on customer info tab so set to true
			}

			if ( ! validated ) {
				e.preventDefault();
				e.stopImmediatePropagation(); // prevent bubbling up the DOM *and* prevent other submit handlers from firing, such as completeOrder
			}

			return validated;
		} );
	}

	/**
	 *
	 * @returns {boolean}
	 */
	static validateSectionsForCustomerTab(): boolean {
		let validated = false;

		let account_validated = ValidationService.validate( EValidationSections.ACCOUNT );

		if ( cfwEventData.settings.needs_shipping_address == 0 ) {
			let billing_address_validated = ValidationService.validate( EValidationSections.BILLING );

			validated = account_validated && billing_address_validated;
		} else {
			let shipping_address_validated = ValidationService.validate( EValidationSections.SHIPPING );

			validated = account_validated && shipping_address_validated;
		}

		return validated;
	}

	/**
	 * @param {EValidationSections} section
	 * @returns {any}
	 */
	static validate( section: EValidationSections ): any {
		let validated: boolean;
		let checkoutForm: any = Main.instance.checkoutForm;

		ValidationService.currentlyValidating = section;

		switch( section ) {
			case EValidationSections.SHIPPING:
				validated = checkoutForm.parsley().validate( { group: 'shipping'} );
				break;
			case EValidationSections.BILLING:
				validated = checkoutForm.parsley().validate( { group: 'billing'} );
				break;
			case EValidationSections.ACCOUNT:
				validated = checkoutForm.parsley().validate( { group: 'account'} );
				break;
		}

		if( validated == null ) {
			validated = true;
		}

		return validated;
	}

	/**
	 * Handles non ajax cases
	 */
	static validateShippingOnLoadIfNotCustomerTab(): void {
		let hash: string = window.location.hash;
		let customerInfoId: string = "#cfw-customer-info";
		let sectionToValidate: EValidationSections = ( cfwEventData.settings.needs_shipping_address == 1 ) ? EValidationSections.SHIPPING : EValidationSections.BILLING;

		if ( hash != customerInfoId && hash != "" ) {

			if ( ! ValidationService.validate( sectionToValidate ) ) {
				EasyTabService.go( EasyTab.CUSTOMER );
			}
		}
	}


	/**
	 * @return {EValidationSections}
	 */
	static get currentlyValidating(): EValidationSections {
		return this._currentlyValidating;
	}

	/**
	 * @param {EValidationSections} value
	 */
	static set currentlyValidating( value: EValidationSections ) {
		this._currentlyValidating = value;
	}
}