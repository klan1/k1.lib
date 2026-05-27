<?php

/**
 * @license Apache-2.0
 * @package k1lib
 * @subpackage notifications
 * Notifications On DOM - Renders notification messages into the HTML DOM.
 */

namespace k1lib\notifications;

use k1lib\html\ul;

class on_DOM extends common_code {

    /**
     * The section name used for organizing on_DOM notifications.
     * @var string
     */
    protected static string $section_name = 'on_DOM';

    /**
     * Queues a notification message for display in the DOM.
     *
     * Adds a message to the notification queue with a specific type and target tag.
     * Optionally includes a title for the notification.
     *
     * @param string $message The notification message content to display
     * @param string $type The message type/class for styling (e.g., 'primary', 'success', 'warning', 'danger')
     * @param string $tag_id The HTML element ID where this notification will be rendered (default: 'k1lib-output')
     * @param string|null $title Optional title to display with the notification
     * @return void
     */
    public static function queue_mesasage(string $message, string $type = "primary", string $tag_id = 'k1lib-output', string|null $title = null): void {
        parent::_queue_mesasage(self::$section_name, $message, $type, $tag_id);
        if (!empty($title)) {
            self::queue_title($title, $type);
        }
    }

    /**
     * Queues a notification title for a specific type.
     *
     * Sets a title that will be displayed as a header for all notifications
     * of a specific type within the on_DOM section.
     *
     * @param string $title The title text to display with notifications
     * @param string $type The message type/class for styling (e.g., 'primary', 'success', 'warning')
     * @return void
     */
    public static function queue_title(string $title, string $type = "primary"): void {
        parent::_queue_mesasage_title(self::$section_name, $title, $type);
    }

    /**
     * Renders and displays all queued notifications into the DOM.
     *
     * Iterates through all stored notifications and outputs them either:
     * - As styled alert components within a template element, or
     * - As plain text output to stdout (cli mode)
     *
     * Single messages display as-is; multiple messages of the same type
     * are grouped into an unordered list (ul). After rendering, the
     * session data is cleared.
     *
     * @param string $order The display order: 'asc' (oldest first) or 'desc' (newest first). Default: 'asc'
     * @return void
     */
    public static function insert_messases_on_DOM(string $order = 'asc'): void {
        if (isset($_SESSION['k1lib_notifications']) && empty(self::$data)) {
            self::$data = & $_SESSION['k1lib_notifications'];
        }
        if (isset($_SESSION['k1lib_notifications_titles']) && empty(self::$data_titles)) {
            self::$data_titles = & $_SESSION['k1lib_notifications_titles'];
        }

        if (isset(self::$data[self::$section_name]) && !empty(self::$data[self::$section_name])) {
            if ($order === 'asc') {
                self::$data[self::$section_name] = array_reverse(self::$data[self::$section_name]);
            }
            foreach (self::$data[self::$section_name] as $tag_id => $types_messages) {
                if (!empty(self::$tag_id_override)) {
                    $tag_id = self::$tag_id_override;
                }

                if (isset(self::$tpl)) {
                    $tag_insert = self::$tpl->body()->get_element_by_id($tag_id);
                }

                foreach ($types_messages as $type => $messages) {
                    if (isset(self::$tpl)) {
                        $alert = new \k1app\template\mazer\components\alert();
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
                    } else {
                        if (count($messages) === 1) {
                            echo "({$type}) {$messages[0]}" . PHP_EOL;
                        } else {
                            $ul = new ul();
                            foreach ($messages as $message) {
                                echo "({$type}) {$message}" . PHP_EOL;
                            }
                        }
                    }
                }
            }
        }
        unset($_SESSION['k1lib_notifications']);
        unset($_SESSION['k1lib_notifications_titles']);
    }
}