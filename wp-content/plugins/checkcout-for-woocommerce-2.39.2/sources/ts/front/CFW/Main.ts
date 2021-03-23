import { TabContainer }							from "./Elements/TabContainer";
import { AjaxInfo } 							from "./Types/Types";
import { TabContainerSection }					from "./Elements/TabContainerSection";
import { ValidationService }					from "./Services/ValidationService";
import { EasyTabService }						from "./Services/EasyTabService";
import { ParsleyService }						from "./Services/ParsleyService";
import { Alert, AlertInfo } 					from "./Elements/Alert";
import { CompleteOrderAction } 					from "./Actions/CompleteOrderAction";
import { ZipAutocompleteService }               from "./Services/ZipAutocompleteService";
import { AddressAutocompleteService }           from "./Services/AddressAutocompleteService";

declare let jQuery: any;
declare let cfwEventData: any;

/**
 * The main class of the front end checkout system
 */
export class Main {
	/**
	 * @type {any}
	 * @private
	 */
	private _checkoutForm: any;

	/**
	 * @type {TabContainer}
	 * @private
	 */
	private _tabContainer: TabContainer;

    /**
	 * @type {any}
	 * @private
     */
    private _alertContainer: any;

	/**
	 * @type {AjaxInfo}
	 * @private
	 */
	private _ajaxInfo: AjaxInfo;

	/**
	 * @type {any}
	 * @private
	 */
	private _settings: any;

	/**
	 * @type {ParsleyService}
	 * @private
	 */
	private _parsleyService: ParsleyService;

	/**
	 * @type {EasyTabService}
	 * @private
	 */
	private _easyTabService: EasyTabService;

	/**
	 * @type {ValidationService}
	 * @private
	 */
	private _validationService: ValidationService;

	/**
	 * @type {ZipAutocompleteService}
	 * @private
	 */
	private _zipAutocompleteService: ZipAutocompleteService;

	/**
	 * @type {AddressAutocompleteService}
	 * @private
	 */
	private _addressAutocompleteService: AddressAutocompleteService;

	/**
	 * @type {boolean}
	 * @private
	 */
	private _force_updated_checkout: boolean;

	/**
	 * @type {boolean}
	 * @private
	 */
	private _preserve_alerts: boolean;

	/**
	 * @type {MutationObserver}
	 * @private
	 */
	private _errorObserver: MutationObserver;

	/**
	 * @type any
	 * @private
	 */
	private _updateCheckoutTimer: any;

	/**
	 * @type {Main}
	 * @private
	 * @static
	 */
	private static _instance: Main;

	/**
	 * @param {any} checkoutFormEl
	 * @param {any} easyTabsWrap
	 * @param {any} alertContainer
	 * @param {TabContainer} tabContainer
	 * @param {AjaxInfo} ajaxInfo
	 * @param {Cart} cart
	 * @param {any} settings
	 * @param {any} compatibility
	 */
	constructor(
		checkoutFormEl: any,
		easyTabsWrap: any,
		alertContainer: any,
		tabContainer: TabContainer,
		ajaxInfo: AjaxInfo,
		settings: any,
		compatibility: any
	) {
		Main.instance = this;

		checkoutFormEl.garlic({
			events: ['textInput', 'input', 'change', 'click', 'keypress', 'paste', 'focus'],
			destroy: false,
			excluded: 'input[type="file"], input[type="hidden"], input[type="submit"], input[type="reset"], input[name="payment_method"], input[name="paypal_pro-card-number"], input[name="paypal_pro-card-cvc"], input[name="wc-authorize-net-aim-account-number"], input[name="wc-authorize-net-aim-csc"], input[name="paypal_pro_payflow-card-number"], input[name="paypal_pro_payflow-card-cvc"], input[name="paytrace-card-number"], input[name="paytrace-card-cvc"], input[id="stripe-card-number"], input[id="stripe-card-cvc"], input[name="creditCard"], input[name="cvv"], input.wc-credit-card-form-card-number, input[name="wc-authorize-net-cim-credit-card-account-number"], input[name="wc-authorize-net-cim-credit-card-csc"], input.wc-credit-card-form-card-cvc, input.js-sv-wc-payment-gateway-credit-card-form-account-number, input.js-sv-wc-payment-gateway-credit-card-form-csc, input.shipping_method, input[name^="tocheckoutcw"], #_sumo_pp_enable_order_payment_plan, .cfw-cart-quantity-input, .gift-certificate-show-form input'
		});

		if( easyTabsWrap.length === 0) {
			easyTabsWrap = tabContainer.jel;
		}

		this.checkoutForm = checkoutFormEl;
		this.tabContainer = tabContainer;
		this.alertContainer = alertContainer;
		this.ajaxInfo = ajaxInfo;
		this.settings = settings;
		this.parsleyService = new ParsleyService();
		this.easyTabService = new EasyTabService( easyTabsWrap );
		this.zipAutocompleteService = new ZipAutocompleteService();
		this.addressAutocompleteService = new AddressAutocompleteService();
		this.easyTabService.isDisplayed = this.settings.load_tabs;

		// Setup events and event listeners
		this.eventSetup();
	}

