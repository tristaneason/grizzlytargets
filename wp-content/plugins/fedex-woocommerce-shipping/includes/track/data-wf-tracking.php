<?php
/**
 * Shipping services and tracking related data.
 */
return array(
	'wf_usps' => array(
		'name'  => 'United States Postal Service (USPS)',
		'tracking_url' => 'https://tools.usps.com/go/TrackConfirmAction_input?qtc_tLabels1=',
		'api_url' => 'http://production.shippingapis.com/ShippingAPI.dll'
	),
	'wf_ups' => array(
		'name'  => 'UPS',
		'tracking_url' => 'http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=',
		'api_url' => ''
	),
	'wf_canada_post' => array(
		'name'  => 'Canada Post',
		'tracking_url' => 'http://www.canadapost.ca/cpotools/apps/track/personal/findByTrackNumber?trackingNumber=',
		'api_url' => 'https://soa-gw.canadapost.ca/rs/'
	),
	'wf_fedex' => array(
		'name'  => 'FedEx',
		'tracking_url' => 'https://www.fedex.com/apps/fedextrack/?action=track&trackingnumber=',
		'api_url' => ''
	),
	'wf_dhl_express' => array(
		'name'  => 'DHL Express',
		'tracking_url' => 'http://www.dhl.com/en/express/tracking.html?AWB=',
		'api_url' => ''
	),
	'wf_dhl_usa' => array(
		'name'  => 'DHL USA',
		'tracking_url' => 'http://www.dhl-usa.com/content/us/en/express/tracking.shtml?brand=DHL&AWB=',
		'api_url' => ''
	),
	'wf_dhl_global' => array(
		'name'  => 'DHL Global',
		'tracking_url' => 'http://webtrack.dhlglobalmail.com/?mobile=&trackingnumber=',
		'api_url' => ''
	),
	'wf_ontrac' => array(
		'name'  => 'OnTrac',
		'tracking_url' => 'http://www.ontrac.com/trackingdetail.asp?tracking=',
		'api_url' => ''
	),
	'wf_icc_world' => array(
		'name'  => 'ICC World',
		'tracking_url' => 'http://iccworld.com/track.asp?txtawbno=',
		'api_url' => ''
	),
	'wf_royal_mail' => array(
		'name'  => 'Royal Mail',
		'tracking_url' => 'https://www.royalmail.com/track-your-item?trackNumber=',
		'api_url' => ''
	),
	'wf_parcel_force' => array(
		'name'  => 'Parcel Force',
		'tracking_url' => 'http://www.parcelforce.com/track-trace?trackNumber=',
		'api_url' => ''
	),
	'wf_tnt_cons' => array(
		'name'  => 'TNT (Consignment)',
		'tracking_url' => 'http://www.tnt.com/webtracker/tracking.do?requestType=GEN&searchType=CON&respLang=en&respCountry=GENERIC&sourceID=1&sourceCountry=ww&cons=',
		'api_url' => ''
	),
	'wf_tnt_ref' => array(
		'name'  => 'TNT (Reference)',
		'tracking_url' => 'http://www.tnt.com/webtracker/tracking.do?requestType=GEN&searchType=REF&respLang=en&respCountry=GENERIC&sourceID=1&sourceCountry=ww&cons=',
		'api_url' => ''
	),
	'wf_yrc_freight' => array(
		'name'  => 'YRC Freight',
		'tracking_url' => 'https://my.yrc.com/tools/#/track/shipments?endDate=&referenceNumberType=PRO&referenceNumber=',
		'api_url' => ''
	),
	'wf_yrc_regional' => array(
		'name'  => 'YRC Regional',
		'tracking_url' => 'http://www.usfc.com/shipmentStatus/track.do?proNumber=',
		'api_url' => ''
	),
	'wf_db_schenker' => array(
		'name'  => 'DB Schenker',
		'tracking_url' => 'https://eschenker.dbschenker.com/nges-portal/public/en-US_US/#!/tracking/schenker-search?refNumber=',
		'api_url' => ''
	),
	'wf_roadrunner' => array(
		'name'  => 'Roadrunner',
		'tracking_url' => 'https://www.rrts.com/Tools/Tracking/Pages/MultipleResults.aspx?PROS=',
		'api_url' => ''
	),
	'wf_dpd_de' => array(
		'name'  => 'dpd (DE)',
		'tracking_url' => 'https://tracking.dpd.de/parcelstatus?locale=en_D2&query=',
		'api_url' => ''
	),
	'wf_aramex' => array(
		'name'  => 'Aramex',
		'tracking_url' => 'https://www.aramex.com/track-results-multiple.aspx?ShipmentNumber=',
		'api_url' => ''
	),
	'wf_dsv' => array(
		'name'  => 'DSV',
		'tracking_url' => 'https://www.tracktrace.dsv.com/newtracking/public/PublicSearch.spr?mode=reference&action=directSearch&sid=',
		'api_url' => ''
	),
	'wf_canpar' => array(
		'name'  => 'Canpar Courier',
		'tracking_url' => 'https://www.canpar.ca/en/track/TrackingAction.do?locale=en&type=0&reference=',
		'api_url' => ''
	),
	'wf_purolator' => array(
		'name'  => 'Purolator',
		'tracking_url' => 'http://shipnow.purolator.com/shiponline/track/purolatortrack.asp?pinno=',
		'api_url' => ''
	),
 	'wf_asendia_usa' => array(
		'name'  => 'ASENDIA (USA)',
		'tracking_url' => 'http://tracking.asendiausa.com/t.aspx?p=',
		'api_url' => ''
	),
	'wf_lasership' => array(
		'name'  => 'LaserShip',
		'tracking_url' => 'http://http://www.lasership.com/track/',
		'api_url' => ''
	),
	'wf_i_parcel_ups' => array(
		'name'  => 'i-parcel (UPS)',
		'tracking_url' => 'https://tracking.i-parcel.com/Home/Index?trackingnumber=',
		'api_url' => ''
	),
	'wf_abfs' => array(
		'name'  => 'ABF.com',
		'tracking_url' => 'https://www.abfs.com/tools/trace/default.asp?hidSubmitted=Y&reftype0=A&refno0=',
		'api_url' => ''
	),
	'wf_estes_express' => array(
		'name'  => 'ESTES Express',
		'tracking_url' => 'http://http://www.estes-express.com/cgi-dta/edn419.mbr/output?search_criteria=',
		'api_url' => ''
	),
	'wf_rl_carriers' => array(
		'name'  => 'RL Carriers',
		'tracking_url' => 'www2.rlcarriers.com/freight/shipping/shipment-tracing?docType=PRO&pro=',
		'api_url' => ''
	),
	'wf_skynet_ww_ex' => array(
		'name'  => 'SkyNet Worldwide Express',
		'tracking_url' => 'http://www.crossroads.co.za/tracking2/tracking.aspx?type=way&wb=',
		'api_url' => ''
	),
	'wf_globegistics' => array(
		'name'  => 'Globegistics',
		'tracking_url' => 'http://dm.mytracking.net/GLOBEGISTICS/track/TrackDetails.aspx?t=',
		'api_url' => ''
	),
	'wf_odfl' => array(
		'name'  => 'Old Dominion',
		'tracking_url' => 'http://www.odfl.com/Trace/standardResult.faces?pro=',
		'api_url' => ''
	),
	'wf_saia' => array(
		'name'  => 'SAIA',
		'tracking_url' => 'http://www.saiasecure.com/tracing/b_manifest.asp?link=y&pro=',
		'api_url' => ''
	),
	'wf_cevalogistics' => array(
		'name'  => 'CEVA Logistics',
		'tracking_url' => 'http://www.cevalogistics.com/en-US/toolsresources/Pages/CEVATrak.aspx?sv=',
		'api_url' => ''
	),
	'wf_india_post' => array(
		'name'  => 'India Post',
		'tracking_url' => 'http://ipsweb.ptcmysore.gov.in/ipswebtracking/IPSWeb_item_events.asp?itemid=',
		'api_url' => ''
	),
	'wf_con_way' => array(
		'name'  => 'Con-Way Freight',
		'tracking_url' => 'http://www.con-way.com/webapp/manifestrpts_p_app/Tracking/TrackingRS.jsp?PRO=',
		'api_url' => ''
	),
	'wf_averitt_express' => array(
		'name'  => 'Averitt Express',
		'tracking_url' => 'https://www.averittexpress.com/trackLTLById.avrt?serviceType=LTL&resultsPageTitle=LTL+Tracking+by+PRO+and+BOL&trackPro=',
		'api_url' => ''
	),
	'wf_adrexo' => array(
		'name'  => 'Colis Prive (Adrexo)',
		'tracking_url' => 'https://www.colisprive.com/moncolis/pages/detailColis.aspx?numColis=',
		'api_url' => ''
	),
	'wf_freightquote' => array(
		'name'  => 'FreightQuote',
		'tracking_url' => 'http://www.freightquote.com/trackshipment.aspx?bol=',
		'api_url' => ''
	),
	'wf_correios' => array(
		'name'  => 'Correios',
		'tracking_url' => 'http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=',
		'api_url' => ''
	),
	'wf_the_professional' => array(
		'name'  => 'The Professional Couriers',
		'tracking_url' => 'http://www.tpcindia.com/Tracking2014.aspx?type=0&service=0&id=',
		'api_url' => ''
	),
	'wf_aus_post' => array(
		'name'  => 'Australian Post',
		'tracking_url' => 'http://auspost.com.au/track/track.html?id=',
		'api_url' => ''
	),
	'wf_japan_post' => array(
		'name'  => 'Japan Post',
		'tracking_url' => 'https://trackings.post.japanpost.jp/services/srv/search/direct?searchKind=S004&locale=en&x=24&y=11&reqCodeNo1=',
		'api_url' => ''
	),
	'wf_yodel_direct' => array(
		'name'  => 'Yodel Direct',
		'tracking_url' => 'https://www.yodeldirect.co.uk/tracking/',
		'api_url' => ''
	),
	'wf_collect_plus' => array(
		'name'  => 'Collect+',
		'tracking_url' => 'https://www.collectplus.co.uk/track/',
		'api_url' => ''
	),
	'wf_apc_overnight' => array(
		'name'  => 'APC Overnight',
		'tracking_url' => 'http://www.apc-overnight.com/apc/captcha.php?txtpostcode=&Track=Track&type=1&txtconno=',
		'api_url' => ''
	),
	'wf_interlink_express_1' => array(
		'name'  => 'Interlink Express (1)',
		'tracking_url' => 'http://www.interlinkexpress.com/apps/tracking/?reference=',
		'api_url' => ''
	),
	'wf_interlink_express_2' => array(
		'name'  => 'Interlink Express (2)',
		'tracking_url' => 'http://www.interlinkexpress.com/apps/tracking/?help_type=%2Fapps%2Ftracking%2F%23results&reference=[ID]&postcode=[PIN]',
		'api_url' => ''
	),
	'wf_uk_mail' => array(
		'name'  => 'UK Mail',
		'tracking_url' => 'https://www.ukmail.com/manage-my-delivery/manage-my-delivery?ctl00%24Content%24C001%24LO_01_txtConsignmentNo=',
		'api_url' => ''
	),
	'wf_hermesworld' => array(
		'name'  => 'Hermesworld',
		'tracking_url' => 'https://tracking.hermesworld.com/?TrackID=',
		'api_url' => ''
	),
	'wf_myhermes_uk' => array(
		'name'  => 'myHermes (UK)',
		'tracking_url' => 'https://www.myhermes.co.uk/tracking-results.html?tracking-widget-search-submit=Go&trackingNumber=',
		'api_url' => ''
	),
	'wf_fastway_couriers' => array(
		'name'  => 'Fastway Couriers Ireland',
		'tracking_url' => 'http://www.fastway.ie/courier-services/track-your-parcel?l=',
		'api_url' => ''
	),
	'wf_posti' => array(
		'name'  => 'Posti',
		'tracking_url' => 'http://www.posti.fi/itemtracking/posti/search_by_shipment_id?lang=en&ShipmentId=',
		'api_url' => ''
	),
	'wf_two_go' => array(
		'name'  => '2GO',
		'tracking_url' => 'http://supplychain.2go.com.ph/CustomerSupport/etrace/indiv1.asp?code=',
		'api_url' => ''
	),
	'wf_fedex_sameday' => array(
		'name'  => 'FedEx SameDay',
		'tracking_url' => 'https://www.fedexsameday.com/fdx_dotracking_ua.aspx?tracknum=',
		'api_url' => ''
	),
	'wf_postnord' => array(
		'name'  => 'Postnord',
		'tracking_url' => 'http://www.posten.se/sv/Kundservice/Sidor/Sok-brev-paket.aspx?search=',
		'api_url' => ''
	),
	'wf_pbt_couriers' => array(
		'name'  => 'PBT Couriers',
		'tracking_url' => 'http://www.pbt.com/nick/results.cfm?ticketNo=',
		'api_url' => ''
	),
	'wf_fastway_couriers' => array(
		'name'  => 'Fastway Couriers',
		'tracking_url' => 'http://www.fastway.co.nz/courier-services/track-your-parcel?l=',
		'api_url' => ''
	),
	'wf_nz_post' => array(
		'name'  => 'New Zealand Post',
		'tracking_url' => 'https://www.nzpost.co.nz/tools/tracking?trackid=',
		'api_url' => ''
	),
	'wf_courier_post' => array(
		'name'  => 'CourierPost',
		'tracking_url' => 'http://trackandtrace.courierpost.co.nz/Search/',
		'api_url' => ''
	),
	'wf_postnl' => array(
		'name'  => 'PostNL',
		'tracking_url' => 'https://jouw.postnl.nl/?ShowAnonymousLayover=False&CustomerServiceClaim=False/#!/track-en-trace/%251$s/NL/',
		'api_url' => ''
	),
	'wf_dpd_nl' => array(
		'name'  => 'DPD (NL)',
		'tracking_url' => 'http://track.dpdnl.nl/?parcelnumber=',
		'api_url' => ''
	),
	'wf_gojavas' => array(
		'name'  => 'Gojavas',
		'tracking_url' => 'http://gojavas.com/docket_details.php?pop=docno&docno=',
		'api_url' => ''
	),
	'wf_blue_dart' => array(
		'name'  => 'Blue Dart',
		'tracking_url' => 'http://www.bluedart.com/servlet/RoutingServlet?handler=tnt&action=awbquery&awb=awb&numbers=',
		'api_url' => ''
	),
	'wf_deutsche_post' => array(
		'name'  => 'Deutsche Post (DHL)',
		'tracking_url' => 'http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc=',
		'api_url' => ''
	),
	'wf_dhl_intraship' => array(
		'name'  => 'DHL Intraship (DE)',
		'tracking_url' => 'http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&rfn=&extendedSearch=true&idc=',
		'api_url' => ''
	),
	'wf_colissimo' => array(
		'name'  => 'Colissimo',
		'tracking_url' => 'http://www.colissimo.fr/portail_colissimo/suivre.do?language=fr_FR&colispart=',
		'api_url' => ''
	),
	'wf_dpd_cz' => array(
		'name'  => 'DPD (CZ)',
		'tracking_url' => 'https://tracking.dpd.de/parcelstatus?locale=cs_CZ&query=',
		'api_url' => ''
	),
	'wf_dhl_cz' => array(
		'name'  => 'DHL (CZ)',
		'tracking_url' => 'http://www.dhl.cz/cs/express/sledovani_zasilek.html?AWB=',
		'api_url' => ''
	),
	'wf_posta_cz' => array(
		'name'  => 'Posta (CZ)',
		'tracking_url' => 'https://www.postaonline.cz/trackandtrace/-/zasilka/cislo?parcelNumbers=',
		'api_url' => ''
	),
	'wf_ppl_cz' => array(
		'name'  => 'PPL (CZ)',
		'tracking_url' => 'https://www.ppl.cz/main2.aspx?cls=Package&idSearch=',
		'api_url' => ''
	),
	'wf_post_ag' => array(
		'name'  => 'Post AG',
		'tracking_url' => 'https://www.post.at/sendungsverfolgung.php?pnum1=',
		'api_url' => ''
	),
	'wf_postnl_02' => array(
		'name'  => 'PostNL (02)',
		'tracking_url' => 'https://jouw.postnl.nl/[ID]/track-en-trace/111111111/NL/[PIN]',
		'api_url' => ''
	),
	'wf_stamps_usps' => array(
		'name'  => 'Stamps.com (USPS)',
		'tracking_url' => 'http://www.stamps.com/shipstatus/?confirmation=',
		'api_url' => ''
	),
	'wf_ctt_expresso' => array(
		'name'  => 'CTT Expresso',
		'tracking_url' => 'http://www.cttexpresso.pt/feapl_2/app/open/objectSearch/cttexpresso_feapl_132col-cttexpressoObjectSearch.jspx?lang=01&pesqObjecto.objectoId=',
		'api_url' => ''
	),
	// 'wf_' => array(
		// 'name'  => '',
		// 'tracking_url' => '',
		// 'api_url' => ''
	// ),
);

 