<?php

/**
 * XML generation functions for k1lib
 *
 * @license Apache-2.0
 * @package k1lib
 */

namespace k1lib\xml;

/**
 * Generate XML/Excel spreadsheet from array data.
 *
 * @param array $data_array Array of data with headers in first row
 * @param bool $do_download If TRUE, force download; if FALSE, return XML string
 * @param string|null $file_name Download filename
 * @return string|null Returns XML string if $do_download is FALSE, otherwise dies
 */
function do_xml($data_array, $do_download = false, $file_name = null) {
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
     <Created>2019-11-07T06:25:24Z</Created>
     <Company>Klan1 Network</Company>
     <Version>16.00</Version>
    </DocumentProperties>
    <OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">
     <AllowPNG/>
    </OfficeDocumentSettings>
    <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
     <WindowHeight>17440</WindowHeight>
     <WindowWidth>28040</WindowWidth>
     <WindowTopX>7580</WindowTopX>
     <WindowTopY>29460</WindowTopY>
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
      <Alignment ss:Horizontal="Center" ss:Vertical="Bottom"/>
      <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="12" ss:Color="#FFFFFF"
       ss:Bold="1"/>
      <Interior ss:Color="#305496" ss:Pattern="Solid"/>
     </Style>
    </Styles>
    <Worksheet ss:Name="Names">
        <Table>
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
    foreach ($data_array as $rowNumber => $rowData) {
        if ($rowNumber == 0) {
            $headersCode .= "\t<Row ss:AutoFitHeight=\"0\">\n";
            foreach ($rowData as $headerName) {
                $headersCode .= "\t\t<Cell ss:StyleID=\"s62\">";
                $headersCode .= "<Data ss:Type=\"String\">{$headerName}</Data>";
                $headersCode .= "</Cell>\n";
                $numCols++;
            }
            $headersCode .= "\t</Row>\n";
            $numRows++;
        } else {
            $rowsCode .= "\t<Row ss:AutoFitHeight=\"0\">\n";
            foreach ($rowData as $dataValue) {
                $rowsCode .= "\t\t<Cell>";
                if (is_numeric($dataValue)) {
                    $rowsCode .= "<Data ss:Type=\"Number\">{$dataValue}</Data>";
                } else {
                    $rowsCode .= "<Data ss:Type=\"String\">{$dataValue}</Data>";
                }
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

    if ($do_download) {
        ob_clean();
        header('Content-Description: XML document download');
        header('Cache-Control: public');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . (empty($file_name) ? 'xml_report.xml' : $file_name));
        flush();
        echo $xmlTemplate;
        die();
    } else {
        return $xmlTemplate;
    }
}