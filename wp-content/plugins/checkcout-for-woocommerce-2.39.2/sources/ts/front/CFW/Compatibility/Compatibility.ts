import {Main} from "../Main";

export abstract class Compatibility {
	private _name;

	/**
	 * @param {Main} main The Main object
	 * @param {any} params Params for the child class to run on load
	 * @param {boolean} load Should load be fired on instantiation
	 * @param {string} name The name of the compatibility module
	 */
	protected constructor( main: Main, params, load: boolean = true, name: string ) {
		this.name = name;

		if ( load ) {
			this.load( main, params );
			console.log( 'CheckoutWC Compatibility Module Loaded: ' + this.name );
		}
	}

	/**
	 * Literally anything function. Runs user code.
	 *
	 * @param {Main} main The Main object
	 * @param {any} params Params for the child class to run on load
	 */
	abstract load( main: Main, params ): void;

	get name() {
		return this._name;
	}

	set name(value) {
		this._name = value;
	}
}