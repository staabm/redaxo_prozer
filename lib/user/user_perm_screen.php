<?php

class pz_user_perm_screen {

	public $user_perm;	
	
	function __construct($user_perm) 
	{
		$this->user_perm = $user_perm;
	}
	
	
	public function getTableView($p = array())
	{
		$edit_link = pz::url("screen","tools","perms",array("user_perm_id"=>$this->user_perm->getId(),"mode"=>"edit_user_perm"));
		$delete_link = pz::url("screen","tools","perms",array("user_perm_id"=>$this->user_perm->getId(),"mode"=>"delete_user_perm"));
		
		$return = '<tr>';
		$return .= '<td class="img1"><img src="'.$this->user_perm->getToUser()->getInlineImage().'" width="40" height="40" alt="" /></td>';
       	$return .= '<td><a href="javascript:pz_loadPage(\'user_perm_form\',\''.$edit_link.'\')"><span class="title">'.$this->user_perm->getToUser()->getName().'</span></a></td>';

		if($this->user_perm->hasCalendarReadPerm())  
			$return .= '<td><span class="status status-1">'.rex_i18n::msg("yes").'</span></td>';
		else 
			$return .= '<td><span class="status status-2">'.rex_i18n::msg("no").'</span></td>';

		if($this->user_perm->hasCalendarWritePerm())  
			$return .= '<td><span class="status status-1">'.rex_i18n::msg("yes").'</span></td>';
		else 
			$return .= '<td><span class="status status-2">'.rex_i18n::msg("no").'</span></td>';

		if($this->user_perm->hasEmailReadPerm())  
			$return .= '<td><span class="status status-1">'.rex_i18n::msg("yes").'</span></td>';
		else 
			$return .= '<td><span class="status status-2">'.rex_i18n::msg("no").'</span></td>';

		if($this->user_perm->hasEmailWritePerm())  
			$return .= '<td><span class="status status-1">'.rex_i18n::msg("yes").'</span></td>';
		else 
			$return .= '<td><span class="status status-2">'.rex_i18n::msg("no").'</span></td>';

   		$return .= '<td><a class="bt2" href="javascript:pz_loadPage(\'user_perms_list\',\''.$delete_link.'\')"><span class="title">'.rex_i18n::msg("delete").'</span></a></td>';
        
        $return .= '</tr>';
        
		return $return;
		
	}
	
	
	static function getTableListView($user_perms, $p = array())
	{
		$list = "";
		
		$paginate_screen = new pz_paginate_screen($user_perms);
		$paginate = $paginate_screen->getPlainView($p);
		
		foreach($paginate_screen->getCurrentElements() as $user_perm) {
			$up = new pz_user_perm_screen($user_perm);
			$list .= $up->getTableView($p);
		}
		
		$content = $paginate.'
          <table class="users tbl1">
          <thead><tr>
              <th></th>
              <th>'.rex_i18n::msg("username").'</th>
              <th>'.rex_i18n::msg("user_perm_calendar_read").'</th>
              <th>'.rex_i18n::msg("user_perm_calendar_write").'</th>
              <th>'.rex_i18n::msg("user_perm_email_read").'</th>
              <th>'.rex_i18n::msg("user_perm_email_write").'</th>
              <th>'.rex_i18n::msg("functions").'</th>
          </tr></thead>
          <tbody>
            '.$list.'
          </tbody>
          </table>';
		
		if(isset($p["info"])) {
			$content = $p["info"].$content;
		}
		
		$f = new rex_fragment();
		$f->setVar('title', $p["title"], false);
		$f->setVar('content', $content , false);
		return '<div id="user_perms_list" class="design2col">'.$f->parse('pz_screen_list.tpl').'</div>';

	}
	
	
	
	// ---------------------------------------- FORM VIEWS
	
