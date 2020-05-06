<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	// $data == array(type (a or span), class, id, href)
	// @TODO Document this because it is pretty funky

/**
 *
 * @param array $data        	
 * @return string boolean array
 *         required:
 *         "text" key for the button text
 *         optional:
 *         "item" is not used here but is used by the create_button_bar script.
 *         this should be improved in a later version so it just focuses on
 *         either the class or id
 *         "tag" defaults to "a" but can be "div" "span" or other tag if the
 *         type=>"pass-through" then it just returns the "text" as-is without
 *         any further processing
 *         "href" defaults to "#" is only used if "type" is "a" (default)
 *         "class" defaults to "button" but can be replaced by any other classes
 *         as defined in the css or javascript
 *         "id" is completely optional
 *         "enclosure" is an option array with type class and id keys. This is
 *         used if the particular button needs an added container (for AJAX
 *         manipulation)
 *        
 *         EXAMPLES
 *         A button that provides a standard url (type and class are defaults
 *         "a" and "button");
 *         $data = array( "text" => "View Record", "href" =>
 *         "/index.php/record/view/2352");
 *         returns: <a href="/index.php/record/view/2352" class="button">View
 *         Record</a>
 *        
 *         A button that triggers a jquery script by class with an id that is
 *         parsed by the jQuery to parse for a relevant database table key:
 *         $data = array( "text" => "Edit Record", "type" => "span", "class" =>
 *         "button edit-record" "id" => "er_2532" );
 *         returns <span class="button edit-record" id="er_2532">Edit
 *         Record</span>
 *        
 *         A Button that needs a surrounding span for jQuery mainpulation:
 *         $data = array( "text" => "Edit Record", "type" => "span", "class" =>
 *         "button edit-record" "id" => "er_2532",
 *         "enclosure" => array("type" => "span", "id" => "edit-record-span" )
 *         );
 *         returns:<span id="edit-record-span"><span class="button edit-record"
 *         id="er_2532">Edit Record</span></span>
 *        
 */
function create_button($data)
{
	if (array_key_exists ( "text", $data )) {
		$tag = "a";
		$href = "";
		$title = "";
		$target = "";
		$style = "";
		$text = $data ["text"];
		if (array_key_exists ( "tag", $data )) {
			if (isset ( $data ["tag"] )) {
				$tag = $data ["tag"];
			}
		} else {
			if (array_key_exists ( "href", $data )) {
				$href = "href='" . $data ["href"] . "'";
			} else {
				$href = "href='#'";
			}
		}
		
		if (array_key_exists ( "target", $data )) {
			$target = "target='" . $data ["target"] . "'";
		}
		
		if (array_key_exists ( "title", $data )) {
			$title = "title ='" . $data ["title"] . "'";
		}
		if ($tag != "pass-through") {
			
			if (array_key_exists ( "class", $data )) {
				if (! is_array ( $data ["class"] )) {
					$data ["class"] = explode ( " ", $data ["class"] );
				}
			} else {
				$data ["class"] = array ();
			}
			
			if (array_key_exists ( "style", $data )) {
				$data ["class"] = array_merge ( get_button_style ( $data ["style"] ), $data ["class"] );
				$text = $text . add_fa_icon ( $data ["style"] );
			}
			
			if (array_key_exists ( "selection", $data ) && preg_match ( "/" . str_replace ( "/", "\/", $data ["selection"] ) . "/", $_SERVER ['REQUEST_URI'] )) {
				$data ["class"] [] = "active";
			}
			$class = sprintf ( "class='%s'", implode ( " ", $data ["class"] ) );
			
			$id = "";
			if (array_key_exists ( "id", $data )) {
				$id = "id='" . $data ["id"] . "'";
			}
			
			$button = "<$tag $href $id $class $target $title>$text</$tag>";
			
			if (array_key_exists ( "enclosure", $data )) {
				if (array_key_exists ( "tag", $data ["enclosure"] )) {
					$enc_tag = $data ["enclosure"] ["tag"];
					$enc_class = "";
					$enc_id = "";
					if (array_key_exists ( "class", $data ["enclosure"] )) {
						$enc_class = "class='" . $data ["enclosure"] ["class"] . "'";
					}
					if (array_key_exists ( "id", $data ["enclosure"] )) {
						$enc_id = "id='" . $data ["enclosure"] ["id"] . "'";
					}
					$button = "<$enc_tag $enc_class $enc_id>$button</$enc_tag>";
				}
			}
		} else {
			return $data ["text"];
		}
		return $button;
	} else {
		return FALSE;
	}
}

