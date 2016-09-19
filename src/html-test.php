<?php
include 'init.php';

$html_document = new k1lib\html\html_document_tag("es");
$html_document->head()->set_title("HTML TEST");
$html_document->body()->append_p("Hello world");
$html_document->body()->append_p("Hello world")->append_div("new-div")->append_p("Another P inside the DIV");

$html_document->generate_tag(TRUE);
