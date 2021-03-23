import { Main }                             from "../Main";
import { TabContainerSection }              from "../Elements/TabContainerSection";
import { TabContainer }                     from "../Elements/TabContainer";
import { TabContainerBreadcrumb } 			from "../Elements/TabContainerBreadcrumb";

declare let jQuery: any;

/**
 * EzTab Enum
 */
export enum EasyTab {
    CUSTOMER,
    SHIPPING,
    PAYMENT,
}

/**
 * Easy tab Direction Object Blueprint
 */
export type EasyTabDirection = { current: EasyTab, target: EasyTab };

/**
 *
 */
export class EasyTabService {

    /**
     * @type {any}
     * @private
     */
    private _easyTabsWrap: any;

	/**
     * @type {boolean}
     * @private
	 */
	private _isDisplayed: boolean = true;

    /**
     * @param easyTabsWrap
     */
    constructor( easyTabsWrap: any ) {
       this.easyTabsWrap = easyTabsWrap;
    }

    /**
     * Returns the current and target tab indexes
     *
     * @param target
     * @returns {EasyTabDirection}
     */
    static getTabDirection( target ): EasyTabDirection {
        let currentTabIndex: number = 0;
        let targetTabIndex: number = 0;

        Main.instance.tabContainer.tabContainerSections.forEach(( tab: TabContainerSection, index: number ) => {
            let $tab: any = tab.jel;

            if($tab.filter( ':visible' ).length !== 0) {
                currentTabIndex = index;
            }

            if($tab.is( jQuery( target ))) {
                targetTabIndex = index;
            }
        });

        return <EasyTabDirection>{ current: currentTabIndex, target: targetTabIndex };
    }

    /**
     *
     */
    initialize( breadcrumb: TabContainerBreadcrumb ) {
        if ( this.isDisplayed ) {
			this.easyTabsWrap.easytabs({
				defaultTab: 'li.tab#default-tab',
				tabs: 'ul > li.tab'
			});

			this.easyTabsWrap.removeClass( 'cfw-tabs-not-initialized' );

			breadcrumb.show();

            this.easyTabsWrap.bind( 'easytabs:after', ( event, clicked, target ) => {
                // Scroll to the top of current tab on tab switch
                jQuery( 'html, body' ).animate( {
                    scrollTop: jQuery( '#cfw-tab-container' ).offset().top
                }, 300 );

                // Add a class to checkout form to indicate payment tab is active tab
                // Doesn't work when tab is initialized by hash in URL
                let easyTabDirection: EasyTabDirection = EasyTabService.getTabDirection( target );
                let current_tab_id = EasyTabService.getTabId( easyTabDirection.target );

                this.setActiveTabClass( current_tab_id + '-active' );

                // Remove temporary alerts on successful tab switch
                Main.instance.alertContainer.find( '.cfw-alert-temporary' ).remove();
            } );

            // Add payment tab active class on window load
            jQuery( window ).on( 'load cfw_updated_checkout', () => {
                let hash = window.location.hash;

                if ( hash ) {
                    this.setActiveTabClass( hash.replace( '#', '' ) + '-active' );
                } else {
                    this.setActiveTabClass( 'cfw-customer-info-active' );
                }
            } );

            jQuery( document.body ).on( 'click', '.cfw-tab-link, .cfw-next-tab, .cfw-prev-tab', ( event ) => {
                if ( jQuery( event.target ).data('tab') ) {
                    this.easyTabsWrap.easytabs('select', jQuery( event.target ).data('tab') );
                }
            } );
		} else {
        	breadcrumb.hide();
		}
    }

    /**
     *
     * @param active_class any
     */
    setActiveTabClass( active_class: any ) {
        let main = Main.instance;
        let checkout_form = main.checkoutForm;

        checkout_form.removeClass( `cfw-customer-info-active` ).removeClass( `cfw-shipping-method-active` ).removeClass( `cfw-payment-method-active` ).addClass( active_class );
    }

    /**
     * @param {EasyTab} tab
     */
    static go( tab: EasyTab ): void {
        Main.instance.easyTabService.easyTabsWrap.easytabs( 'select', EasyTabService.getTabId( tab ));
    }

    /**
     * Returns the id of the tab passed in
     *
     * @param {EasyTab} tab
     * @returns {string}
     */
    static getTabId( tab: EasyTab ): string {
        let tabContainer: TabContainer = Main.instance.tabContainer;
        let easyTabs: Array<TabContainerSection> = tabContainer.tabContainerSections;

        return easyTabs[tab].jel.attr( 'id' );
    }

	/**
	 * @return {any}
	 */
	get easyTabsWrap(): any {
        return this._easyTabsWrap;
    }

	/**
	 * @param {any} value
	 */
	set easyTabsWrap( value: any ) {
        this._easyTabsWrap = value;
    }

	/**
	 * @return {boolean}
	 */
	get isDisplayed(): boolean {
		return this._isDisplayed;
	}

	/**
	 * @param {any} value
	 */
	set isDisplayed( value: boolean ) {
		this._isDisplayed = value;
	}
}