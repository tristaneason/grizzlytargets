/**
 *
 */
export class Element {

    /**
     * @type {any}
     * @protected
     */
    protected _jel: any;

    /**
     * @param jel
     */
    constructor( jel: any ) {
        this.jel = jel;
    }

    /**
     * @returns {any}
     */
    get jel(): any {
        return this._jel;
    }

    /**
     * @param value
     */
    set jel( value: any ) {
        this._jel = value;
    }
}