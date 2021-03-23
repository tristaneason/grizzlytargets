<?php

/**
 * Schema object for: EstimateAddRq
 * 
 * @author "Keith Palmer Jr." <Keith@ConsoliByte.com>
 * @license LICENSE.txt
 * 
 * @package QuickBooks
 * @subpackage QBXML
 */

/**
 * 
 */
require_once 'QuickBooks.php';

/**
 * 
 */
require_once 'QuickBooks/QBXML/Schema/Object.php';

/**
 * 
 */
class QuickBooks_QBXML_Schema_Object_EstimateAddRq extends QuickBooks_QBXML_Schema_Object
{
	protected function &_qbxmlWrapper()
	{
		static $wrapper = 'EstimateAdd';
		
		return $wrapper;
	}
	
	protected function &_dataTypePaths()
	{
		static $paths = array (
  'CustomerRef ListID' => 'IDTYPE',
  'CustomerRef FullName' => 'STRTYPE',
  'ClassRef ListID' => 'IDTYPE',
  'ClassRef FullName' => 'STRTYPE',
  'TemplateRef ListID' => 'IDTYPE',
  'TemplateRef FullName' => 'STRTYPE',
  'TxnDate' => 'DATETYPE',
  'RefNumber' => 'STRTYPE',
  'BillAddress Addr1' => 'STRTYPE',
  'BillAddress Addr2' => 'STRTYPE',
  'BillAddress Addr3' => 'STRTYPE',
  'BillAddress Addr4' => 'STRTYPE',
  'BillAddress Addr5' => 'STRTYPE',
  'BillAddress City' => 'STRTYPE',
  'BillAddress State' => 'STRTYPE',
  'BillAddress Province' => 'STRTYPE', 
  'BillAddress PostalCode' => 'STRTYPE',
  'BillAddress Country' => 'STRTYPE',
  'BillAddress Note' => 'STRTYPE',
  'ShipAddress Addr1' => 'STRTYPE',
  'ShipAddress Addr2' => 'STRTYPE',
  'ShipAddress Addr3' => 'STRTYPE',
  'ShipAddress Addr4' => 'STRTYPE',
  'ShipAddress Addr5' => 'STRTYPE',
  'ShipAddress City' => 'STRTYPE',
  'ShipAddress State' => 'STRTYPE',
  'ShipAddress Province' => 'STRTYPE', 
  'ShipAddress PostalCode' => 'STRTYPE',
  'ShipAddress Country' => 'STRTYPE',
  'ShipAddress Note' => 'STRTYPE',
  'IsActive' => 'BOOLTYPE',
  'PONumber' => 'STRTYPE',
  'TermsRef ListID' => 'IDTYPE',
  'TermsRef FullName' => 'STRTYPE',
  'DueDate' => 'DATETYPE',
  'SalesRepRef ListID' => 'IDTYPE',
  'SalesRepRef FullName' => 'STRTYPE',
  'FOB' => 'STRTYPE',
  'ItemSalesTaxRef ListID' => 'IDTYPE',
  'ItemSalesTaxRef FullName' => 'STRTYPE',
  'Memo' => 'STRTYPE',
  'CustomerMsgRef ListID' => 'IDTYPE',
  'CustomerMsgRef FullName' => 'STRTYPE',
  'IsToBeEmailed' => 'BOOLTYPE',
  'IsTaxIncluded' => 'BOOLTYPE',
  'CustomerSalesTaxCodeRef ListID' => 'IDTYPE',
  'CustomerSalesTaxCodeRef FullName' => 'STRTYPE',
  'Other' => 'STRTYPE',
  'EstimateLineAdd ItemRef ListID' => 'IDTYPE',
  'EstimateLineAdd ItemRef FullName' => 'STRTYPE',
  'EstimateLineAdd Desc' => 'STRTYPE',
  'EstimateLineAdd Quantity' => 'QUANTYPE',
  'EstimateLineAdd UnitOfMeasure' => 'STRTYPE',
  'EstimateLineAdd Rate' => 'PRICETYPE',
  'EstimateLineAdd RatePercent' => 'PERCENTTYPE',
  'EstimateLineAdd ClassRef ListID' => 'IDTYPE',
  'EstimateLineAdd ClassRef FullName' => 'STRTYPE',
  'EstimateLineAdd Amount' => 'AMTTYPE',
  'EstimateLineAdd InventorySiteRef ListID' => 'IDTYPE', 
  'EstimateLineAdd InventorySiteRef FullName' => 'STRTYPE', 
  'EstimateLineAdd SalesTaxCodeRef ListID' => 'IDTYPE',
  'EstimateLineAdd SalesTaxCodeRef FullName' => 'STRTYPE',
  'EstimateLineAdd MarkupRate' => 'PRICETYPE',
  'EstimateLineAdd MarkupRatePercent' => 'PERCENTTYPE',
  'EstimateLineAdd PriceLevelRef ListID' => 'IDTYPE',
  'EstimateLineAdd PriceLevelRef FullName' => 'STRTYPE',
  'EstimateLineAdd OverrideItemAccountRef ListID' => 'IDTYPE',
  'EstimateLineAdd OverrideItemAccountRef FullName' => 'STRTYPE',
  'EstimateLineAdd Other1' => 'STRTYPE',
  'EstimateLineAdd Other2' => 'STRTYPE',
  'EstimateLineAdd LotNumber' => 'STRTYPE',
  'EstimateLineAdd DataExt OwnerID' => 'GUIDTYPE',
  'EstimateLineAdd DataExt DataExtName' => 'STRTYPE',
  'EstimateLineAdd DataExt DataExtValue' => 'STRTYPE',
  'EstimateLineGroupAdd ItemGroupRef ListID' => 'IDTYPE',
  'EstimateLineGroupAdd ItemGroupRef FullName' => 'STRTYPE',
  'EstimateLineGroupAdd Desc' => 'STRTYPE',
  'EstimateLineGroupAdd Quantity' => 'QUANTYPE',
  'EstimateLineGroupAdd UnitOfMeasure' => 'STRTYPE',
  'EstimateLineGroupAdd DataExt OwnerID' => 'GUIDTYPE',
  'EstimateLineGroupAdd DataExt DataExtName' => 'STRTYPE',
  'EstimateLineGroupAdd DataExt DataExtValue' => 'STRTYPE',
  'IncludeRetElement' => 'STRTYPE',
);
		
		return $paths;
	}
	
