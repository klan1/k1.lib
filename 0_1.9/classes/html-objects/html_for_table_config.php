<?php

/* HTML 4.01 TABLE VER2 */

use k1lib\html\classes as html_classes;
use k1lib\html\functions as html_functions;

class html_table_with_table_config {

    private $boardID;
    //SQL
    private $sqlQuery;
    private $sqlSearchFilter;
    private $sqlLimitStament;
    private $sqlResult;
    private $sqlResultCount = 0;
    private $rowCount = 0;
    // LIMIT
    private $pageSize = 50;
    private $pageEnd = null;
    private $pageActual = 0;
    private $tableConfigArray;
    private $tableFieldLinksArray;
    private $htmlTableMode = null;
    private $configViewRule;
    private $doSearchSystem = false;
    // Atributos de TABLE (HTML)
    private $id;
    private $class;
    private $other_attribs;
    // Atributos varios
    private $no_data;
    private $numbering;
    private $form_object;
    // codigo HTML
    private $html_code;

    function __construct($boarID = null) {
        $this->boardID = $boarID;
        $this->configViewRule = "show-table";
        $this->form_object = false;
        $this->numbering = false;
        $this->class = "";
        $this->other_attribs = "";
        $this->no_data = "No hay datos para mostrar";
    }

    /**
     * Make and add the WHERE SQL stament for the filter (search)
     * @param Array $formVarsArray
     * @return true 
     */
    function makeSqlFilter(Array $formVarsArray) {
        $doFilter = false;
        foreach ($formVarsArray as $searchValue) {
            if (!empty($searchValue)) {
                $doFilter = true;
            }
        }
        if ($doFilter) {
            if ($this->htmlTableMode == "SQL") {
                //looks if the SQL have the WHERE stament
                if (strstr(strtolower($this->sqlQuery), "where") !== false) {
                    $this->sqlSearchFilter = " ";
                    $i = 1;
                } else {
                    $this->sqlSearchFilter = " WHERE ";
                    $i = 0;
                }
                // and make the conditions, all with LIKE
                foreach ($formVarsArray as $field => $searchValue) {
                    if (isset($this->tableConfigArray[$field]) && (!empty($searchValue))) {
                        $this->sqlSearchFilter .= ($i >= 1) ? " AND " : "";
                        $this->sqlSearchFilter .= " $field LIKE '%$searchValue%'";
                        $i++;
                    }
                }
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getSqlSearchFilter() {
        if (!empty($this->sqlSearchFilter)) {
            return $this->sqlSearchFilter;
        } else {
            return false;
        }
    }

    function nextPage() {
        if ($this->pageActual < $this->pageEnd) {
            $this->pageActual++;
            return true;
        } else {
            return false;
        }
    }

    function prevPage() {
        if ($this->pageActual > 0) {
            $this->pageActual--;
            return true;
        } else {
            return false;
        }
    }

    function makeLimitStament() {
        if ($this->htmlTableMode == "SQL") {
            $tableAction = k1_get_url_level_value_by_name("table-action");
            $pageNumber = k1_get_url_level_value_by_name("page-number");
            if ($pageNumber > 0) {
                $this->pageActual = $pageNumber - 1;
            } else {
                $pageNumber = 0;
            }
            //looks if the SQL have the WHERE stament
            // and make the conditions, all with LIKE
            if ($pageNumber == "all") {
                $this->pageActual = 0;
//                $this->pageSize = $this->rowCount;
            } else {
                $this->sqlLimitStament = " LIMIT " . $this->pageActual * $this->pageSize . ", {$this->pageSize}";
            }
            return true;
        } else {
            return false;
        }
    }

    public function getRowCount() {
        return $this->rowCount;
    }

    public function getPageActual() {
        return $this->pageActual + 1;
    }

    public function getTotalPages() {
        $totalPages = $this->rowCount / $this->pageSize;
//        d($totalPages);
        return ceil($totalPages);
    }

    private function getSqlCount($sqlQuery) {
        global $db;

        // TODO: regular expresion for get the table name
        $fromPosition = strpos(strtolower($sqlQuery), "from");
        $sqlCount = "SELECT count(*) as rows " . substr($sqlQuery, $fromPosition);
        $countResult = k1_sql_query($db, $sqlCount, false);
        $this->rowCount = $countResult['rows'];
    }

    function modeSQL($sqlQuery, $tableConfigArray) {
        $this->htmlTableMode = "SQL";
        if (!is_array($tableConfigArray)) {
            die(__FUNCTION__ . " need an array to work on \$tableConfigArray");
        } else {
            $this->tableConfigArray = $tableConfigArray;
        }
        if (is_string($sqlQuery)) {
            $this->sqlQuery = $sqlQuery;
        } else {
            k1_show_error("La variable recibida no es un array ", __FILE__);
            return false;
        }
        $serializeSearchID = $this->boardID . '-search';
        $lastFormVars = k1_unserialize_var($serializeSearchID);
        $formVars = k1_get_all_request_vars($_POST, $serializeSearchID);
//        $formErrors = k1_form_check_values($formVars, $this->tableConfigArray, $db);
        if ((count($formVars) === 0) && (count($lastFormVars) > 0)) {
            $formVars = $lastFormVars;
            k1_serialize_var($formVars, $serializeSearchID);
        }

        if (!empty($formVars)) {
            $this->makeSqlFilter($formVars);
            $this->sqlQuery .= $this->sqlSearchFilter;
        }
        $this->getSqlCount($this->sqlQuery);
        $this->makeLimitStament();
        $this->sqlQuery .= $this->sqlLimitStament;

        global $db;
        $this->sqlResult = k1_sql_query($db, $this->sqlQuery, true, true);
        $this->sqlResultCount = count($this->sqlResult);
        if (empty($this->sqlSearchFilter)) {
            k1_serialize_var($this->sqlResult[0], $this->boardID . '-tableHeaders');
        }
    }

    public function getSqlResultCount() {
        return $this->sqlResultCount;
    }

    public function getSqlQuery() {
        return $this->sqlQuery;
    }

    function modeArray(&$sqlResult, $tableConfigArray) {
        $this->htmlTableMode = "ARRAY";
        if (!is_array($tableConfigArray)) {
            die(__FUNCTION__ . " need an array to work on \$tableConfigArray");
        } else {
            $this->tableConfigArray = $tableConfigArray;
        }
        if (is_array($sqlResult) || empty($sqlResult)) {
            $this->sqlResult = & $sqlResult;
        } else {
            k1_show_error("La variable recibida no es un array", __FILE__);
            return false;
        }
    }

    function doCode(net_klan1_dev_EasyControllerClass $controllerObject = null) {
        $serializedTableHeaders = k1_unserialize_var($this->boardID . '-tableHeaders');
        $serializedFormVars = k1_unserialize_var($this->boardID . '-search');
        if (empty($this->sqlResult) && empty($this->sqlSearchFilter)) {
            if (!empty($this->no_data)) {
                return "<p>{$this->no_data}</p>";
            } else {
                return "";
            }
        } elseif (empty($this->sqlResult)) {
            $this->sqlResult[0] = $serializedTableHeaders;
        }
        $table_object = new html_classes\table_tag($this->class, $this->id);
        $thead = $table_object->append_thead();
        $tr = $thead->append_tr();

        if (strstr($this->form_object, 'radio,checkbox') !== false) {
            $tr->append_th("Selector");
        } else {
            
        }
        if ($this->numbering == 1) {
            $tr->append_th("No.");
        }

        foreach ($this->sqlResult[0] as $rowFieldName) {
            if (substr($rowFieldName, 0, 1) != "_") {
                if (isset($this->tableConfigArray[$rowFieldName])) {
                    if ($this->tableConfigArray[$rowFieldName][$this->configViewRule]) { //$this->configViewRule =  show-table
                        $rowFieldName = $this->tableConfigArray[$rowFieldName]['label'];
                        $tr->append_th($rowFieldName);
                    }
                } else {
                    $tr->append_th($rowFieldName);
                }
            }
        }
        /**
         * SEARCH FORM
         */
//        if ($this->htmlTableMode == "SQL") {
//            $this->html_code.= "<form name = 'search-filter' action = '' method = 'post'>\n";
////            $this->html_code .= "<table>\n";
////            $this->html_code .= "\t<thead>\n";
//            if ($this->doSearchSystem) {
//                $this->html_code.= "\t\t<tr>\n";
//                // <th></th> -- html object
//                if (strstr($this->form_object, 'radio,checkbox') !== false) {
//                    $this->html_code.= "\t\t<th></th>\n";
//                } else {
//                    
//                }
//                // <th></th> -- numero
//                if ($this->numbering == 1) {
//                    $this->html_code.= "\t\t<th></th>\n";
//                }
//                // <th></th> -- DATA NAME
//                $cols = 0;
//                foreach ($this->sqlResult[0] as $rowFieldName) {
//                    if (isset($serializedFormVars[$rowFieldName])) {
//                        $formValue = $serializedFormVars[$rowFieldName];
//                    } else {
//                        $formValue = '';
//                    }
//                    if (isset($this->tableConfigArray[$rowFieldName])) {
//                        if ($this->tableConfigArray[$rowFieldName][$this->configViewRule]) { //$this->configViewRule =  show-table
//                            $cols++;
//                            $this->html_code.= "\t\t\t<th>\n";
////                            $this->html_code.= "\t\t\t\t<label>{$this->tableConfigArray[$rowFieldName]['label']}\n";
//                            $this->html_code.= "\t\t\t\t\t<input type='text' name='{$rowFieldName}' id='{$rowFieldName}' value=\"{$formValue}\" class='tiny' /></td>\n";
////                            $this->html_code.= "\t\t\t\t</label>\n";
//                            $this->html_code.= "\t\t\t</th>\n";
//                        }
//                    }
//                }
//
//                //unset the fields titles
//                // </tr>
//                $this->html_code.= "\t\t</tr>\n";
//                $this->html_code.= "\t\t<tr class=''>\n";
//                $this->html_code.= "\t\t\t<th colspan='{$cols}'>\n";
//                $this->html_code.= "\t\t\t\t<button type='submit'  class='tiny'>Filtrar</button>\n";
//                $this->html_code.= "\t\t\t</th>\n";
//                $this->html_code.= "\t\t</tr>\n";
//            }
////            $this->html_code .= "\t</thead>\n";
////            $this->html_code .= "</table>\n";
//            $this->html_code.= "</form>\n";
//        }
        //unset the fields titles

        unset($this->sqlResult[0]);
        $tbody = $table_object->append_tbody();

        // Ciclo de RENGLONES
        foreach ($this->sqlResult as $key => $row) {

            $actualRowKeysText = k1_table_keys_to_text($row, $this->tableConfigArray);

            $tr = $tbody->append_tr();

            if (strstr($this->form_object, 'radio,checkbox') !== false) {
                $tr->append_td("<input type='{$this->form_object}' name='__TABLE_KEYS__' value='$actualRowKeysText'>");
            }
            if ($this->numbering == 1) {
                $tr->append_td($key);
            }
            // Ciclo de CELDAS
            foreach ($row as $rowFieldName => $rowValue) {
                if (substr($rowFieldName, 0, 1) != "_") {
                    if (is_numeric($rowValue)) {
//                        $rowValue = number_format($rowValue);
                    }
                    // php Function apply from SQL 
                    if (strstr($rowValue, "function-") !== false) {
                        $function_to_execute = strstr($rowValue, "k1");
                        eval("\$rowValue = $function_to_execute;");
                    }
                    /**
                     * TODO: make this better and DO NOT repeat the code
                     */
                    $configViewValue = true;
                    if (isset($this->tableConfigArray[$rowFieldName][$this->configViewRule])) {
                        $configViewValue = $this->tableConfigArray[$rowFieldName][$this->configViewRule];
                    }
                    if ($configViewValue === false) {
                        continue;
                    } else {
                        // especific code for view:yes  
                    }
                    if (array_key_exists($rowFieldName, $this->tableFieldLinksArray)) {
                        if (substr($this->tableFieldLinksArray[$rowFieldName], 0, 12) == '[field-self]') {
                            $sprintf_url = $rowValue;
                            if (strstr($this->tableFieldLinksArray[$rowFieldName], ",") !== false) {
                                list($dummy, $rowValue) = explode(",", $this->tableFieldLinksArray[$rowFieldName]);
                            }
                        } else {
                            $sprintf_url = sprintf($this->tableFieldLinksArray[$rowFieldName], $actualRowKeysText);
                        }
                        if ($controllerObject !== null) {
                            $sprintf_url = parseUrlTag($sprintf_url, $controllerObject);
                        }
                        $link = k1_get_app_link($sprintf_url);
                        $tr->append_td("<a href='{$link}' rel='external'>{$rowValue}</a>");
                    } else {
                        $tr->append_td($rowValue);
                    }
                }
            }
        }
        return $table_object->generate_tag();
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setClass($class) {
        $this->class = $class;
    }

    public function getTableFieldLinksArray() {
        return $this->tableFieldLinksArray;
    }

    public function setTableFieldLinksArray($tableFieldLinksArray = Array(), $defaultUrlLink = null) {
        if (!is_array($tableFieldLinksArray)) {
            die(__METHOD__ . " need an array to work on \$tableFieldLinksArray");
        }
        // get the fields to make links
        if (count($tableFieldLinksArray) === 0) {
            // if there are not definitions
            $key_fields_array = k1_get_table_keys($this->tableConfigArray);
            foreach ($key_fields_array as $key => $value) {
                $this->tableFieldLinksArray[$key] = $defaultUrlLink;
            }
        } else {
            foreach ($tableFieldLinksArray as $key => $value) {
                if ($value === null) {
                    $this->tableFieldLinksArray[$key] = $defaultUrlLink;
                } else {
                    global $controllerObject;
                    if (is_object($controllerObject)) {
                        $value = parseUrlTag($value, $controllerObject);
                    }
                    $this->tableFieldLinksArray[$key] = $value;
                }
            }
        }
    }

    public function getHtmlTableMode() {
        return $this->htmlTableMode;
    }

    public function getDoSearchSystem() {
        return $this->doSearchSystem;
    }

    public function setDoSearchSystem($doSearchSystem) {
        $this->doSearchSystem = $doSearchSystem;
    }

    function doXml($resultData = null) {
        if (empty($resultData)) {
            $resultData = $this->sqlResult;
        }
        $headersCode = "";
        $rowsCode = "";
        $xmlTemplate = <<<HTML
<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
           xmlns:o="urn:schemas-microsoft-com:office:office"
           xmlns:x="urn:schemas-microsoft-com:office:excel"
           xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
           xmlns:html="http://www.w3.org/TR/REC-html40">
    <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
        <Author>Alejandro Trujillo J.</Author>
        <LastAuthor>Alejandro Trujillo J.</LastAuthor>
        <Created>2014-06-18T20:05:04Z</Created>
        <Company>Klan1 Network</Company>
        <Version>14.0</Version>
    </DocumentProperties>
    <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
        <WindowHeight>15260</WindowHeight>
        <WindowWidth>25600</WindowWidth>
        <WindowTopX>0</WindowTopX>
        <WindowTopY>0</WindowTopY>
        <ProtectStructure>False</ProtectStructure>
        <ProtectWindows>False</ProtectWindows>
    </ExcelWorkbook>
    <Styles>
        <Style ss:ID="Default" ss:Name="Normal">
            <Alignment ss:Vertical="Bottom"/>
            <Borders/>
            <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="12" ss:Color="#000000"/>
            <Interior/>
            <NumberFormat/>
            <Protection/>
        </Style>
        <Style ss:ID="s62">
            <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="12" ss:Color="#000000"
                  ss:Bold="1"/>
        </Style>
        <Style ss:ID="s64">
            <Font ss:FontName="Calibri" ss:Size="12" ss:Color="#003366"/>
        </Style>
    </Styles>
    <Worksheet ss:Name="Names">
        <Table ss:ExpandedColumnCount="%NumCols%"
         ss:ExpandedRowCount="%NumRows%"
         x:FullColumns="1" x:FullRows="1">
            <Column ss:Index="4" ss:AutoFitWidth="0" ss:Width="154.5"/>
            %Headers%
            %DataRows%
        </Table>
        <WorksheetOptions 
            xmlns="urn:schemas-microsoft-com:office:excel">
            <Print>
                <ValidPrinterInfo/>
                <HorizontalResolution>300</HorizontalResolution>
                <VerticalResolution>300</VerticalResolution>
            </Print>
            <Selected/>
            <Panes>
                <Pane>
                    <Number>3</Number>
                    <ActiveRow>1</ActiveRow>
                </Pane>
            </Panes>
            <ProtectObjects>False</ProtectObjects>
            <ProtectScenarios>False</ProtectScenarios>
        </WorksheetOptions>
    </Worksheet>
</Workbook>
HTML;
        $numCols = 0;
        $numRows = 0;
        foreach ($resultData as $rowNumber => $rowData) {
            if ($rowNumber == 0) {
                $headersCode .= "\t<Row>\n";
                foreach ($rowData as $headerName) {
                    $headersCode .= "\t\t<Cell>\n";
                    $headersCode .= "\t\t\t<Data ss:Type = \"String\">{$headerName}</Data>\n";
                    $headersCode .= "\t\t</Cell>\n";
                    $numCols++;
                }
                $headersCode .= "\t</Row>\n";
                $numRows++;
            } else {
                $rowsCode .= "\t<Row>\n";
                foreach ($rowData as $dataValue) {
                    $rowsCode .= "\t\t<Cell>\n";
                    $rowsCode .= "\t\t\t<Data ss:Type=\"String\">{$dataValue}</Data>\n";
                    $rowsCode .= "\t\t</Cell>\n";
                }
                $rowsCode .= "\t</Row>\n";
                $numRows++;
            }
        }
        $xmlTemplate = str_replace("%Headers%", $headersCode, $xmlTemplate);
        $xmlTemplate = str_replace("%NumCols%", $numCols, $xmlTemplate);
        $xmlTemplate = str_replace("%DataRows%", $rowsCode, $xmlTemplate);
        $xmlTemplate = str_replace("%NumRows%", $numRows, $xmlTemplate);

        return $xmlTemplate;
    }

}
