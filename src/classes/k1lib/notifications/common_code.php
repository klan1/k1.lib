<?php

/**
 * Base class for notification system that displays messages to users on screen.
 * Provides static methods to queue, store, and retrieve notification messages.
 *
 * @author Alejandro Trujillo J. <https://github.com/j0hnd03>
 * @package notifications
 */

namespace k1lib\notifications;

use k1lib\html\html_document;

class common_code {

    /**
     * Template object for rendering notifications in the DOM.
     * @var html_document|null
     */
    static protected html_document|null $tpl = null;

    /**
     * Override for the tag ID where notifications are inserted.
     * @var string|null
     */
    static protected string|null $tag_id_override = null;

    /**
     * Counter for notification items.
     * @var int
     */
    static protected int $data_count = 0;

    /**
     * Stores the notification data indexed by section, tag_id, type, and message.
     * @var array
     */
    static protected array $data = [];

    /**
     * Stores notification titles indexed by section and type.
     * @var array
     */
    static protected array $data_titles = [];

    /**
     * Retrieves all stored notification data.
     *
     * Enables the notification system if not already enabled and returns
     * the complete data array containing all queued notifications.
     *
     * @return array The stored notification data indexed by section, tag_id, type
     */
    static public function get_data(): array {
        self::is_enabled(true);
        return self::$data;
    }

    /**
     * Queues a notification message into the session storage.
     *
     * Stores a message with a specific type and target tag ID within a given section.
     * The message is persisted in the SESSION for later retrieval and display.
     *
     * @param string $section The section or context identifier (e.g., 'on_DOM')
     * @param string $message The notification message content to display
     * @param string $type The message type/class for styling (e.g., 'primary', 'success', 'warning')
     * @param string $tag_id The HTML element ID where this notification will be rendered
     */
    static protected function _queue_mesasage(string $section, string $message, string $type, string $tag_id): void {
        $_SESSION['k1lib_notifications'][$section][$tag_id][$type][] = $message;
        if (empty(self::$data)) {
            self::$data = & $_SESSION['k1lib_notifications'];
        }
    }

    /**
     * Queues a notification title for a specific section and type.
     *
     * Stores a title that will be displayed as a header for notifications
     * of a specific type within a given section.
     *
     * @param string $section The section or context identifier (e.g., 'on_DOM')
     * @param string $title The title text to display with notifications
     * @param string $type The message type/class for styling (e.g., 'primary', 'success', 'warning')
     */
    static protected function _queue_mesasage_title(string $section, string $title, string $type): void {
        $_SESSION['k1lib_notifications_titles'][$section][$type] = $title;
        if (empty(self::$data_titles)) {
            self::$data_titles = & $_SESSION['k1lib_notifications_titles'];
        }
    }

    /**
     * Clears all queued notifications from session storage.
     *
     * Removes both the notifications and their associated titles from the session,
     * effectively resetting the notification queue.
     *
     * @return void
     */
    static public function clean_queue(): void {
        unset($_SESSION['k1lib_notifications']);
        unset($_SESSION['k1lib_notifications_titles']);
    }

    /**
     * Sets the template object for rendering notifications in the DOM.
     *
     * Optionally allows overriding the default tag ID where notifications
     * will be inserted when rendered.
     *
     * @param html_document $tpl The template object with body() method to access DOM elements
     * @param string|null $tag_id_override Optional. Overrides the default tag ID for notification insertion
     * @return void
     */
    static function set_tpl(html_document $tpl, string|null $tag_id_override = null): void {
        self::$tpl = $tpl;
        if ($tag_id_override !== null) {
            self::$tag_id_override = $tag_id_override;
        }
    }
}