	protected function &_maxLengthPaths()
	{
		static $paths = array (
  'CustomerRef ListID' => 0,
  'CustomerRef FullName' => 209,
  'ClassRef ListID' => 0,
  'ClassRef FullName' => 209,
  'TemplateRef ListID' => 0,
  'TemplateRef FullName' => 209,
  'TxnDate' => 0,
  'RefNumber' => 11,
  'BillAddress Addr1' => 41,
  'BillAddress Addr2' => 41,
  'BillAddress Addr3' => 41,
  'BillAddress Addr4' => 41,
  'BillAddress Addr5' => 41,
  'BillAddress City' => 31,
  'BillAddress State' => 21,
  'BillAddress Province' => 21, 
  'BillAddress PostalCode' => 13,
  'BillAddress Country' => 31,
  'BillAddress Note' => 41,
  'ShipAddress Addr1' => 41,
  'ShipAddress Addr2' => 41,
  'ShipAddress Addr3' => 41,
  'ShipAddress Addr4' => 41,
  'ShipAddress Addr5' => 41,
  'ShipAddress City' => 31,
  'ShipAddress State' => 21,
  'ShipAddress Province' => 21, 
  'ShipAddress PostalCode' => 13,
  'ShipAddress Country' => 31,
  'ShipAddress Note' => 41,
  'IsActive' => 0,
  'PONumber' => 25,
  'TermsRef ListID' => 0,
  'TermsRef FullName' => 209,
  'DueDate' => 0,
  'SalesRepRef ListID' => 0,
  'SalesRepRef FullName' => 209,
  'FOB' => 13,
  'ItemSalesTaxRef ListID' => 0,
  'ItemSalesTaxRef FullName' => 209,
  'Memo' => 4095,
  'CustomerMsgRef ListID' => 0,
  'CustomerMsgRef FullName' => 209,
  'IsToBeEmailed' => 0,
  'IsTaxIncluded' => 0,
  'CustomerSalesTaxCodeRef ListID' => 0,
  'CustomerSalesTaxCodeRef FullName' => 209,
  'Other' => 29,
  'EstimateLineAdd ItemRef ListID' => 0,
  'EstimateLineAdd ItemRef FullName' => 209,
  'EstimateLineAdd Desc' => 4095,
  'EstimateLineAdd Quantity' => 0,
  'EstimateLineAdd UnitOfMeasure' => 31,
  'EstimateLineAdd Rate' => 0,
  'EstimateLineAdd RatePercent' => 0,
  'EstimateLineAdd ClassRef ListID' => 0,
  'EstimateLineAdd ClassRef FullName' => 209,
  'EstimateLineAdd Amount' => 0,
  'EstimateLineAdd InventorySiteRef ListID' => 0,
  'EstimateLineAdd InventorySiteRef FullName' => 209,
  'EstimateLineAdd SalesTaxCodeRef ListID' => 0,
  'EstimateLineAdd SalesTaxCodeRef FullName' => 209,
  'EstimateLineAdd MarkupRate' => 0,
  'EstimateLineAdd MarkupRatePercent' => 0,
  'EstimateLineAdd PriceLevelRef ListID' => 0,
  'EstimateLineAdd PriceLevelRef FullName' => 209,
  'EstimateLineAdd OverrideItemAccountRef ListID' => 0,
  'EstimateLineAdd OverrideItemAccountRef FullName' => 209,
  'EstimateLineAdd Other1' => 29,
  'EstimateLineAdd Other2' => 29,
  'EstimateLineAdd LotNumber' => 29,
  'EstimateLineAdd DataExt OwnerID' => 0,
  'EstimateLineAdd DataExt DataExtName' => 31,
  'EstimateLineAdd DataExt DataExtValue' => 0,
  'EstimateLineGroupAdd ItemGroupRef ListID' => 0,
  'EstimateLineGroupAdd ItemGroupRef FullName' => 209,
  'EstimateLineGroupAdd Desc' => 4095,
  'EstimateLineGroupAdd Quantity' => 0,
  'EstimateLineGroupAdd UnitOfMeasure' => 31,
  'EstimateLineGroupAdd DataExt OwnerID' => 0,
  'EstimateLineGroupAdd DataExt DataExtName' => 31,
  'EstimateLineGroupAdd DataExt DataExtValue' => 0,
  'IncludeRetElement' => 50,
);
		
		return $paths;
	}
	
