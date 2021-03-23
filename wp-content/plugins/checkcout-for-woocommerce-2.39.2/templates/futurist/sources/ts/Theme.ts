export class Theme {

    /**
     * @type {any}
     * @private
     */
    private _cfwEventData: any;

    /**
     * @param cfwEventData
     */
    constructor(cfwEventData: any) {
        this.cfwEventData = cfwEventData;

        this.run();
    }

    /**
     *
     */
    public run() {
        this.circleFillOnTabSwitch();
    }

    /**
     *
     */
    public circleFillOnTabSwitch() {
        let easyTabsWrap = $(this.cfwEventData.elements.easyTabsWrapElClass);
        let breadcrumb = $(this.cfwEventData.elements.breadCrumbElId);
        let breadcrumbLi = breadcrumb.children("li");

        // On EasyTabs after
        easyTabsWrap.on('easytabs:after', () => {
            this.circleFill(breadcrumb, breadcrumbLi);
        });

        // On page load
        this.circleFill(breadcrumb, breadcrumbLi)
    }

    public circleFill(breadcrumb, breadcrumbLi) {
        let breadcrumbLiActive = breadcrumb.find("li.active");
        const filledCircleClass = "filled-circle";

        breadcrumbLi.each((index, el) => {
            let activeNum = breadcrumbLiActive.data("breadcrumbNumber");
            el = $(el);

            if ((index + 1) <= activeNum) {
                el.addClass(filledCircleClass);
            } else {
                el.removeClass(filledCircleClass);
            }
        });
    }

    /**
     * @return {any}
     */
    get cfwEventData(): any {
        return this._cfwEventData;
    }

    /**
     * @param value
     */
    set cfwEventData(value: any) {
        this._cfwEventData = value;
    }
}

// JQuery is already loaded on the page
declare let jQuery: any;

let $ = jQuery;

$(document).ready(() => {
    let theme = new Theme((<any>window).cfwEventData);
});