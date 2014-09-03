<?php

/**
 * Debugging class
 *
 * Dumps/Displays the contents of a variable in a colored tabular format
 * Based on the idea, javascript and css code of Macromedia's ColdFusion cfdump tag
 * A much better presentation of a variable's contents than PHP's var_dump and print_r functions
 *
 * @package   Lib
 * @author    Kwaku Otchere <ospinto@hotmail.com>
 **/
class DBug
{

	/**
	 * Line where dbug has been called
	 */
	protected $line;

	/**
	 * File where dbug has been called
	 */
	protected $file;

	/**
	 * Is top level output
	 */
	protected $isTop;

	var $html;

	var $xmlDepth = array();

	var $xmlCData;

	var $xmlSData;

	var $xmlDData;

	var $xmlCount = 0;

	var $xmlAttrib;

	var $xmlName;

	var $arrType = array("array", "object", "resource", "boolean");


	protected function __construct($var, $forceType = "", $printResult = true)
	{

		$backtrace = debug_backtrace();
		$basedir = dirname(dirname(dirname(__FILE__)));
		$this->file = str_replace($basedir, "", $backtrace[1]["file"]);
		$this->line = $backtrace[1]["line"];
		$this->isTop = true;

		$this->html = "";

		$this->makeJsHeader();

		$arrAccept = array("array", "object", "xml"); //array of variable types that can be "forced"
		if (in_array($forceType, $arrAccept))
			$this->{"varIs" . ucfirst($forceType)}($var);
		else
			$this->checkType($var);

		if ($printResult) {
			echo $this->html;
		}
	}


	public function html()
	{
		return $this->html;
	}


	static function dump()
	{
		$args = func_get_args();
		foreach ($args as $var) {
			new dbug($var);
			echo "<br><br>";
		}
	}


	static function string()
	{
		$args = func_get_args();
		$html = "";
		foreach ($args as $var) {
			$dbug = new dbug($var, "", false);
			$html .= $dbug->html();
		}
		return $html;
	}


	static function stop()
	{
		$args = func_get_args();
		foreach ($args as $var) {
			new dbug($var);
			echo "<br><br>";
		}
		die();
	}

	public static function toArray($models)
	{
		if (!is_object($models) && !is_array($models))
			return $models;

		$data = array();

		$isModel = !is_array($models);
		$models = $isModel ? array($models) : $models;

		foreach ($models as $key => $model) {
			if (!is_a($model, 'CModel')) {
				$data[$key] = $model;
				continue;
			}

			$attributes = array_keys($model->attributes);
			if (property_exists($model, 'safeAttributes')) $attributes = $model->safeAttributes;
			if (property_exists($model, 'unsafeAttributes')) $attributes = array_diff($attributes, $model->unsafeAttributes);
			if (property_exists($model, 'addAttributes')) $attributes = array_merge($attributes, $model->addAttributes);

			foreach ($attributes as $attribute) {
				if (method_exists($model, $attribute))
					$dataItem[$attribute] = call_user_func(array($model, $attribute));
				else
					$dataItem[$attribute] = $model->{$attribute};
			}

			foreach ($model->relations() as $relation => $options) {
				if ($model->hasRelated($relation))
					if (is_scalar($model->{$relation}))
						$dataItem[$relation] = $model->{$relation};
					else
						$dataItem[$relation] = self::toArray($model->{$relation});
			}
			$data[$model->id] = $dataItem;
		}

		return $isModel ? reset($data) : $data;
	}

	/**
	 * TODO
	 */
	static function stopArray()
	{
		$args = func_get_args();
		foreach ($args as $var) {
			new dbug(self::toArray($var));
			echo "<br><br>";
		}
		die();
	}

	/**
	 * TODO
	 */
	static function dumpArray()
	{
		$args = func_get_args();
		foreach ($args as $var) {
			new dbug(self::toArray($var));
			echo "<br><br>";
		}
	}


