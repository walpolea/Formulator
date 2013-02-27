<?php
class Formulator {

	protected $globalattrs = array( "accesskey", "class", "contenteditable", "contextmenu", "dir", "draggable", "dropzone", "hidden", "id", "lang", "spellcheck", "style", "tabindex", "title" );
	protected $eventattrs = array( "onblur", "onchange", "oncontextmenu", "onfocus", "onformchange", "onforminput", "oninput", "oninvalid", "onselect", "onsubmit", "onkeydown", "onkeyup", "onkeypress" );
	protected $formattrs = array( "accept-charset", "action", "autocomplete", "enctype", "method", "name", "novalidate", "target", "id", "class" );
	protected $inputattrs = array( "accept", "alt", "autocomplete", "autofocus", "checked", "disabled", "form", "formaction", "formenctype", "formmethod", "formnovalidate", "formtarget", "height", "list", "max", "maxlength", "min", "multiple", "name", "pattern", "placeholder", "readonly", "required", "size", "src", "step", "type", "value", "width", "id", "name", "class" );
	protected $labelattrs = array( "for", "from" );

	function __construct() {
		
	}
	
	function load($file) {
		$data = file_get_contents($file);
		if( $data ) {
			return $this->formify($data);
		} else {
			return false;
		}
		
	}
	/*
	formdata - json string or php array
	*/
	function formify( $formdata ) {
		
		if( !is_array($formdata) ) {
			$formdata = json_decode( $formdata, true );
        }
		
		//start the form
		$form = '<form ';
		
		//generate form parameters
		$formattrs = array_merge( $this->globalattrs, $this->formattrs );
		$formparams = $this->generateParams( $formdata, $formattrs );
		
		//add form parameters
		$form .= $formparams;
		
		$form .= '>'.PHP_EOL;
		
		$inputclass = null;
		if( isset($formdata["inputclass"]) ) {
			$inputclass = $formdata["inputclass"];
		}
		
		//add the form content
		foreach( $formdata["inputs"] as $input ) {
			$form .= $this->createInput( $input, $inputclass );
		}
		
		//end the form
		$form .= '</form>'.PHP_EOL;
		
		return $form;
		  
	}
	
	/*
	inputdata - json string or php array
	*/
	function createInput( $inputdata, $inputclass = null ) {
		
		if( !is_array($inputdata) ) {
			$inputdata = json_decode( $inputdata, true );
        }
		
		$inputattrs = array_merge( $this->globalattrs, $this->eventattrs, $this->inputattrs );
		$inputparams = $this->generateParams( $inputdata, $inputattrs );
		
		$input = '';
		
		if( isset( $inputclass ) ) {
			$input .= '<div class="'.$inputclass.'">'.PHP_EOL;	
		}
		
		//create the label
		if( isset( $inputdata["label"] ) ) {
			$inputdata["label"]["for"] = $inputdata["id"];
			$label = $this->createLabel( $inputdata["label"] );
			$input .= $label; 
		}
		
		if( $inputdata["type"] == "select" ) {
			
			$input .= '<select '.$inputparams.' />'.PHP_EOL;
			
			foreach( $inputdata["options"] as $option ) {
				$input .= '<option value="'.$option["value"].'">'.$option["label"].'</option>'.PHP_EOL;	
			}
			
			$input .= '</select><br>'.PHP_EOL;
			
			
		} else {
			//create the input
			$input .= '<input '.$inputparams.'/>'.PHP_EOL;
		}
		
		if( isset( $inputclass ) ) {
			$input .= '</div>'.PHP_EOL;	
		} else {
			$input .= '<br>'.PHP_EOL;	
		}
		
		return $input;
		
	}
	
	function createLabel( $labeldata ) {
		
		if( !is_array($labeldata) ) {
			$labeldata = json_decode( $labeldata, true );
        }
		
		$labelattrs = array_merge( $this->globalattrs, $this->eventattrs, $this->labelattrs );
		$labelparams = $this->generateParams( $labeldata, $labelattrs );
		
		$label = '<label '.$labelparams.'>'.$labeldata["content"].'</label>'.PHP_EOL;
		
		return $label;
		
	}
	
	protected function generateParams( $data, $attrs ) {
		
		$params = array();
		
		foreach( $attrs as $attr ) {
			if(isset( $data[$attr] ) ) {
				$params[$attr] = $data[$attr];
			}
		}
		
		$result = '';
		
		foreach( $params as $pk=>$pv ) {
			$result .= $pk.'="'.$pv.'" ';
		}
		
		return $result;
	}
	
}

/////////////////////////////////////////
if( isset( $_GET["form"] ) ) {
	$formdata = $_GET["form"];
} else if ( isset( $_POST["form"] ) ) {
	$formdata = $_POST["form"];
}

if( isset( $formdata ) ) {
	$f = new Formulator();
	header('Content-type: text/html');
	echo $f->formify($formdata);
}
?>