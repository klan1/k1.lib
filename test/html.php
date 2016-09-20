<?php

include_once '../src/init.php';

use k1lib\html\DOM as DOM;

DOM::start();

$head = DOM::html()->head();
$body = DOM::html()->body();

$head->set_title("HTML TEST");
$head->link_css("https://cdnjs.cloudflare.com/ajax/libs/foundation/6.2.3/foundation.css");
$body->append_child_tail(new \k1lib\html\script_tag("https://code.jquery.com/jquery-3.1.0.min.js"));
$body->append_child_tail(new \k1lib\html\script_tag("https://cdnjs.cloudflare.com/ajax/libs/foundation/6.2.3/foundation.min.js"));
$body->append_child_tail((new \k1lib\html\script_tag())->set_value("$(document).foundation();"));


$body->append_div("hello-world-class", "hello-world-id")->append_p("Hello world");

$body->append_div("another-div")->set_value("A free text inside the DIV element");

$body->get_element_by_id("hello-world-id")->append_p("With ID search object insert!");

DOM::html()->generate_tag(TRUE);
