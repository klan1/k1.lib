<?php

/**
 * On screen solution for show messages to user.
 *
 * @author J0hnD03 <alejandro.trujillo@klan1.com>
 * @version 1.0
 * @package notifications
 */

namespace k1lib\html\notifications;

use k1lib\html\div;
use k1lib\html\foundation\callout;
use k1lib\html\ul;

class on_DOM extends common_code {

    static protected $section_name = 'on_DOM';

    static function queue_mesasage($message, $type = "primary", $tag_id = 'k1lib-output', $title = NULL) {
        parent::test();
        parent::_queue_mesasage(self::$section_name, $message, $type, $tag_id);
        if (!empty($title)) {
            self::queue_title($title, $type);
        }
    }

    static function queue_title($title, $type = "primary") {
        parent::_queue_mesasage_title(self::$section_name, $title, $type);
    }

    static function insert_messases_on_DOM($order = 'asc') {
        if (isset($_SESSION['k1lib_notifications']) && empty(self::$data)) {
            self::$data = & $_SESSION['k1lib_notifications'];
        }
        if (isset($_SESSION['k1lib_notifications_titles']) && empty(self::$data_titles)) {
            self::$data_titles = & $_SESSION['k1lib_notifications_titles'];
        }

        if (isset(self::$data[self::$section_name]) && !empty(self::$data[self::$section_name])) {
            if ($order == 'asc') {
                self::$data[self::$section_name] = array_reverse(self::$data[self::$section_name]);
            }
            $tag_object = self::$tpl->body()->get_element_by_id("k1lib-output");
            var_dump($tag_object);
            foreach (self::$data[self::$section_name] as $tag_id => $types_messages) {
                if ($tag_object->get_attribute("id") != $tag_id) {
                    $tag_object = self::$tpl->body()->get_element_by_id($tag_id);
                    if (empty($tag_object)) {
                        if (self::$tpl->body()->header()) {
                            $tag_object = self::$tpl->body()->header()->append_div(NULL, $tag_id);
                        } else {
                            $tag_object = self::$tpl->body()->append_child_head(new div(NULL, $tag_id));
                        }
                    } // else no needed
                } // else no needed
                foreach ($types_messages as $type => $messages) {
                    $call_out = new callout();
                    $call_out->set_class($type);
                    if (isset(self::$data_titles[self::$section_name][$type]) && !empty(self::$data_titles[self::$section_name][$type])) {
                        $call_out->set_title(self::$data_titles[self::$section_name][$type]);
                    }
                    if (count($messages) === 1) {
                        $call_out->set_message($messages[0]);
                        $call_out->append_to($tag_object);
                    } else {
                        $ul = new ul();
                        foreach ($messages as $message) {
                            $ul->append_li($message);
                        }
                        $call_out->set_message($ul);
                        $call_out->append_to($tag_object);
                    }
                }
            }
        }
        unset($_SESSION['k1lib_notifications']);
        unset($_SESSION['k1lib_notifications_titles']);
    }

}
