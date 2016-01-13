<?php

namespace k1lib\xml;

function do_xml($data_array) {
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
         ss:ExpandedRow_count="%NumRows%"
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
    foreach ($data_array as $rowNumber => $rowData) {
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