	/**
	 * Handles event setup and registration of listeners
	 */
	eventSetup(): void {
		this.compatibilityEvents();
		this.observerEvents();
	};

	/**
	 * Event setup relating to the registration and creation of compatibility classes
	 */
	compatibilityEvents(): void {
		// Compatibility Class Creation
		Object.keys( cfwEventData.compatibility ).forEach( function (key) {
			new (<any>window).CompatibilityClasses[ cfwEventData.compatibility[ key ].class ]( Main.instance, cfwEventData.compatibility[ key ].params );
		} );
	}

	/**
	 * Event setup relating to observers
	 */
	observerEvents() {
		(<any>window).addEventListener( 'cfw-main-after-setup', ({ detail }) => {
			// Error observer messages to ignore
			window.dispatchEvent( new CustomEvent( 'cfw-payment-error-observer-ignore-list' ));

			// Setup the errorObserver
			detail.main.errorObserverWatch();
		});
	}

	/**
	 * Sets up the tab container by running easy tabs, setting up animation listeners, and setting up events and on load
	 * functionality
	 */
	setup(): void {

		// Before setup event
		window.dispatchEvent( new CustomEvent( 'cfw-main-before-setup', { detail: { main: this } }));

		// Initialize the easy tabs
		this.easyTabService.initialize( this.tabContainer.tabContainerBreadcrumb );

		// Setup the validation service - has to happen after tabs are setup
        this.validationService = new ValidationService( this.easyTabService.easyTabsWrap );

		// Setup animation listeners
		this.setupAnimationListeners();

		// Fix floating labels
		this.tabContainer.setFloatLabelOnGarlicRetrieve();

		// Before set wraps event in case anyone needs to do some JIT class adding
		window.dispatchEvent( new CustomEvent( 'cfw-main-before-tab-container-set-wraps', { detail: { main: this } }));

		/**
		 * NOTE: If you are doing any DOM manipulation ( adding and removing classes specifically ). Do it before the setWraps
		 * call on the tab container sections. Once this is called all the setup of the different areas will have completed and
		 * wont be run again until next page load
		 */
		// Loop through and set up the wraps on the tab container sections
		this.tabContainer.tabContainerSections.forEach(( tcs: TabContainerSection ) => tcs.setWraps());

		// After the set wraps has done but before we set up any tabContainer listeners
		window.dispatchEvent( new CustomEvent( 'cfw-main-after-tab-container-set-wraps', { detail: { main: this } }));

		// Set up event handlers
		this.tabContainer.setUpdateCheckoutHandler();
		this.tabContainer.setAccountCheckListener();
		this.tabContainer.setDefaultLoginFormListener();
		this.tabContainer.setLogInListener();
        this.tabContainer.setPaymentMethodUpdate();
        this.tabContainer.setUpdateCartTriggers();
        this.tabContainer.setQuantityPromptTriggers();
		this.tabContainer.setUpMobileCartDetailsReveal();
		this.tabContainer.setCompleteOrderHandlers();
		this.tabContainer.setApplyCouponListener();
		this.tabContainer.setTermsAndConditions();
		this.tabContainer.setCreateAccountCheckboxListener();
		this.zipAutocompleteService.setZipAutocompleteHandlers();

		// Page load actions
		jQuery( window ).on( 'load', () => {
			this.tabContainer.setUpPaymentGatewayRadioButtons();
			this.tabContainer.setUpPaymentTabAddressRadioButtons();
			this.tabContainer.initSelectedPaymentGateway();

			jQuery( '#ship-to-different-address-checkbox' ).trigger( 'change' );

			jQuery( document.body ).one( 'updated_checkout', () => {
				/**
				 * Re-init Payment Gateways
				 */
				this.tabContainer.initSelectedPaymentGateway();
			} );

			/**
			 * On first load, we force updated_checkout to run for gateways
			 * that need it / want it / gotta have it
			 */
			this.force_updated_checkout = true;
			this.preserve_alerts = true;
			this.tabContainer.triggerUpdateCheckout();

			/**
			 * Now setup our triggers so that we don't double fire on page load
			 */
			this.tabContainer.setUpdateCheckoutTriggers();

			// Init checkout ( WooCommerce native event )
			jQuery( document.body ).trigger( 'init_checkout' );
		});

		// After setup event
		window.dispatchEvent( new CustomEvent( 'cfw-main-after-setup', { detail: { main: this } }) );
	}

