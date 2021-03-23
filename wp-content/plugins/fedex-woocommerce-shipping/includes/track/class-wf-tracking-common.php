<?php

class ShipmentSource {
	public $shipment_id_cs;
	public $shipping_service;
	public $order_date;
	public $shipping_postcode;
	public $trigger_api = false;
}

class ShipmentResult {
	public $message;
	public $tracking_info_obj_array;
	public $tracking_info_api_obj_array;
}

class TrackingInfo {
	public $tracking_id;
	public $tracking_link;
}

class TrackingInfoApi {
	public $tracking_id;
	public $tracking_link;
	public $api_tracking_status;
	public $api_tracking_error;
}

class ApiTracking {
	public $status;
	public $error;
}

