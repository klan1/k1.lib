<?php

function jsAlert($message,$tags=1,$back=0)
{
    $jsCode="";

    if ($tags==1) {$jsCode.= "<script language='javascript'>\n";}
    
    $jsCode.= "\talert('$message');\n";

    if ($back==1) {$jsCode.= "\thistory.back()\n";}
    if ($back==2) {$jsCode.= "\thistory.back()\nwindow.close();\n";}
    if ($tags==1) {$jsCode.= "</script>\n";}
    
    return $jsCode;
}

function jsErrWindow($err_message,$tags = 1	)
{
    $jsCode="";

    if ($tags==1) {$jsCode.= "<script language='javascript'>\n";}
    
    $jsCode.= <<<EOD
errWindow.document.open();
errWindow.document.write("<P>" + "$err_message");
EOD;

    if ($tags==1) {$jsCode.= "</script>\n";}
    
    return $jsCode;
}

function jsURL($url)
{
    $jsCode = "<script language='javascript'>\n";
    $jsCode.= "window.location='$url'";
	$jsCode.= "</script>\n";
    echo $jsCode;
	return 1;
}
?>