	static function getAddForm($p = array()) 
	{
		$header = '
        <header>
          <div class="header">
            <h1 class="hl1">'.rex_i18n::msg("user_perm_add").'</h1>
          </div>
        </header>';

		$xform = new rex_xform;
		// $xform->setDebug(TRUE);

		$xform->setObjectparams("main_table",'pz_user_perm');
		$xform->setObjectparams("form_action", "javascript:pz_loadFormPage('user_perm_form','user_perm_add_form','".pz::url('screen','tools','perms',array("mode"=>'add_user_perm'))."')");
		$xform->setObjectparams("form_id", "user_perm_add_form");

		$xform->setValueField('objparams',array('fragment', 'pz_screen_xform.tpl'));

		$xform->setValidateField("unique", array("user_id,to_user_id",rex_i18n::msg("user_perm_user_exists")));
		$xform->setValueField('pz_select_screen',array('to_user_id', rex_i18n::msg('user'), pz::getUsersAsString(),"","",0));

		function pz_checkIsMe($label,$user_id,$me_id) {
			if($user_id == $me_id)
				return TRUE;
			return FALSE;
		}

		$xform->setValidateField("customfunction", array("to_user_id","pz_checkIsMe",pz::getUser()->getId(),rex_i18n::msg("user_perm_user_isme")));


		$xform->setValueField("checkbox",array("calendar_read",rex_i18n::msg("user_perm_calendar_read"),"1","0","0"));
		$xform->setValueField("checkbox",array("calendar_write",rex_i18n::msg("user_perm_calendar_write"),"1","0","0"));

		$xform->setValueField("checkbox",array("email_read",rex_i18n::msg("user_perm_email_read"),"1","0","0"));
		$xform->setValueField("checkbox",array("email_write",rex_i18n::msg("user_perm_email_write"),"1","0","0"));

		$xform->setValueField("hidden", array("user_id",pz::getUser()->getId()));

		$xform->setValueField("stamp",array("created","created","mysql_datetime","0","0","","","",""));
		$xform->setValueField("stamp",array("updated","updated","mysql_datetime","0","0","","","",""));


		$xform->setActionField("db",array('pz_user_perm'));

		$return = $xform->getForm();

		if($xform->getObjectparams("actions_executed")) 
		{
			$user_id = $xform->getObjectparams("main_id");
			$return = $header.'<p class="xform-info">'.rex_i18n::msg("user_perm_added").'</p>'.$return;
			$return .= pz_screen::getJSUpdateLayer('user_perms_list',pz::url('screen','tools','perms',array("mode"=>'list_user_perms')));
		}else
		{
			$return = $header.$return;	
		}
		$return = '<div id="user_perm_form"><div id="user_perm_add" class="design1col xform-edit">'.$return.'</div></div>';

		return $return;	
		
	}
	
	
	public function getEditForm($p = array()) 
	{
	
    	$header = '
        <header>
          <div class="header">
            <h1 class="hl1">'.rex_i18n::msg("user_perm_edit").': '.$this->user_perm->getToUser()->getName().'</h1>
          </div>
        </header>';

		$xform = new rex_xform;
		// $xform->setDebug(TRUE);

		$xform->setObjectparams("main_table",'pz_user_perm');
		$xform->setObjectparams("main_id",$this->user_perm->getId());
		$xform->setObjectparams("main_where",'id='.$this->user_perm->getId());
		$xform->setObjectparams('getdata',true);

		$xform->setHiddenField("user_perm_id",$this->user_perm->getId());

		$xform->setObjectparams("form_action", "javascript:pz_loadFormPage('user_perm_form','user_perm_edit_form','".pz::url('screen','tools','perms',array("mode"=>'edit_user_perm'))."')");
		$xform->setObjectparams("form_id", "user_perm_edit_form");
		$xform->setObjectparams('form_showformafterupdate',1);

		$xform->setValueField('objparams',array('fragment', 'pz_screen_xform.tpl'));

		$xform->setValidateField("unique", array("user_id,to_user_id",rex_i18n::msg("user_perm_user_exists")));
		$xform->setValueField('pz_select_screen',array('to_user_id', rex_i18n::msg('user'), pz::getUsersAsString(),"","",0));
		function pz_checkIsMe($label,$user_id,$me_id) {
			if($user_id == $me_id)
				return TRUE;
			return FALSE;
		}
		$xform->setValidateField("customfunction", array("to_user_id","pz_checkIsMe",pz::getUser()->getId(),rex_i18n::msg("user_perm_user_isme")));


		$xform->setValueField("checkbox",array("calendar_read",rex_i18n::msg("user_perm_calendar_read"),"1","0","0"));
		$xform->setValueField("checkbox",array("calendar_write",rex_i18n::msg("user_perm_calendar_write"),"1","0","0"));

		$xform->setValueField("checkbox",array("email_read",rex_i18n::msg("user_perm_email_read"),"1","0","0"));
		$xform->setValueField("checkbox",array("email_write",rex_i18n::msg("user_perm_email_write"),"1","0","0"));

		$xform->setValueField("hidden", array("user_id",pz::getUser()->getId()));

		$xform->setValueField("stamp",array("created","created","mysql_datetime","0","0","","","",""));
		$xform->setValueField("stamp",array("updated","updated","mysql_datetime","0","0","","","",""));

		$xform->setActionField("db",array('pz_user_perm','id='.$this->user_perm->getId()));

		$return = $xform->getForm();

		if($xform->getObjectparams("actions_executed")) 
		{

			$return = $header.'<p class="xform-info">'.rex_i18n::msg("user_perm_updated").'</p>'.$return;
			$return .= pz_screen::getJSUpdateLayer('user_perms_list',pz::url('screen','tools','perms',array("mode"=>'list_user_perms')));
			
		}else
		{
			$return = $header.$return;	
		}
		$return = '<div id="user_perm_form"><div id="user_perm_edit" class="design1col xform-edit">'.$return.'</div></div>';

		return $return;	
		
	}
	
}