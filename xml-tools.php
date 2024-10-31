<?php
/**
 * Tools to create a correctly indented and permisive with the tags names XML
 * file.
 * 
 * The first function to call will be 'initialize_xml'.
 * 
 * Common flows are:
 * Create a tag with childs:
 * open_tag
 * ... [create some childs]
 * close_tag
 * 
 * Add a tag with a value:
 * add_tag
 * 
 * Add a tag with attributes, with or without childs:
 * open_tag_with_atributes
 * add_attribute [n times]
 * add_attribute($last_attribute=TRUE)
 * add_value or [create some childs]
 * close_tag
 * 
 * Lastly save to an xml file:
 * save_to_xml_file
 */

$xml_doc = '';
$indent_level = 0;

/**
 * Returns the char for a new line.
 */
function npxbe_new_line(){
    return "\n";
}

/**
 * Returns the number of tabs to match the current indentation.
 */
function npxbe_indent(){
    global $indent_level;
    $indentation = '';
    for($i = 0; $i < $indent_level; $i++){
        $indentation .= "\t";
    }
    
    return $indentation;
}

/**
 * Increases the indent level by one.
 */
function npxbe_increase_indent_level(){
    global $indent_level;
    
    $indent_level++;
}

/**
 * Reduces de indentation level by one, unless its already zero.
 */
function npxbe_reduce_indent_level(){
    global $indent_level;
    
    if($indent_level > 0){
        $indent_level--;
    }
}

/**
 * Sets the XML header with version and encoding especified.
 * 
 * @param string $version The XML version.
 * @param string $encoding The XML charset encoding.
 */
function npxbe_initialize_xml($version, $encoding){
    global $xml_doc;
    
    $xml_doc = '<?xml version="'.$version.'" encoding="' . $encoding . '" ?>';
}

/**
 * Opens a new tag and increases indent level.
 * 
 * @param string $tag_name The name of the new tag.
 */
function npxbe_open_tag($tag_name){
    global $xml_doc;
    
    $xml_doc .= npxbe_new_line() . npxbe_indent() . '<' . $tag_name . '>';
    
    npxbe_increase_indent_level();
}

/**
 * Closes a tag and reduces indent level if it has childs.
 * 
 * @param string $tag_name The name of the tag to close.
 * @param boolean $has_childs Indicates if the tag had childs
 */
function npxbe_close_tag($tag_name, $has_childs = TRUE){
    global $xml_doc;
    
    if($has_childs){
        npxbe_reduce_indent_level();
        $xml_doc .= npxbe_new_line() . npxbe_indent();
    }
    
    $xml_doc .= '</' . $tag_name . '>';
}

/**
 * Opens a tag that has attributes, so the tag declaration is left open.
 * Increases indent level if it has childs.
 * 
 * @param string $tag_name The name of the tag
 * @param boolean $has_childs Indicates if the tag had childs
 */
function npxbe_open_tag_with_atributes($tag_name, $has_childs = FALSE){
    global $xml_doc;
    
    $xml_doc .= npxbe_new_line() . npxbe_indent() . '<' . $tag_name;
    
    if($has_childs){
        npxbe_increase_indent_level();
    }
}

/**
 * Adds an attribute to apreviously opened tag with attributes. If this is the
 * last attribute to add to the tag, indicated by the $last_attribute parameter,
 * the tag definition will be closed. You can use the add_value function to
 * set the value of the tag. 
 * 
 * @param string $attr_name The name of the attribute
 * @param string $attr_value The value of the attribute
 * @param boolean $last_attribute Indicates if this is the last attribute
 */
function npxbe_add_attribute($attr_name, $attr_value, $last_attribute = FALSE){
    global $xml_doc;
    
    $xml_doc .= ' ' . $attr_name . '="' . $attr_value . '"';
    
    if($last_attribute){
        $xml_doc .= '>';
    }
}

/**
 * Adds a value to the previously opened tag with attributes.
 * 
 * @param string $tag_value The tag value
 */
function npxbe_add_value($tag_value){
    global $xml_doc;
    
    $xml_doc .= $tag_value;
}

/**
 * Adds a tag with a value. This tag will have no childs and no attributes.
 * 
 * @param string $tag_name The tag name
 * @param string $tag_value The tag value
 */
function npxbe_add_tag($tag_name, $tag_value){
    global $xml_doc;
    
    $xml_doc .= npxbe_new_line() . npxbe_indent() . '<' . $tag_name . '>' 
            . $tag_value . '</' . $tag_name . '>';
}

/**
 * Saves the content of the XML to a file.
 * 
 * @param string $path_to_file The path to the file.
 */
function npxbe_save_to_xml_file($path_to_file){
    global $xml_doc;
    
    file_put_contents($path_to_file, $xml_doc);
}