	protected function &_isOptionalPaths()
	{
		static $paths = array (
  'CustomerRef ListID' => true,
  'CustomerRef FullName' => true,
  'ClassRef ListID' => true,
  'ClassRef FullName' => true,
  'TemplateRef ListID' => true,
  'TemplateRef FullName' => true,
  'TxnDate' => true,
  'RefNumber' => true,
  'BillAddress Addr1' => true,
  'BillAddress Addr2' => true,
  'BillAddress Addr3' => true,
  'BillAddress Addr4' => true,
  'BillAddress Addr5' => true,
  'BillAddress City' => true,
  'BillAddress State' => true,
  'BillAddress Province' => true, 
  'BillAddress PostalCode' => true,
  'BillAddress Country' => true,
  'BillAddress Note' => true,
  'ShipAddress Addr1' => true,
  'ShipAddress Addr2' => true,
  'ShipAddress Addr3' => true,
  'ShipAddress Addr4' => true,
  'ShipAddress Addr5' => true,
  'ShipAddress City' => true,
  'ShipAddress State' => true,
  'ShipAddress Province' => true, 
  'ShipAddress PostalCode' => true,
  'ShipAddress Country' => true,
  'ShipAddress Note' => true,
  'IsActive' => true,
  'PONumber' => true,
  'TermsRef ListID' => true,
  'TermsRef FullName' => true,
  'DueDate' => true,
  'SalesRepRef ListID' => true,
  'SalesRepRef FullName' => true,
  'FOB' => true,
  'ItemSalesTaxRef ListID' => true,
  'ItemSalesTaxRef FullName' => true,
  'Memo' => true,
  'CustomerMsgRef ListID' => true,
  'CustomerMsgRef FullName' => true,
  'IsToBeEmailed' => true,
  'IsTaxIncluded' => true,
  'CustomerSalesTaxCodeRef ListID' => true,
  'CustomerSalesTaxCodeRef FullName' => true,
  'Other' => true,
  'EstimateLineAdd ItemRef ListID' => true,
  'EstimateLineAdd ItemRef FullName' => true,
  'EstimateLineAdd Desc' => true,
  'EstimateLineAdd Quantity' => true,
  'EstimateLineAdd UnitOfMeasure' => true,
  'EstimateLineAdd Rate' => false,
  'EstimateLineAdd RatePercent' => false,
  'EstimateLineAdd ClassRef ListID' => true,
  'EstimateLineAdd ClassRef FullName' => true,
  'EstimateLineAdd Amount' => true,
  'EstimateLineAdd InventorySiteRef ListID' => true,
  'EstimateLineAdd InventorySiteRef FullName' => true,
  'EstimateLineAdd SalesTaxCodeRef ListID' => true,
  'EstimateLineAdd SalesTaxCodeRef FullName' => true,
  'EstimateLineAdd MarkupRate' => false,
  'EstimateLineAdd MarkupRatePercent' => false,
  'EstimateLineAdd PriceLevelRef ListID' => true,
  'EstimateLineAdd PriceLevelRef FullName' => true,
  'EstimateLineAdd OverrideItemAccountRef ListID' => true,
  'EstimateLineAdd OverrideItemAccountRef FullName' => true,
  'EstimateLineAdd Other1' => true,
  'EstimateLineAdd Other2' => true,
  'EstimateLineAdd LotNumber' => true,
  'EstimateLineAdd DataExt OwnerID' => false,
  'EstimateLineAdd DataExt DataExtName' => false,
  'EstimateLineAdd DataExt DataExtValue' => false,
  'EstimateLineGroupAdd ItemGroupRef ListID' => true,
  'EstimateLineGroupAdd ItemGroupRef FullName' => true,
  'EstimateLineGroupAdd Desc' => true,
  'EstimateLineGroupAdd Quantity' => true,
  'EstimateLineGroupAdd UnitOfMeasure' => true,
  'EstimateLineGroupAdd DataExt OwnerID' => false,
  'EstimateLineGroupAdd DataExt DataExtName' => false,
  'EstimateLineGroupAdd DataExt DataExtValue' => false,
  'IncludeRetElement' => true,
);
	}
	