	errorObserverWatch() {
		// Select the node that will be observed for mutations
		let targetNode = jQuery('form').get(0);

		// Options for the observer ( which mutations to observe )
		let config = { childList: true, characterData: true, subtree: true };

		if ( ! this.errorObserver ) {
			// Create an observer instance linked to the callback function
			let observer = new MutationObserver(( mutationsList ) => this.errorMutationListener( mutationsList ));

			// Start observing the target node for configured mutations
			observer.observe( targetNode, config );

			this.errorObserver = observer;
		}
	}

	/**
	 * @param mutationsList
	 */
	errorMutationListener( mutationsList ) {
		let ignoreList = (<any>window).errorObserverIgnoreList;

		if( jQuery( '#cfw-payment-method:visible' ).length > 0 ) {
			for ( let mutation of mutationsList ) {
				if ( mutation.type === 'childList' ) {
					let addedNodes = mutation.addedNodes;
					let $errorNode: any = null;

					Array.from( addedNodes ).forEach( node => {
						let $node: any = jQuery( node );
						let hasClass: boolean = $node.hasClass( 'woocommerce-error' );
						let hasGroupCheckoutClass: boolean = $node.hasClass( 'woocommerce-NoticeGroup-checkout' );

						if ( hasClass || hasGroupCheckoutClass ) {
							if( ignoreList.indexOf( $node.text() ) == -1 ) {
								Main.removeOverlay();
								$errorNode = $node;
								$errorNode.attr( 'class','' );
							}
						}
					});

					if ($errorNode ) {
						let alertInfo: AlertInfo = {
							type: "error",
							message: $errorNode,
							cssClass: "cfw-alert-error"
						};

						let alert: Alert = new Alert( Main.instance.alertContainer, alertInfo );
						alert.addAlert();

						CompleteOrderAction.initCompleteOrder = false;
					}
				}
			}
		}
	}

	/**
	 * Adds a visual indicator that the checkout is doing something
	 */
	static addOverlay(): void {
		if( jQuery( '#cfw-payment-method:visible' ).length > 0) {
			jQuery( 'body' ).addClass( 'show-overlay' );
		}
	}

	/**
	 * Remove the visual indicator
	 */
	static removeOverlay(): void {
		jQuery( 'body' ).removeClass( 'show-overlay' );
	}

	/**
	 * Sets up animation listeners
	 */
	setupAnimationListeners(): void {
		jQuery( '#cfw-ci-login' ).on( 'click', function(){
			jQuery( '#cfw-login-slide' ).addClass( 'stay-open' ).slideDown(300);
            jQuery( '#createaccount' ).prop( 'checked', false );
		});
	}

