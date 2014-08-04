<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
require_once('include/MVC/View/views/view.detail.php');
class ContactsViewDetail extends ViewDetail{
	 function display() {
		if($this->bean->contact_type_c == 'Student'){
			unset($this->dv->defs['panels']['LBL_PANEL_ADVANCED']);
		}
		if($this->bean->contact_type_c == 'Business'){
			unset($this->dv->defs['panels']['LBL_PANEL_ASSIGNMENT']);
		}
		//Clear Template Cache
		TemplateHandler::clearCache('Contacts', 'DetailView.tpl');
		$this->dv->process();
        echo $this->dv->display();	
	 }
}