	protected function &_sinceVersionPaths()
	{
		static $paths = array (
  'CustomerRef ListID' => 999.99,
  'CustomerRef FullName' => 999.99,
  'ClassRef ListID' => 999.99,
  'ClassRef FullName' => 999.99,
  'TemplateRef ListID' => 999.99,
  'TemplateRef FullName' => 999.99,
  'TxnDate' => 999.99,
  'RefNumber' => 999.99,
  'BillAddress Addr1' => 999.99,
  'BillAddress Addr2' => 999.99,
  'BillAddress Addr3' => 999.99,
  'BillAddress Addr4' => 2,
  'BillAddress Addr5' => 6,
  'BillAddress City' => 999.99,
  'BillAddress State' => 999.99,
  'BillAddress Province' => 999.99, 
  'BillAddress PostalCode' => 999.99,
  'BillAddress Country' => 999.99,
  'BillAddress Note' => 6,
  'ShipAddress Addr1' => 999.99,
  'ShipAddress Addr2' => 999.99,
  'ShipAddress Addr3' => 999.99,
  'ShipAddress Addr4' => 2,
  'ShipAddress Addr5' => 6,
  'ShipAddress City' => 999.99,
  'ShipAddress State' => 999.99,
  'ShipAddress Province' => 999.99, 
  'ShipAddress PostalCode' => 999.99,
  'ShipAddress Country' => 999.99,
  'ShipAddress Note' => 6,
  'IsActive' => 3,
  'PONumber' => 999.99,
  'TermsRef ListID' => 999.99,
  'TermsRef FullName' => 999.99,
  'DueDate' => 999.99,
  'SalesRepRef ListID' => 999.99,
  'SalesRepRef FullName' => 999.99,
  'FOB' => 999.99,
  'ItemSalesTaxRef ListID' => 999.99,
  'ItemSalesTaxRef FullName' => 999.99,
  'Memo' => 999.99,
  'CustomerMsgRef ListID' => 999.99,
  'CustomerMsgRef FullName' => 999.99,
  'IsToBeEmailed' => 6,
  'IsTaxIncluded' => 6,
  'CustomerSalesTaxCodeRef ListID' => 999.99,
  'CustomerSalesTaxCodeRef FullName' => 999.99,
  'Other' => 6,
  'EstimateLineAdd ItemRef ListID' => 999.99,
  'EstimateLineAdd ItemRef FullName' => 999.99,
  'EstimateLineAdd Desc' => 999.99,
  'EstimateLineAdd Quantity' => 999.99,
  'EstimateLineAdd UnitOfMeasure' => 7,
  'EstimateLineAdd Rate' => 999.99,
  'EstimateLineAdd RatePercent' => 999.99,
  'EstimateLineAdd ClassRef ListID' => 999.99,
  'EstimateLineAdd ClassRef FullName' => 999.99,
  'EstimateLineAdd Amount' => 999.99,
  'EstimateLineAdd InventorySiteRef ListID' => 999.99,
  'EstimateLineAdd InventorySiteRef FullName' => 999.99,
  'EstimateLineAdd SalesTaxCodeRef ListID' => 999.99,
  'EstimateLineAdd SalesTaxCodeRef FullName' => 999.99,
  'EstimateLineAdd MarkupRate' => 999.99,
  'EstimateLineAdd MarkupRatePercent' => 999.99,
  'EstimateLineAdd PriceLevelRef ListID' => 999.99,
  'EstimateLineAdd PriceLevelRef FullName' => 999.99,
  'EstimateLineAdd OverrideItemAccountRef ListID' => 999.99,
  'EstimateLineAdd OverrideItemAccountRef FullName' => 999.99,
  'EstimateLineAdd Other1' => 6,
  'EstimateLineAdd Other2' => 6,
  'EstimateLineAdd LotNumber' => 6,
  'EstimateLineAdd DataExt OwnerID' => 999.99,
  'EstimateLineAdd DataExt DataExtName' => 999.99,
  'EstimateLineAdd DataExt DataExtValue' => 999.99,
  'EstimateLineGroupAdd ItemGroupRef ListID' => 999.99,
  'EstimateLineGroupAdd ItemGroupRef FullName' => 999.99,
  'EstimateLineGroupAdd Desc' => 999.99,
  'EstimateLineGroupAdd Quantity' => 999.99,
  'EstimateLineGroupAdd UnitOfMeasure' => 7,
  'EstimateLineGroupAdd DataExt OwnerID' => 999.99,
  'EstimateLineGroupAdd DataExt DataExtName' => 999.99,
  'EstimateLineGroupAdd DataExt DataExtValue' => 999.99,
  'IncludeRetElement' => 4,
);
		
		return $paths;
	}
	