/**
 *
 * @param
 *        	compound array $buttons
 * @param array $options        	
 * @return string
 */
function create_button_bar($buttons, $type = "list", $options = NULL)
{
	$id = "";
	$selection = "";
	$class = "";
	if ($options) {
		if (array_key_exists ( "id", $options )) {
			$id = sprintf ( "id='%s'", $options ["id"] );
		}
		
		if (array_key_exists ( "selection", $options )) {
			$selection = $options ["selection"];
		}
		
		if (array_key_exists ( "class", $options )) {
			$class = $options ["class"];
		}
	}
	$button_list = array ();
	
	// the "selection" option indicates the page in the interface. Currently as
	// indicated by the uri->segment(1)
	foreach ( $buttons as $button ) {
		
		$button_list [] = create_button ( $button );
	}
	if ($type == "list") {
		
		$contents = implode ( "</li><li>", $button_list );
		$template = "<ul class='button-list'><li>$contents</li></ul>";
		$output = sprintf ( "<div class='btn-group %s'  %s>%s</div>", $class, $id, $template );
	} elseif ($type == "toolbar") {
		$contents = implode ( "", $button_list );
		// $template = "<ul class='button-list'><li>$contents</li></ul>";
		$output = sprintf ( "<div class='btn-group %s'  %s>%s</div>", $class, $id, $contents );
	}
	return $output;
}

function create_toolbar($buttons, $options = NULL)
{
	return create_button_bar ( $buttons, "toolbar", $options );
}

/**
 * create a field set that can be edited with AJAX on the fly.
 *
 * @param string $field_name        	
 * @param string $value        	
 * @param string $label        	
 * @param array $options
 *        	(envelope, class, attributes)
 */
function create_edit_field($field_name, $value, $label, $options = array())
{
	$envelope = "p";
	if (array_key_exists ( "envelope", $options )) {
		$envelope = $options ["envelope"];
	}
	
	$field_wrapper = "span";
	if (array_key_exists ( "field-wrapper", $options )) {
		$field_wrapper = $options ["field-wrapper"];
	}
	$id = "";
	$table = "";
	if (array_key_exists ( "table", $options ) && array_key_exists ( "id", $options )) {
		$table = $options ["table"];
		$id = $options ["id"];
	}
	$money = "";
	if (array_key_exists ( "money", $options )) {
		$classes [] = "usd";
	}
	
	/*
	 * The id is split with the "-" delimiter in javascript when the field is
	 * clicked
	 */
	$output [] = sprintf ( "<%s class='field-envelope' id='%s__%s__%s'>", $envelope, $table, $field_name, $id );
	if ($label != "") {
		$output [] = sprintf ( "<label>%s:&nbsp;</label>", $label );
	}
	if ($value == "") {
		$value = "&nbsp;";
	}
	
	/* add additional classes to the actual field */
	$classes [] = "edit-field field";
	if (array_key_exists ( "class", $options )) {
		$classes [] = $options ["class"];
	}
	$field_class = implode ( " ", $classes );
	$format = "";
	if (array_key_exists ( "format", $options )) {
		$format = sprintf ( "format='%s'", $options ["format"] );
	}
	
	/*
	 * Attributes are non-standard html attributes that are used by javascript
	 * these can include the type of input to be generated
	 */
	$attributes = "";
	if (array_key_exists ( "attributes", $options )) {
		$attributes = $options ["attributes"];
	}
	$output [] = sprintf ( "<%s class='%s' %s %s name='%s'>%s</%s></%s>", $field_wrapper, $field_class, $attributes, $format, $field_name, $value, $field_wrapper, $envelope );
	
	return implode ( "\r", $output );
}

function edit_field($field_name, $value, $label, $table, $id, $options = array())
{
	$options ["id"] = $id;
	$options ["table"] = $table;
	return create_edit_field ( $field_name, $value, $label, $options );
}

function inline_field($field_name, $object, $table, $options = array())
{
	$value = $object->$field_name;
	$id = $object->id;
	$label = ucfirst ( clean_string ( $field_name ) );
	if (array_key_exists ( "id", $options )) {
		$id = $options ['id'];
	}
	if (array_key_exists ( "label", $options )) {
		$label = $options ['label'];
	}
	if (! array_key_exists ( "class", $options )) {
		$options ['class'] = "editable";
	}
	return edit_field ( $field_name, $value, $label, $table, $id, $options );
}

/**
 * create a persistent field that updates the database on blur through ajax
 * DEPRECATED for inline_field
 * @param string $field_name        	
 * @param string $value        	
 * @param string $table        	
 * @param string $id        	
 * @param array $options        	
 * @return string
 */
