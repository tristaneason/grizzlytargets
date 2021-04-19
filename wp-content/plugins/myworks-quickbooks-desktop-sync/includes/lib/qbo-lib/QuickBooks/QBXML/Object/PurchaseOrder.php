<?php
QuickBooks_Loader::load('/QuickBooks/QBXML/Object.php');
QuickBooks_Loader::load('/QuickBooks/QBXML/Object/Generic.php');

QuickBooks_Loader::load('/QuickBooks/QBXML/Object/PurchaseOrder/PurchaseOrderLine.php');
QuickBooks_Loader::load('/QuickBooks/QBXML/Object/PurchaseOrder/PurchaseOrderLineGroup.php');

class QuickBooks_QBXML_Object_PurchaseOrder extends QuickBooks_QBXML_Object
{
	public function __construct($arr = array())
	{
		parent::__construct($arr);
	}
	
	public function addPurchaseOrderLine($obj)
	{
		return $this->addListItem('PurchaseOrderLine', $obj);
		
		/*
		$lines = $this->get('PurchaseOrderLine');
		
		//
		$lines[] = $obj;
		
		return $this->set('PurchaseOrderLine', $lines);
		*/
	}
	
	public function addPurchaseOrderLineGroup($obj)
	{
		return $this->addListItem('PurchaseOrderLineGroup', $obj);
		
		/*
		$lines = $this->get('PurchaseOrderLineGroup');
		
		//
		$lines[] = $obj;
		
		return $this->set('PurchaseOrderLineGroup', $lines);
		*/
	}
	
	/**
	 * 
	 * 
	 * @return boolean
	 */
	protected function _cleanup()
	{
		
		return true;
	}
	
	public function asList($request)
	{
		switch ($request)
		{
			case 'PurchaseOrderAddRq':
				
				if (isset($this->_object['PurchaseOrderLine']))
				{
					$this->_object['PurchaseOrderLineAdd'] = $this->_object['PurchaseOrderLine'];
				}
				
				if (isset($this->_object['PurchaseOrderLineGroup']))
				{
					$this->_object['PurchaseOrderLineGroupAdd'] = $this->_object['PurchaseOrderLineGroup'];
				}
				
				/*
				if (isset($this->_object['ShippingLine']))
				{
					$this->_object['ShippingLineAdd'] = $this->_object['ShippingLine'];
				}				

				if (isset($this->_object['SalesTaxLine']))
				{
					$this->_object['SalesTaxLineAdd'] = $this->_object['SalesTaxLine'];
				}				

				if (isset($this->_object['DiscountLine']))
				{
					$this->_object['DiscountLineAdd'] = $this->_object['DiscountLine'];
				}
				*/
				
				break;
			case 'PurchaseOrderModRq':
				
				if (isset($this->_object['PurchaseOrderLine']))
				{
					$this->_object['PurchaseOrderLineMod'] = $this->_object['PurchaseOrderLine'];	
				}
				
				if (isset($this->_object['PurchaseOrderLineGroup']))
				{
					$this->_object['PurchaseOrderLineGroupMod'] = $this->_object['PurchaseOrderLineGroup'];	
				}
				
				break;
		}
		
		return parent::asList($request);
	}
	
	public function asXML($root = null, $parent = null, $object = null)
	{
		//print('PURCHASEORDER got called asXML: ' . $root . ', ' . $parent . "\n");
		//exit;
		
		if (is_null($object))
		{
			$object = $this->_object;
		}
		
		switch ($root)
		{
			case QUICKBOOKS_ADD_PURCHASEORDER:
				
				//if (isset($this->_object['PurchaseOrderLine']))
				//{
				//	$this->_object['PurchaseOrderLineAdd'] = $this->_object['PurchaseOrderLine'];
				//}

				if (!empty($object['PurchaseOrderLineAdd']))
				{
					foreach ($object['PurchaseOrderLineAdd'] as $key => $obj)
					{
						$obj->setOverride('PurchaseOrderLineAdd');
					}
				}
				
				if (!empty($object['PurchaseOrderLineGroupAdd']))
				{
					foreach ($object['PurchaseOrderLineGroupAdd'] as $key => $obj)
					{
						$obj->setOverride('PurchaseOrderLineGroupAdd');
					}
				}

				/*
				if (!empty($object['ShippingLineAdd']))
				{
					foreach ($object['ShippingLineAdd'] as $key => $obj)
					{
						$obj->setOverride('ShippingLineAdd');
					}
				}
				
				if (!empty($object['DiscountLineAdd']))
				{
					foreach ($object['DiscountLineAdd'] as $key => $obj)
					{
						$obj->setOverride('DiscountLineAdd');
					}
				}
				
				if (!empty($object['SalesTaxLineAdd']))
				{
					foreach ($object['SalesTaxLineAdd'] as $key => $obj)
					{
						$obj->setOverride('SalesTaxLineAdd');
					}
				}
				*/
				
				break;
			case QUICKBOOKS_MOD_PURCHASEORDER:
				
				/*
				if (isset($object['PurchaseOrderLine']))
				{
					$object['PurchaseOrderLineMod'] = $object['PurchaseOrderLine'];
				}
				*/

				if (!empty($object['PurchaseOrderLineMod']))
				{
					foreach ($object['PurchaseOrderLineMod'] as $key => $obj)
					{
						$obj->setOverride('PurchaseOrderLineMod');
					}
				}
				
				if (!empty($object['PurchaseOrderLineGroupMod']))
				{
					foreach ($object['PurchaseOrderLineGroupMod'] as $key => $obj)
					{
						$obj->setOverride('PurchaseOrderLineGroupMod');
					}
				}

				break;
		}
		
		//print_r($this->_object);
		
		return parent::asXML($root, $parent, $object);
	}
	
	/**
	 * 
	 */
	public function asArray($request, $nest = true)
	{
		$this->_cleanup();
		
		return parent::asArray($request, $nest);
	}
	
	/**
	 * 
	 * 
	 * @param boolean $todo_for_empty_elements	A constant, one of: QUICKBOOKS_XML_XML_COMPRESS, QUICKBOOKS_XML_XML_DROP, QUICKBOOKS_XML_XML_PRESERVE
	 * @param string $indent
	 * @param string $root
	 * @return string
	 */
	public function asQBXML($request, $todo_for_empty_elements = QuickBooks_QBXML_Object::XML_DROP, $indent = "\t", $root = null, $parent = null)
	{
		$this->_cleanup();
		
		return parent::asQBXML($request, $todo_for_empty_elements, $indent, $root);
	}
	
	/**
	 * Tell the type of object this is
	 * 
	 * @return string
	 */
	public function object()
	{
		return QUICKBOOKS_OBJECT_PURCHASEORDER;
	}
}