	protected function &_isRepeatablePaths()
	{
		static $paths = array (
  'CustomerRef ListID' => false,
  'CustomerRef FullName' => false,
  'ClassRef ListID' => false,
  'ClassRef FullName' => false,
  'TemplateRef ListID' => false,
  'TemplateRef FullName' => false,
  'TxnDate' => false,
  'RefNumber' => false,
  'BillAddress Addr1' => false,
  'BillAddress Addr2' => false,
  'BillAddress Addr3' => false,
  'BillAddress Addr4' => false,
  'BillAddress Addr5' => false,
  'BillAddress City' => false,
  'BillAddress State' => false,
  'BillAddress Province' => false, 
  'BillAddress PostalCode' => false,
  'BillAddress Country' => false,
  'BillAddress Note' => false,
  'ShipAddress Addr1' => false,
  'ShipAddress Addr2' => false,
  'ShipAddress Addr3' => false,
  'ShipAddress Addr4' => false,
  'ShipAddress Addr5' => false,
  'ShipAddress City' => false,
  'ShipAddress State' => false,
  'ShipAddress Province' => false, 
  'ShipAddress PostalCode' => false,
  'ShipAddress Country' => false,
  'ShipAddress Note' => false,
  'IsActive' => false,
  'PONumber' => false,
  'TermsRef ListID' => false,
  'TermsRef FullName' => false,
  'DueDate' => false,
  'SalesRepRef ListID' => false,
  'SalesRepRef FullName' => false,
  'FOB' => false,
  'ItemSalesTaxRef ListID' => false,
  'ItemSalesTaxRef FullName' => false,
  'Memo' => false,
  'CustomerMsgRef ListID' => false,
  'CustomerMsgRef FullName' => false,
  'IsToBeEmailed' => false,
  'IsTaxIncluded' => false,
  'CustomerSalesTaxCodeRef ListID' => false,
  'CustomerSalesTaxCodeRef FullName' => false,
  'Other' => false,
  'EstimateLineAdd ItemRef ListID' => false,
  'EstimateLineAdd ItemRef FullName' => false,
  'EstimateLineAdd Desc' => false,
  'EstimateLineAdd Quantity' => false,
  'EstimateLineAdd UnitOfMeasure' => false,
  'EstimateLineAdd Rate' => false,
  'EstimateLineAdd RatePercent' => false,
  'EstimateLineAdd ClassRef ListID' => false,
  'EstimateLineAdd ClassRef FullName' => false,
  'EstimateLineAdd Amount' => false,
  'EstimateLineAdd InventorySiteRef ListID' => false,
  'EstimateLineAdd InventorySiteRef FullName' => false,
  'EstimateLineAdd SalesTaxCodeRef ListID' => false,
  'EstimateLineAdd SalesTaxCodeRef FullName' => false,
  'EstimateLineAdd MarkupRate' => false,
  'EstimateLineAdd MarkupRatePercent' => false,
  'EstimateLineAdd PriceLevelRef ListID' => false,
  'EstimateLineAdd PriceLevelRef FullName' => false,
  'EstimateLineAdd OverrideItemAccountRef ListID' => false,
  'EstimateLineAdd OverrideItemAccountRef FullName' => false,
  'EstimateLineAdd Other1' => false,
  'EstimateLineAdd Other2' => false,
  'EstimateLineAdd LotNumber' => false,
  'EstimateLineAdd DataExt OwnerID' => false,
  'EstimateLineAdd DataExt DataExtName' => false,
  'EstimateLineAdd DataExt DataExtValue' => false,
  'EstimateLineGroupAdd ItemGroupRef ListID' => false,
  'EstimateLineGroupAdd ItemGroupRef FullName' => false,
  'EstimateLineGroupAdd Desc' => false,
  'EstimateLineGroupAdd Quantity' => false,
  'EstimateLineGroupAdd UnitOfMeasure' => false,
  'EstimateLineGroupAdd DataExt OwnerID' => false,
  'EstimateLineGroupAdd DataExt DataExtName' => false,
  'EstimateLineGroupAdd DataExt DataExtValue' => false,
  'IncludeRetElement' => true,
);
			
		return $paths;
	}
	