/*
function live_field($field_name, $value, $table, $id, $options = array())
{
	if (TRUE) {
		$size = "auto";
		if (in_array ( "size", $options )) {
			$size = $options ["size"];
		}
		$envelope = "div";
		if (in_array ( "envelope", $options )) {
			$envelope = $options ["envelope"];
		}
		$label = "";
		if (in_array ( "label", $options )) {
			$label = sprintf ( "<label>%s</label>", $options ["label"] );
		}
		$output = sprintf ( "<%s class='field-envelope' id='%s__%s__%s'>%s
		<span class='live-field text' name='%s'>
		<input type='text' name='%s' value='%s' class='persistent' id='%s_%s' style='width:%spx'></span>
		</%s>", $envelope, $table, $field_name, $id, $label, $field_name, $field_name, $value, $field_name, $id, $size, $envelope );
	} else {
		$label = "";
		if (in_array ( "label", $options )) {
			$label = $options ["label"];
		}
		$output = edit_field ( $field_name, $value, $label, $table, $id, $options );
	}
	return $output;
}
*/
/**
 * create a checkbox with labels
 *
 * @param string $name        	
 * @param array $values        	
 * @param array $selections
 *        	@TODO add id option
 */
function create_checkbox($name, $values, $selections = array())
{
	$output = array ();
	foreach ( $values as $value ) {
		$checked = "";
		if (in_array ( $value->key, $selections )) {
			$checked = "checked";
		}
		$output [] = sprintf ( "<label>%s</label><input type='checkbox' name='%s' value='%s' %s/>&nbsp;", $value->value, $name, $value->key, $checked );
	}
	return implode ( "\r", $output );
}

function create_autocomplete($items, $selection, $id, $is_live = FALSE)
{
	$output [] = sprintf ( "<ul class='autocomplete-list' id='autocomplete-%s'>", $id );
	foreach ( $items as $item ) {
		$classes = array (
				"autocomplete-item" 
		);
		if ($is_live) {
			$classes = array (
					"autocomplete-item-live" 
			);
		}
		if ($item->value == $selection) {
			$classes [] = "active";
		}
		$output [] = sprintf ( "<li class='%s'>%s</li>", implode ( " ", $classes ), $item->value );
	}
	$output [] = "<li class='autocomplete-list-cancel button link'>Cancel</li>";
	
	$output [] = "</ul>";
	return implode ( "\r", $output );
}

function create_list($items)
{
	$output = array ();
	foreach ( $items as $item ) {
		array_push ( $output, $item->value );
	}
	return json_encode ( $output );
}

function get_button_style($style)
{
	$class = array (
			"btn" 
	);
	switch ($style) {
		case "delete" :
			$class [] = "btn-danger";
			break;
		case "link" :
			$class [] = "btn-link";
			break;
		case "notice" :
			$class [] = "btn-info";
			break;
		case "insert" :
		case "new" :
		// $class[] = "btn-warning";
		// break;
		case "update" :
		case "edit" :
		// $class[] = "btn-success";
		// break;
		
		default :
			$class [] = "btn-default";
	}
	return $class;
}

/**
 * accepts an plain array of values.
 * Searches for certain key terms and returns an icon if such exists.
 *
 * @param array $class        	
 * @return string
 */
function add_fa_icon($class = array())
{
	if (! is_array ( $class )) {
		$class = explode ( " ", $class );
	}
	if (in_array ( "reorder", $class )) {
		$output = "&nbsp;<i class='fa fa-shopping-cart'></i>";
	} elseif (in_array ( "export", $class )) {
		$output = "&nbsp;<i class='fa fa-cloud-download'></i>";
	} elseif (in_array ( "edit", $class )) {
		$output = "&nbsp;<i class='fa fa-pencil-square-o'></i>";
	} elseif (in_array ( "update", $class )) {
		$output = "&nbsp;<i class='fa fa-arrow-up'></i>";
	} elseif (in_array ( "new", $class )) {
		$output = "&nbsp;<i class='fa fa-star'></i>";
	} elseif (in_array ( "details", $class )) {
		$output = "&nbsp;<i class='fa fa-eye'></i>";
	} elseif (in_array ( "refine", $class )) {
		$output = "&nbsp;<i class='fa fa-search'></i>";
	} elseif (in_array ( "delete", $class )) {
		$output = "&nbsp;<i class='fa fa-exclamation-triangle'></i>";
	} elseif (in_array ( "print", $class )) {
		$output = "&nbsp;<i class='fa fa-print'></i>";
	} else {
		$output = "";
	}
	return $output;
}
