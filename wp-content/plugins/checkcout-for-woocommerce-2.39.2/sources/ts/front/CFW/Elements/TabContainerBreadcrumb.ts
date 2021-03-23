import { Element } from "./Element";

/**
 *
 */
export class TabContainerBreadcrumb extends Element {

    /**
     *
     * @param jel
     */
    constructor( jel: any ) {
        super( jel );
    }

	/**
     * Hides the breadcrumb
	 */
	hide(): void {
        this.jel.hide();
    }

	/**
     * Shows the breadcrumb
	 */
	show(): void {
        this.jel.show();
    }
}