	/*
	abstract protected function &_inLocalePaths()
	{
		static $paths = array(
			'FirstName' => array( 'QBD', 'QBCA', 'QBUK', 'QBAU' ), 
			'LastName' => array( 'QBD', 'QBCA', 'QBUK', 'QBAU' ),
			);
		
		return $paths;
	}
	*/
	
	protected function &_reorderPathsPaths()
	{
		static $paths = array (
			'CustomerRef ListID',
			'CustomerRef FullName',
			'ClassRef ListID',
			'ClassRef FullName',
			'TemplateRef ListID',
			'TemplateRef FullName',
			'TxnDate',
			'RefNumber',
			'BillAddress Addr1',
			'BillAddress Addr2',
			'BillAddress Addr3',
			'BillAddress Addr4',
			'BillAddress Addr5',
			'BillAddress City',
			'BillAddress State',
			'BillAddress Province', 
			'BillAddress PostalCode',
			'BillAddress Country',
			'BillAddress Note',
			'ShipAddress Addr1',
			'ShipAddress Addr2',
			'ShipAddress Addr3',
			'ShipAddress Addr4',
			'ShipAddress Addr5',
			'ShipAddress City',
			'ShipAddress State',
			'ShipAddress Province', 
			'ShipAddress PostalCode',
			'ShipAddress Country',
			'ShipAddress Note',
			'IsActive',
			'PONumber',
			'TermsRef ListID',
			'TermsRef FullName',
			'DueDate',
			'SalesRepRef ListID',
			'SalesRepRef FullName',
			'FOB',
			'ItemSalesTaxRef ListID',
			'ItemSalesTaxRef FullName',
			'Memo',
			'CustomerMsgRef ListID',
			'CustomerMsgRef FullName',
			'IsToBeEmailed',
			'IsTaxIncluded',
			'CustomerSalesTaxCodeRef ListID',
			'CustomerSalesTaxCodeRef FullName',
			'Other',
			'EstimateLineAdd',
			'EstimateLineAdd ItemRef',
			'EstimateLineAdd ItemRef ListID',
			'EstimateLineAdd ItemRef FullName',
			'EstimateLineAdd Desc',
			'EstimateLineAdd Quantity',
			'EstimateLineAdd UnitOfMeasure',
			'EstimateLineAdd Rate',
			'EstimateLineAdd RatePercent',
			'EstimateLineAdd ClassRef ListID',
			'EstimateLineAdd ClassRef FullName',
			'EstimateLineAdd Amount',
			'EstimateLineAdd InventorySiteRef ListID',
			'EstimateLineAdd InventorySiteRef FullName',
			'EstimateLineAdd LotNumber',
			'EstimateLineAdd SalesTaxCodeRef ListID',
			'EstimateLineAdd SalesTaxCodeRef FullName',
			'EstimateLineAdd MarkupRate',
			'EstimateLineAdd MarkupRatePercent',
			'EstimateLineAdd PriceLevelRef ListID',
			'EstimateLineAdd PriceLevelRef FullName',
			'EstimateLineAdd OverrideItemAccountRef ListID',
			'EstimateLineAdd OverrideItemAccountRef FullName',
			'EstimateLineAdd Other1',
			'EstimateLineAdd Other2',
			
			'EstimateLineAdd DataExt OwnerID',
			'EstimateLineAdd DataExt DataExtName',
			'EstimateLineAdd DataExt DataExtValue',
			'EstimateLineGroupAdd', 
			'EstimateLineGroupAdd ItemGroupRef ListID',
			'EstimateLineGroupAdd ItemGroupRef FullName',
			'EstimateLineGroupAdd Desc',
			'EstimateLineGroupAdd Quantity',
			'EstimateLineGroupAdd UnitOfMeasure',
			'EstimateLineGroupAdd DataExt OwnerID',
			'EstimateLineGroupAdd DataExt DataExtName',
			'EstimateLineGroupAdd DataExt DataExtValue',
			'IncludeRetElement',
		);
		
		return $paths;
	}
}
