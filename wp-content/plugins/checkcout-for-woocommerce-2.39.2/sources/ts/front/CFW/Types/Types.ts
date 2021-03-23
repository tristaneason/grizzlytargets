import { LabelType }                from "../Enums/LabelType";

export type EventCallback = { eventName: string, func: Function, target: any };
export type InputLabelType = { type: LabelType, cssClass: string };
export type AjaxInfo = { url: string };
export type AccountExistsData = { "wc-ajax": string, email: string };
export type LogInData = { "wc-ajax": string, email: string, password: string };
export type PaymentMethodData = { "wc-ajax": string, "payment_method": string };

export type CompatibilityClassOptions = {
	class: string,
	params: any,
	event: CompatibilityClassOptionsEvent,
	fireLoad: boolean
}

export type CompatibilityClassOptionsEvent = "before-setup" | "after-setup";
