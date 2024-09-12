<?php

/**
 * On screen solution for show messages to user.
 *
 * @author J0hnD03 <alejandro.trujillo@klan1.com>
 * @version 1.0
 * @package notifications
 */

namespace k1lib\html\notifications;

use k1app\template\mazer\components\alert;
use k1lib\html\ul;

class on_DOM extends common_code
{

    protected static $section_name = 'on_DOM';

    public static function queue_mesasage($message, $type = "primary", $tag_id = 'k1lib-output', $title = null)
    {
        parent::_queue_mesasage(self::$section_name, $message, $type, $tag_id);
        if (!empty($title)) {
            self::queue_title($title, $type);
        }
    }

    public static function queue_title($title, $type = "primary")
    {
        parent::_queue_mesasage_title(self::$section_name, $title, $type);
    }

    public static function insert_messases_on_DOM($order = 'asc')
    {
        if (isset($_SESSION['k1lib_notifications']) && empty(self::$data)) {
            self::$data =  & $_SESSION['k1lib_notifications'];
        }
        if (isset($_SESSION['k1lib_notifications_titles']) && empty(self::$data_titles)) {
            self::$data_titles =  & $_SESSION['k1lib_notifications_titles'];
        }

        if (isset(self::$data[self::$section_name]) && !empty(self::$data[self::$section_name])) {
            if ($order == 'asc') {
                self::$data[self::$section_name] = array_reverse(self::$data[self::$section_name]);
            }
            foreach (self::$data[self::$section_name] as $tag_id => $types_messages) {
                if (!empty(self::$tag_id_override)) {
                    $tag_id = self::$tag_id_override;
                }
                $tag_insert = self::$tpl->body()->get_element_by_id($tag_id);

                foreach ($types_messages as $type => $messages) {
                    $alert = new alert();
                    $alert->set_class($type);
                    if (isset(self::$data_titles[self::$section_name][$type]) && !empty(self::$data_titles[self::$section_name][$type])) {
                        $alert->set_title(self::$data_titles[self::$section_name][$type]);
                    }
                    if (count($messages) === 1) {
                        $alert->set_message($messages[0]);
                        $alert->append_to($tag_insert);
                    } else {
                        $ul = new ul();
                        foreach ($messages as $message) {
                            $ul->append_li($message);
                        }
                        $alert->set_message($ul);
                        $alert->append_to($tag_insert);
                    }
                }
            }
        }
        unset($_SESSION['k1lib_notifications']);
        unset($_SESSION['k1lib_notifications_titles']);
    }

}
