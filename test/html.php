<?php

include_once '../src/init.php';

use k1lib\html\DOM as DOM;

DOM::start();

DOM::html()->head()->set_title("HTML TEST");

DOM::html()->head()->link_css("https://cdnjs.cloudflare.com/ajax/libs/foundation/6.2.3/foundation.css");
DOM::html()->body()->append_child_tail(new \k1lib\html\script_tag("https://code.jquery.com/jquery-3.1.0.min.js"));
DOM::html()->body()->append_child_tail(new \k1lib\html\script_tag("https://cdnjs.cloudflare.com/ajax/libs/foundation/6.2.3/foundation.min.js"));
DOM::html()->body()->append_child_tail((new \k1lib\html\script_tag())->set_value("$(document).foundation();"));

DOM::html()->body()->append_p("Hello world");

DOM::html()->body()->append_div("new-div")->append_p("An P element inside the DIV element");

DOM::html()->generate_tag(TRUE);
