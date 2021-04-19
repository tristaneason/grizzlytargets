<?php
QuickBooks_Loader::load('/QuickBooks/QBXML/Object.php');
QuickBooks_Loader::load('/QuickBooks/QBXML/Object/PurchaseOrder.php');

class QuickBooks_QBXML_Object_PurchaseOrder_PurchaseOrderLine extends QuickBooks_QBXML_Object{
	public function __construct($arr = array())
	{
		parent::__construct($arr);
	}
	
	protected function _cleanup()
	{
		if ($this->exists('Amount'))
		{
			$this->setAmountType('Amount', $this->getAmountType('Amount'));
		}
		
		return true;
	}
	
	/**
	 * 
	 */
	public function asArray($request, $nest = true)
	{
		$this->_cleanup();
		
		return parent::asArray($request, $nest);
	}
	
	public function asXML($root = null, $parent = null, $object = null)
	{
		$this->_cleanup();
		
		switch ($parent)
		{
			case QUICKBOOKS_ADD_PURCHASEORDER:
				$root = 'PurchaseOrderLineAdd';
				$parent = null;
				break;
			case QUICKBOOKS_MOD_PURCHASEORDER:
				$root = 'PurchaseOrderLineMod';
				$parent = null;
				break;
		}
		
		return parent::asXML($root, $parent, $object);
	}
	
	/**
	 * 
	 * 
	 * @param boolean $todo_for_empty_elements	A constant, one of: QUICKBOOKS_XML_XML_COMPRESS, QUICKBOOKS_XML_XML_DROP, QUICKBOOKS_XML_XML_PRESERVE
	 * @param string $indent
	 * @param string $root
	 * @return string
	 */
	public function asQBXML($request, $todo_for_empty_elements = QUICKBOOKS_OBJECT_XML_DROP, $indent = "\t", $root = null)
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
		return 'PurchaseOrderLine';
	}
}