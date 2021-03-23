import 'ts-polyfill';
import { Main }                             from "./front/CFW/Main";
import { TabContainer }                     from "./front/CFW/Elements/TabContainer";
import { TabContainerBreadcrumb }           from "./front/CFW/Elements/TabContainerBreadcrumb";
import { TabContainerSection }              from "./front/CFW/Elements/TabContainerSection";
import { CompatibilityClasses }             from "./compatibility-classes";

/**
 * This is our main kick off file. We used to do this in a require block in the Redirect file but since we've moved to
 * webpack this is the new lay of the land ( commonjs ). In order to make this work in a non node setup ( wordpress ) we need
 * a reliable way to quick off the code hidden behind commonjs module loading. In comes our custom event.
 *
 * Basically we take all our code we have access to and need to run on start up and pass it the variables via a custom
 * event in the Redirect file. This allows us to get all our PHP needed variables, and lets us keep our kick off file
 * as a typescript file
 *
 * @type {Window}
 */

declare var jQuery: any;
declare var cfwEventData: any;
(<any>window).CompatibilityClasses = CompatibilityClasses;
(<any>window).errorObserverIgnoreList = [];

// Fired from compatibility-classes.ts
jQuery(document).ready( function() {
	let data = cfwEventData;
	let checkoutFormEl = jQuery( data.elements.checkoutFormSelector );
	let easyTabsWrapEl = jQuery( data.elements.easyTabsWrapElClass );
	let breadCrumbEl = jQuery( data.elements.breadCrumbElId );
	let customerInfoEl = jQuery( data.elements.customerInfoElId );
	let shippingMethodEl = jQuery( data.elements.shippingMethodElId );
	let paymentMethodEl = jQuery( data.elements.paymentMethodElId );
	let alertContainerEl = jQuery( data.elements.alertContainerId );
	let tabContainerEl = jQuery( data.elements.tabContainerElId );

	// Allow users to add their own Typescript Compatibility classes
	window.dispatchEvent( new CustomEvent( 'cfw-add-user-compatibility-definitions' ));

	let tabContainerBreadcrumb = new TabContainerBreadcrumb( breadCrumbEl );
	let tabContainerSections = [
		new TabContainerSection( customerInfoEl, 'customer_info' ),
		new TabContainerSection( shippingMethodEl, 'shipping_method' ),
		new TabContainerSection( paymentMethodEl, 'payment_method' )
	];
	let tabContainer = new TabContainer( tabContainerEl, tabContainerBreadcrumb, tabContainerSections );

	let main = new Main( checkoutFormEl, easyTabsWrapEl, alertContainerEl, tabContainer, data.ajaxInfo, data.settings, data.compatibility );
	main.setup();
} );