<?php

class pz_label_screen{

	public $label;

	function __construct($label) 
	{
		$this->label = $label;
	}


	// --------------------------------------------------------------- Static returns

  static function getColorClass($id)
  {
    return 'labelc'.$id;
  }

  static function getBorderColorClass($id)
  {
    return 'labelb'.$id;
  }

	// --------------------------------------------------------------- Listviews

	function getListView($p = array())
	{
    	$p["linkvars"]["label_id"] = $this->label->getVar("id");
    
    	$edit_link = "javascript:pz_loadPage('label_form','".pz::url("screen","projects","labels",array_merge($p["linkvars"],array("mode"=>"edit_label","label_id"=>$this->label->getId())))."')";

		$return = '
		   <article>
            <header>
              <a class="detail" href="'.$edit_link.'">
                <hgroup>
                  <h3 class="hl7"><span class="title">'.$this->label->getVar("name").'</span></h3>
                </hgroup>
                <span class="label labelc'.$this->label->getVar('id').'">Label</span>
              </a>
            </header>
            <footer>
	            <a class="bt2" href="'.$edit_link.'">'.rex_i18n::msg("label_edit").'</a>
            </footer>
          </article>';
	
		// <a class="bt2" href="'.pz::url("screen","projects","tools",array("mode"=>"delete","label_id"=>$this->label->getId())).'">'.rex_i18n::msg("label_delete").'</a>
	
		return $return;
	}


	function getDeleteForm($p = array())
	{
		$header = '
	        <header>
	          <div class="header">
	            <h1 class="hl1">'.rex_i18n::msg("delete_address").'</h1>
	          </div>
	        </header>';
		
		$return = $header.'<p class="xform-info">'.rex_i18n::msg("label_deleted", htmlspecialchars($p["label_name"])).'</p>';
		$return .= pz_screen::getJSLoadFormPage('labels_list','labels_search_form',pz::url('screen','projects',$p["function"],array("mode"=>'list')));
		$return = '<div id="label_form"><div id="label_delete" class="design1col xform-delete">'.$return.'</div></div>';

		return $return;
	}


	function getEditForm($p = array()) 
	{
	
		$header = '
	        <header>
	          <div class="header">
	            <h1 class="hl1">'.rex_i18n::msg("label_edit").': '.$this->label->getName().'</h1>
	          </div>
	        </header>';
	
		$xform = new rex_xform;
		// $xform->setDebug(TRUE);

		$xform->setObjectparams("main_table",'pz_label');
		$xform->setObjectparams("main_id",$this->label->getId());
		$xform->setObjectparams("main_where",'id='.$this->label->getId()); // array("id"=>$this->label->getId())
		$xform->setObjectparams('getdata',true);
		$xform->setValueField('objparams',array('fragment', 'pz_screen_xform.tpl'));
		
		$xform->setObjectparams("form_action", "javascript:pz_loadFormPage('label_form','label_edit_form','".pz::url('screen','projects','labels',array("mode"=>'edit_label'))."')");
		$xform->setObjectparams("form_id", "label_edit_form");
		$xform->setHiddenField("label_id",$this->label->getId());
		$xform->setObjectparams('form_showformafterupdate',1);

		$xform->setValueField("text",array("name",rex_i18n::msg("label_name")));
		$xform->setValidateField("empty",array("name",rex_i18n::msg("error_label_name_empty")));

		$xform->setValueField("text",array("color",rex_i18n::msg("label_color")));
		$xform->setValidateField("empty",array("color",rex_i18n::msg("error_label_color_empty")));

		$xform->setValueField("text",array('border',rex_i18n::msg('label_border')));
		$xform->setValidateField("empty",array('border',rex_i18n::msg("error_label_bordercolor_empty")));

		$xform->setActionField("db",array('pz_label','id='.$this->label->getId())); // array("id"=>$this->label->getId())

		$return = $xform->getForm();

		if($xform->getObjectparams("actions_executed")) {
			$this->label->update();
			$return = $header.'<p class="xform-info">'.rex_i18n::msg("label_updated").'</p>'.$return;
			$return .= pz_screen::getJSUpdateLayer('labels_list',pz::url('screen','projects','labels',array("mode"=>'list')));
		}else
		{
			$return = $header.$return;
		}

		if($p["show_delete"])
		{
			$delete_link = pz::url("screen","projects","labels",array("label_id"=>$this->label->getId(),"mode"=>"delete_label"));
			$return .= '<div class="xform">
				<p><a class="bt17" onclick="check = confirm(\''.
				rex_i18n::msg("label_confirm_delete",htmlspecialchars($this->label->getName())).
				'\'); if (check == true) pz_loadPage(\'label_form\',\''.
				$delete_link.'\')" href="javascript:void(0);">- '.rex_i18n::msg("delete_label").'</a></p>
				</div>';
		}

		$return = '<div id="label_form"><div id="label_edit" class="design1col xform-edit">'.$return.'</div></div>';

		return $return;	
		
	}


	static function getAddForm($p = array()) 
	{
		$header = '
	        <header>
	          <div class="header">
	            <h1 class="hl1">'.rex_i18n::msg("add_label").'</h1>
	          </div>
	        </header>';

		$xform = new rex_xform;
		// $xform->setDebug(TRUE);

		$xform->setObjectparams("main_table",'pz_label');
		$xform->setObjectparams("form_action", "javascript:pz_loadFormPage('label_form','label_add_form','".pz::url('screen','projects','labels',array("mode"=>'add_label'))."')");
		$xform->setObjectparams("form_id", "label_add_form");
		
		$xform->setValueField('objparams',array('fragment', 'pz_screen_xform.tpl'));
		$xform->setValueField("text",array("name",rex_i18n::msg("label_name")));
		$xform->setValidateField("empty",array("name",rex_i18n::msg("error_label_name_empty")));
		$xform->setValueField("text",array("color",rex_i18n::msg("label_color")));
		$xform->setValidateField("empty",array("color",rex_i18n::msg("error_label_color_empty")));
		$xform->setValueField("text",array('border',rex_i18n::msg('label_border')));
		$xform->setValidateField("empty",array('border',rex_i18n::msg("error_label_bordercolor_empty")));
		$xform->setActionField("db",array()); // array("id"=>$label_id)
		$return = $xform->getForm();

		if($xform->getObjectparams("actions_executed")) {
			$label_id = $xform->getObjectparams("main_id");
			
			if($label = pz_label::get($label_id)) {
				$label->create();
			}
			$return = $header.'<p class="xform-info">'.rex_i18n::msg("label_added").'</p>'.$return;
			$return .= pz_screen::getJSUpdateLayer('labels_list',pz::url('screen','projects','labels',array("mode"=>'list')));
		}else
		{
			$return = $header.$return;
		}

		$return = '<div id="label_form"><div id="label_add" class="design1col xform-add">'.$return.'</div></div>';

		return $return;	
		
	}






}


?>