	function makeJsHeader()
	{

		if (defined("DBUG_JAVASCRIPT")) {
			return;
		}

		define("DBUG_JAVASCRIPT", 1);
		$this->html .= <<<HTML
<script language="JavaScript">
/* code modified from ColdFusion's cfdump code */
    function dBug_toggleRow(source) {
        target=(document.all) ? source.parentElement.cells[1] : source.parentNode.lastChild
        dBug_toggleTarget(target,dBug_toggleSource(source));
    }

    function dBug_toggleSource(source) {
        if (source.style.fontStyle=='italic') {
            source.style.fontStyle='normal';
            source.title='click to collapse';
            return 'open';
        } else {
            source.style.fontStyle='italic';
            source.title='click to expand';
            return 'closed';
        }
    }

    function dBug_toggleTarget(target,switchToState) {
        target.style.display=(switchToState=='open') ? '' : 'none';
    }

    function dBug_toggleTable(source) {
        var switchToState=dBug_toggleSource(source);
        if(document.all) {
            var table=source.parentElement.parentElement;
            for(var i=1;i<table.rows.length;i++) {
                target=table.rows[i];
                dBug_toggleTarget(target,switchToState);
            }
        }
        else {
            var table=source.parentNode.parentNode;
            for (var i=1;i<table.childNodes.length;i++) {
                target=table.childNodes[i];
                if(target.style) {
                    dBug_toggleTarget(target,switchToState);
                }
            }
        }
    }
</script>

<style type="text/css">
    table.dBug_array,table.dBug_object,table.dBug_resource,table.dBug_resourceC,table.dBug_xml {
        font-family:Verdana, Arial, Helvetica, sans-serif; color:#000000; font-size:12px;
        border-collapse:collapse;
        margin: 2px;
    }

    table.dBug_array td,table.dBug_object td,table.dBug_resource td,table.dBug_resourceC td,table.dBug_xml td {
        padding: 2px;
    }

    .dBug_arrayHeader,
    .dBug_objectHeader,
    .dBug_resourceHeader,
    .dBug_resourceCHeader,
    .dBug_xmlHeader
        { font-weight:bold; color:#FFFFFF; }

    /* array */
    table.dBug_array { background-color:#006600; }
    table.dBug_array td { background-color:#FFFFFF; border: 1px solid #090;}
    table.dBug_array td.dBug_arrayHeader { background-color:#009900; }
    table.dBug_array td.dBug_arrayKey { background-color:#CCFFCC; }

    /* object */
    table.dBug_object { background-color:#0000CC; }
    table.dBug_object td { background-color:#FFFFFF; border: 1px solid #44c;}
    table.dBug_object td.dBug_objectHeader { background-color:#4444CC; }
    table.dBug_object td.dBug_objectKey { background-color:#CCDDFF; }

    /* resource */
    table.dBug_resourceC { background-color:#884488; }
    table.dBug_resourceC td { background-color:#FFFFFF; border: 1px solid #a6a;}
    table.dBug_resourceC td.dBug_resourceCHeader { background-color:#AA66AA; }
    table.dBug_resourceC td.dBug_resourceCKey { background-color:#FFDDFF; }

    /* resource */
    table.dBug_resource { background-color:#884488; }
    table.dBug_resource td { background-color:#FFFFFF; border: 1px solid #a6a;}
    table.dBug_resource td.dBug_resourceHeader { background-color:#AA66AA; }
    table.dBug_resource td.dBug_resourceKey { background-color:#FFDDFF; }

    /* xml */
    table.dBug_xml { background-color:#888888; }
    table.dBug_xml td { background-color:#FFFFFF; border: 1px solid #aaa; }
    table.dBug_xml td.dBug_xmlHeader { background-color:#AAAAAA; }
    table.dBug_xml td.dBug_xmlKey { background-color:#DDDDDD; }
</style>
HTML;

	}

	/**
	 * Create the main table header
	 *
	 * TODO
	 */
	function makeTableHeader($type, $header, $colspan = 2)
	{
		$this->html .= "<table cellspacing=2 cellpadding=3 class=\"dBug_" . $type . "\">
                <tr>
                    <td class=\"dBug_" . $type . "Header\" colspan=" . $colspan . " style=\"cursor:hand\" onClick='dBug_toggleTable(this)'>" . $header . ($this->isTop ? " [" . htmlspecialchars($this->file) . ": " . $this->line . "]" : "") . "</td>
                </tr>";
		if ($this->isTop) {
			$this->isTop = false;
		}
	}

	/**
	 * create the table row header
	 *
	 * TODO
	 */

	function makeTDHeader($type, $header)
	{
		$this->html .= "<tr>
                <td valign=\"top\" onClick='dBug_toggleRow(this)' style=\"cursor:hand\" class=\"dBug_" . $type . "Key\">" . $header . "</td>
                <td>";
	}

	/**
	 * close table row
	 *
	 * TODO
	 */
	function closeTDRow()
	{
		return "</td>\n</tr>\n";
	}

	/**
	 * error
	 *
	 * TODO
	 */
	function  error($type)
	{
		$error = "Error: Variable is not a";
		//thought it would be nice to place in some nice grammar techniques :)
		// this just checks if the type starts with a vowel or "x" and displays either "a" or "an"
		if (in_array(substr($type, 0, 1), array("a", "e", "i", "o", "u", "x")))
			$error .= "n";
		return ($error . " " . $type . " type");
	}

	/**
	 * check variable type
	 *
	 * TODO
	 */
	function checkType($var)
	{
		switch (gettype($var)) {
			case "resource":
				$this->varIsResource($var);
				break;
			case "object":
				$this->varIsObject($var);
				break;
			case "array":
				$this->varIsArray($var);
				break;
			/*
			case "boolean":
				$this->varIsBoolean($var);
				break;
			*/
			/*
			case "NULL":
				$this->html .= "NULL";
				break;
			*/
			case "string":
				$this->varIsString($var);
				break;

			default:
				$this->varIsSimple($var);
			/*
			$var=($var=="") ? "[empty string]" : $var;
			$this->html .= "<table cellspacing=0><tr>\n<td>".$var."</td>\n</tr>\n</table>\n";
			break;
			*/
		}
	}

	/**
	 * Quoting HTML special chars
	 *
	 * TODO
	 */
	function quoteHtml($text)
	{
		return nl2br(htmlspecialchars($text));
	}

	/**
	 * if variable is a string type
	 *
	 * TODO
	 */
	function varIsString($var)
	{

		$this->makeTableHeader("array", "string (" . strlen($var) . ")");
		$this->html .= "<tr>\n<td colspan=\"2\">" . $this->quoteHtml($var) . "</td>\n</tr>\n";
		$this->html .= "</table>";

	}

	/**
	 * if variable is a simple type
	 *
	 * TODO
	 */
	function varIsSimple($var)
	{

		$type = is_null($var) ? "undefined" : gettype($var);

		$this->makeTableHeader("array", $type);

		if (is_null($var)) {
			$var = "[NULL]";
		} else {
			$var = (gettype($var) == "boolean") ? ($var ? "true" : "false") : $var;
		}

		$this->html .= "<tr>\n<td colspan=\"2\">" . $this->quoteHtml($var) . "</td>\n</tr>\n";
		$this->html .= "</table>";

	}

	/**
	 * if variable is an array type
	 *
	 * TODO
	 */
	function varIsArray($var)
	{
		$this->makeTableHeader("array", "array (" . sizeof($var) . ")");
		if (is_array($var)) {
			if (!$var) {
				// array is empty
				$this->html .= "<tr>\n<td colspan=\"2\">[empty array]</td>\n</tr>\n";
			}
			foreach ($var as $key => $value) {
				$this->makeTDHeader("array", $key);
				if (in_array(gettype($value), $this->arrType))
					$this->checkType($value);
				else {
					if (is_null($value)) {
						$value = "[NULL]";
					} elseif (trim($value) == "") {
						$value = "[empty string]";
					}
					$this->html .= $this->quoteHtml($value) . "</td>\n</tr>\n";
				}
			}
		} else $this->html .= "<tr><td>" . $this->error("array") . $this->closeTDRow();
		$this->html .= "</table>";
	}

	/**
	 * if variable is an object type
	 *
	 * TODO
	 */
	function varIsObject($var)
	{

		$this->makeTableHeader("object", "object (" . get_class($var) . ")");
		$arrObjVars = get_object_vars($var);
		if (is_object($var)) {
			foreach ($arrObjVars as $key => $value) {
				$value = (is_string($value) && trim($value) == "") ? "[empty string]" : $value;
				$this->makeTDHeader("object", $key);
				if (in_array(gettype($value), $this->arrType))
					$this->checkType($value);
				else $this->html .= $this->quoteHtml($value) . $this->closeTDRow();
			}
			$arrObjMethods = get_class_methods(get_class($var));
			foreach ($arrObjMethods as $key => $value) {
				$this->makeTDHeader("object", $value);
				$this->html .= "[function]" . $this->closeTDRow();
			}
		} else $this->html .= "<tr><td>" . $this->error("object") . $this->closeTDRow();
		$this->html .= "</table>";
	}

	/**
	 * if variable is a resource type
	 *
	 * TODO
	 */
	function varIsResource($var)
	{
		$this->makeTableHeader("resourceC", "resource", 1);
		$this->html .= "<tr>\n<td>\n";
		switch (get_resource_type($var)) {
			case "fbsql result":
			case "mssql result":
			case "msql query":
			case "pgsql result":
			case "sybase-db result":
			case "sybase-ct result":
			case "mysql result":
				$db = current(explode(" ", get_resource_type($var)));
				$this->varIsDBResource($var, $db);
				break;
			case "gd":
				$this->varIsGDResource($var);
				break;
			case "xml":
				$this->varIsXmlResource($var);
				break;
			default:
				$this->html .= get_resource_type($var) . $this->closeTDRow();
				break;
		}
		$this->html .= $this->closeTDRow() . "</table>\n";
	}

	/**
	 * if variable is an xml type
	 *
	 * TODO
	 */
	function varIsXml($var)
	{
		$this->varIsXmlResource($var);
	}

	/**
	 * if variable is an xml resource type
	 *
	 * TODO
	 */
	function varIsXmlResource($var)
	{
		$xml_parser = xml_parser_create();
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0);
		xml_set_element_handler($xml_parser, array(&$this, "xmlStartElement"), array(&$this, "xmlEndElement"));
		xml_set_character_data_handler($xml_parser, array(&$this, "xmlCharacterData"));
		xml_set_default_handler($xml_parser, array(&$this, "xmlDefaultHandler"));

		$this->makeTableHeader("xml", "xml document", 2);
		$this->makeTDHeader("xml", "xmlRoot");

		//attempt to open xml file
		$bFile = (!($fp = @fopen($var, "r"))) ? false : true;

		//read xml file
		if ($bFile) {
			while ($data = str_replace("\n", "", fread($fp, 4096)))
				$this->xmlParse($xml_parser, $data, feof($fp));
		} //if xml is not a file, attempt to read it as a string
		else {
			if (!is_string($var)) {
				$this->html .= $this->error("xml") . $this->closeTDRow() . "</table>\n";
				return;
			}
			$data = $var;
			$this->xmlParse($xml_parser, $data, 1);
		}

		$this->html .= $this->closeTDRow() . "</table>\n";

	}

	/**
	 * parse xml
	 *
	 * TODO
	 */
	function xmlParse($xml_parser, $data, $bFinal)
	{
		if (!xml_parse($xml_parser, $data, $bFinal)) {
			die(sprintf("XML error: %s at line %d\n",
				xml_error_string(xml_get_error_code($xml_parser)),
				xml_get_current_line_number($xml_parser)));
		}
	}

	/**
	 * xml: inititiated when a start tag is encountered
	 *
	 * TODO
	 */
	function xmlStartElement($parser, $name, $attribs)
	{
		$this->xmlAttrib[$this->xmlCount] = $attribs;
		$this->xmlName[$this->xmlCount] = $name;
		$this->xmlSData[$this->xmlCount] = '$this->makeTableHeader("xml","xml element",2);';
		$this->xmlSData[$this->xmlCount] .= '$this->makeTDHeader("xml","xmlName");';
		$this->xmlSData[$this->xmlCount] .= '$this->html .= "<strong>' . $this->xmlName[$this->xmlCount] . '</strong>".$this->closeTDRow();';
		$this->xmlSData[$this->xmlCount] .= '$this->makeTDHeader("xml","xmlAttributes");';
		if (count($attribs) > 0)
			$this->xmlSData[$this->xmlCount] .= '$this->varIsArray($this->xmlAttrib[' . $this->xmlCount . ']);';
		else
			$this->xmlSData[$this->xmlCount] .= '$this->html .= "&nbsp;";';
		$this->xmlSData[$this->xmlCount] .= '$this->html .= $this->closeTDRow();';
		$this->xmlCount++;
	}

	/**
	 * xml: initiated when an end tag is encountered
	 *
	 * TODO
	 */
	function xmlEndElement($parser, $name)
	{
		for ($i = 0; $i < $this->xmlCount; $i++) {
			eval($this->xmlSData[$i]);
			$this->makeTDHeader("xml", "xmlText");
			$this->html .= (!empty($this->xmlCData[$i])) ? $this->xmlCData[$i] : "&nbsp;";
			$this->html .= $this->closeTDRow();
			$this->makeTDHeader("xml", "xmlComment");
			$this->html .= (!empty($this->xmlDData[$i])) ? $this->xmlDData[$i] : "&nbsp;";
			$this->html .= $this->closeTDRow();
			$this->makeTDHeader("xml", "xmlChildren");
			unset($this->xmlCData[$i], $this->xmlDData[$i]);
		}
		$this->html .= $this->closeTDRow();
		$this->html .= "</table>";
		$this->xmlCount = 0;
	}

	/**
	 * xml: initiated when text between tags is encountered
	 *
	 * TODO
	 */
	function xmlCharacterData($parser, $data)
	{
		$count = $this->xmlCount - 1;
		if (!empty($this->xmlCData[$count]))
			$this->xmlCData[$count] .= $data;
		else
			$this->xmlCData[$count] = $data;
	}

	/**
	 * xml: initiated when a comment or other miscellaneous texts is encountered
	 *
	 * TODO
	 */
	function xmlDefaultHandler($parser, $data)
	{
		//strip '<!--' and '-->' off comments
		$data = str_replace(array("&lt;!--", "--&gt;"), "", htmlspecialchars($data));
		$count = $this->xmlCount - 1;
		if (!empty($this->xmlDData[$count]))
			$this->xmlDData[$count] .= $data;
		else
			$this->xmlDData[$count] = $data;
	}

	/**
	 * if variable is a database resource type
	 *
	 * TODO
	 */
	function varIsDBResource($var, $db = "mysql")
	{
		$numrows = call_user_func($db . "_num_rows", $var);
		$numfields = call_user_func($db . "_num_fields", $var);
		$this->makeTableHeader("resource", $db . " result", $numfields + 1);
		$this->html .= "<tr><td class=\"dBug_resourceKey\">&nbsp;</td>";
		for ($i = 0; $i < $numfields; $i++) {
			$field[$i] = call_user_func($db . "_fetch_field", $var, $i);
			$this->html .= "<td class=\"dBug_resourceKey\">" . $field[$i]->name . "</td>";
		}
		$this->html .= "</tr>";
		for ($i = 0; $i < $numrows; $i++) {
			$row = call_user_func($db . "_fetch_array", $var, constant(strtoupper($db) . "_ASSOC"));
			$this->html .= "<tr>\n";
			$this->html .= "<td class=\"dBug_resourceKey\">" . ($i + 1) . "</td>";
			for ($k = 0; $k < $numfields; $k++) {
				$tempField = $field[$k]->name;
				$fieldrow = $row[($field[$k]->name)];
				$fieldrow = ($fieldrow == "") ? "[empty string]" : $fieldrow;
				$this->html .= "<td>" . $fieldrow . "</td>\n";
			}
			$this->html .= "</tr>\n";
		}
		$this->html .= "</table>";
		if ($numrows > 0)
			call_user_func($db . "_data_seek", $var, 0);
	}

	/**
	 * if variable is an image/gd resource type
	 *
	 * TODO
	 */
	function varIsGDResource($var)
	{
		$this->makeTableHeader("resource", "gd", 2);
		$this->makeTDHeader("resource", "Width");
		$this->html .= imagesx($var) . $this->closeTDRow();
		$this->makeTDHeader("resource", "Height");
		$this->html .= imagesy($var) . $this->closeTDRow();
		$this->makeTDHeader("resource", "Colors");
		$this->html .= imagecolorstotal($var) . $this->closeTDRow();
		$this->html .= "</table>";
	}
}
