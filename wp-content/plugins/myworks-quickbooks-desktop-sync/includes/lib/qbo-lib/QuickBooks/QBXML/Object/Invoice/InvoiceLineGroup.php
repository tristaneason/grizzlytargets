<?php

/**
 * QuickBooks InvoiceLine object class
 * 
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt 
 * 
 * @package QuickBooks
 * @subpackage Object
 */

/**
 * QuickBooks object base class
 */
QuickBooks_Loader::load('/QuickBooks/QBXML/Object.php');

/**
 * QuickBooks invoice class
 */
QuickBooks_Loader::load('/QuickBooks/QBXML/Object/Invoice.php');

/**
 * QuickBooks InvoiceLine class for Invoices
 */
class QuickBooks_QBXML_Object_Invoice_InvoiceLineGroup extends QuickBooks_QBXML_Object
{
	/**
	 * Create a new QuickBooks Invoice InvoiceLine object
	 * 
	 * @param array $arr
	 */
	public function __construct($arr = array())
	{
		parent::__construct($arr);
	}
	
	public function getTxnLineID()
	{
		return $this->get('TxnLineID');
	}
	
	/**
	 * Set the Item ListID for this InvoiceLine
	 * 
	 * @param string $ListID
	 * @return boolean
	 */
	public function setItemListID($ListID)
	{
		return $this->set('ItemGroupRef ListID', $ListID);
	}
	
	/** 
	 * Set the item application ID for this invoice line 
	 * 
	 * @param mixed $value
	 * @return boolean
	 */
	public function setItemApplicationID($value)
	{
		return $this->set('ItemGroupRef ' . QUICKBOOKS_API_APPLICATIONID, $this->encodeApplicationID(QUICKBOOKS_OBJECT_ITEM, QUICKBOOKS_LISTID, $value));
	}
	
	/**
	 * Set the item name for this invoice line
	 * 
	 * @param string $name
	 * @return boolean
	 */
	public function setItemName($name)
	{
		return $this->set('ItemGroupRef FullName', $name);
	}
	
	public function setItemFullName($FullName)
	{
		return $this->setFullNameType('ItemGroupRef FullName', null, null, $FullName);
	}
	
	/**
	 * Get the ListID for this item
	 * 
	 * @return string
	 */
	public function getItemListID()
	{
		return $this->get('ItemGroupRef ListID');
	}
	
	/**
	 * Get the item application ID
	 * 
	 * @return mixed
	 */
	public function getItemApplicationID()
	{
		//print($this->get('ItemGroupRef ' . QUICKBOOKS_API_APPLICATIONID) . '<br />');
		
		return $this->extractApplicationID($this->get('ItemGroupRef ' . QUICKBOOKS_API_APPLICATIONID));
	}
	
	/**
	 * Get the name of the item for this invoice line item
	 * 
	 * @return string
	 */
	public function getItemName()
	{
		return $this->get('ItemGroupRef FullName');
	}
	
	public function getItemFullName()
	{
		return $this->get('ItemGroupRef FullName');
	}
	
	public function setDesc($descrip)
	{
		return $this->set('Desc', $descrip);
	}
	
	public function getDesc()
	{
		return $this->get('Desc');
	}
	
	public function setDescription($descrip)
	{
		return $this->setDesc($descrip);
	}
	
	public function getDescription()
	{
		return $this->getDesc();
	}
	
	public function setQuantity($quan)
	{
		return $this->set('Quantity', (float) $quan);
	}
	
	public function getQuantity()
	{
		return $this->get('Quantity');
	}
	
	public function setUnitOfMeasure($unit)
	{
		return $this->set('UnitOfMeasure', $unit);
	}
	
	public function getUnitOfMeasure()
	{
		return $this->get('UnitOfMeasure');
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
			case QUICKBOOKS_ADD_INVOICE:
				$root = 'InvoiceLineGroupAdd';
				$parent = null;
				break;
			case QUICKBOOKS_MOD_INVOICE:
				$root = 'InvoiceLineGroupMod';
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
		return 'InvoiceLineGroup';
	}
}