	/**
	 * @returns {any}
	 */
	get checkoutForm(): any {
		return this._checkoutForm;
	}

	/**
	 * @param {any} value
	 */
	set checkoutForm( value: any ) {
		this._checkoutForm = value;
	}

	/**
	 * @returns {TabContainer}
	 */
	get tabContainer() {
		return this._tabContainer;
	}

    /**
	 * @return {any}
     */
    get alertContainer(): any {
        return this._alertContainer;
    }

    /**
     * @param {any} value
     */
    set alertContainer( value: any ) {
        this._alertContainer = value;
    }

    /**
	 * @param value
	 */
	set tabContainer( value: TabContainer ) {
		this._tabContainer = value;
	}

	/**
	 * @returns {AjaxInfo}
	 */
	get ajaxInfo(): AjaxInfo {
		return this._ajaxInfo;
	}

	/**
	 * @param value
	 */
	set ajaxInfo( value: AjaxInfo ) {
		this._ajaxInfo = value;
	}

	/**
	 * @returns {any}
	 */
	get settings(): any {
		return this._settings;
	}

	/**
	 * @param value
	 */
	set settings( value: any ) {
		this._settings = value;
	}

	/**
	 * @returns {ParsleyService}
	 */
	get parsleyService(): ParsleyService {
		return this._parsleyService;
	}

	/**
	 * @param {ParsleyService} value
	 */
	set parsleyService( value: ParsleyService ) {
		this._parsleyService = value;
	}

	/**
	 * @returns {EasyTabService}
	 */
	get easyTabService(): EasyTabService {
		return this._easyTabService;
	}

	/**
	 * @param {EasyTabService} value
	 */
	set easyTabService( value: EasyTabService ) {
		this._easyTabService = value;
	}

	/**
	 * @returns {ValidationService}
	 */
	get validationService(): ValidationService {
		return this._validationService;
	}

	/**
	 * @param {ValidationService} value
	 */
	set validationService( value: ValidationService ) {
		this._validationService = value;
	}

	/**
	 * @returns {ZipAutocompleteService}
	 */
	get zipAutocompleteService(): ZipAutocompleteService {
		return this._zipAutocompleteService;
	}

	/**
	 * @param {ZipAutocompleteService} value
	 */
	set zipAutocompleteService( value: ZipAutocompleteService ) {
		this._zipAutocompleteService = value;
	}

	get addressAutocompleteService(): AddressAutocompleteService {
		return this._addressAutocompleteService;
	}

	set addressAutocompleteService(value: AddressAutocompleteService) {
		this._addressAutocompleteService = value;
	}

	/**
	 * @returns {MutationObserver}
	 */
	get errorObserver(): MutationObserver {
		return this._errorObserver;
	}

	/**
	 * @param {MutationObserver} value
	 */
	set errorObserver( value: MutationObserver ) {
		this._errorObserver = value;
	}

	/**
	 * @returns {boolean}
	 */
	get force_updated_checkout(): boolean {
		return this._force_updated_checkout;
	}

	/**
	 * @param {boolean} value
	 */
	set force_updated_checkout( value: boolean ) {
		this._force_updated_checkout = value;
	}

	/**
	 * @returns {boolean}
	 */
	get preserve_alerts(): boolean {
		return this._preserve_alerts;
	}

	/**
	 * @param {boolean} value
	 */
	set preserve_alerts( value: boolean ) {
		this._preserve_alerts = value;
	}

	get updateCheckoutTimer(): any {
		return this._updateCheckoutTimer;
	}

	set updateCheckoutTimer( value: any ) {
		this._updateCheckoutTimer = value;
	}

	/**
	 * @returns {Main}
	 */
	static get instance(): Main {
		return Main._instance;
	}

	/**
	 * @param {Main} value
	 */
	static set instance( value: Main ) {
		if(!Main._instance ) {
			Main._instance = value;
		}
	}
}