<?php

/*
 * CustomInvoiceInformation
 *
 * @author LBAB <contact@lbab.fr>
 * @copyright Copyright (c) 2014 LBAB.
 * @license GNU/LGPL version 3
 * @version 1.0.0
 * @link www.lbab.fr
 */

if (!defined('_PS_VERSION_'))
  exit;
	
class custominvoiceinformation extends Module
{
	public function __construct()
	{
	    $this->bootstrap = true;
	    
		$this->name = 'custominvoiceinformation';
		$this->tab = 'billing_invoicing';
		$this->version = '1.0.0';
		$this->author = 'LBAB';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');

		parent::__construct();

		$this->displayName = $this->l('Custom Invoice Information');
        	$this->description = $this->l('Add custom information on invoice');

		$this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
	}
	
	public function install()
	{
	    if (Shop::isFeatureActive())
	        Shop::setContext(Shop::CONTEXT_ALL);
	    
		return (parent::install() && 
		    $this->registerHook('displayPDFInvoice') 		    
		);
	}
	
	public function uninstall()
	{		
		return (parent::uninstall() && 
		    Configuration::deleteByName('custominvoiceinformation-line1') &&
		    Configuration::deleteByName('custominvoiceinformation-line2') &&
		    Configuration::deleteByName('custominvoiceinformation-line3') &&
		    Configuration::deleteByName('custominvoiceinformation-line4') &&
		    Configuration::deleteByName('custominvoiceinformation-line5')
		);
	}
	
	private function _displayInfos()
	{
	    $this->context->smarty->assign(
	        array(
	            'moduleName' => $this->displayName,
	            'description' => $this->description,
	            'version' => $this->version
	        )
	    );
	    
	    return $this->display(__FILE__, 'infos.tpl');
	}
	
	public function getContent()
	{
	    $output = '';
	    
	    if (Tools::isSubmit('submit'.$this->name))
	    {
	        Configuration::updateValue('custominvoiceinformation-line1', strval(Tools::getValue('line1')));
	        Configuration::updateValue('custominvoiceinformation-line2', strval(Tools::getValue('line2')));
	        Configuration::updateValue('custominvoiceinformation-line3', strval(Tools::getValue('line3')));
	        Configuration::updateValue('custominvoiceinformation-line4', strval(Tools::getValue('line4')));
	        Configuration::updateValue('custominvoiceinformation-line5', strval(Tools::getValue('line5')));
	    }
	    
	    $output .= $this->_displayInfos();
	    return $output.$this->renderForm();
	}
	
	public function renderForm()
	{
	    // Get default Language
	    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	     
	    // Init Fields form array
	    $fields_form[0]['form'] = array(
	        'legend' => array(
	            'title' => $this->l('Settings'),
	            'icon' => 'logo'
	        ),
		    'description' => $this->l('An empty line is not included in the display. To make a line break, please fill in the fields with a space'),
	        'input' => array(
	            array(
	                'type' => 'text',
	                'label' => $this->l('Line 1'),
	                'name' => 'line1',
	                'required' => false
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('Line 2'),
	                'name' => 'line2',
	                'required' => false
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('Line 3'),
	                'name' => 'line3',
	                'required' => false
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('Line 4'),
	                'name' => 'line4',
	                'required' => false
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('Line 5'),
	                'name' => 'line5',
	                'required' => false
	            ),
	        ),
	        'submit' => array(
	            'title' => $this->l('Save'),
	            'class' => 'btn btn-default pull-right'
	        )
	    );
	     
	    $helper = new HelperForm();
	     
	    // Module, token and currentIndex
	    $helper->module = $this;
	    $helper->name_controller = $this->name;
	    $helper->token = Tools::getAdminTokenLite('AdminModules');
	    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
	     
	    // Language
	    $helper->default_form_language = $default_lang;
	    $helper->allow_employee_form_lang = $default_lang;
	     
	    // Title and toolbar
	    $helper->title = $this->displayName;
	    $helper->show_toolbar = true;        // false -> remove toolbar
	    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
	    $helper->submit_action = 'submit'.$this->name;
	     
	    // Load current value
	    $helper->fields_value['line1'] = Configuration::get('custominvoiceinformation-line1');
	    $helper->fields_value['line2'] = Configuration::get('custominvoiceinformation-line2');
	    $helper->fields_value['line3'] = Configuration::get('custominvoiceinformation-line3');
	    $helper->fields_value['line4'] = Configuration::get('custominvoiceinformation-line4');
	    $helper->fields_value['line5'] = Configuration::get('custominvoiceinformation-line5');
	     
	    return $helper->generateForm($fields_form);
	}

	public function hookDisplayPDFInvoice()
	{
	    $str='';

	    for($i=1 ; $i<6 ; $i++)
	    {
	    	$line=''.Configuration::get('custominvoiceinformation-line'.$i);
	    	if($line!='')
	    	{
	    		if($i>1) { $str.='<br />'; }
	    		$str.=$line;
	    	}
		}

		return $str;
	}

}
