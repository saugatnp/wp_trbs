<?php

/**
 * Plugin Name: CHAT
 * Description: Plugin for Chat System
 * Version: 1.0
 *
 */
register_activation_hook(__FILE__, 'chatSystemTable');
function chatSystemTable()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'conversation';
    $sql = "CREATE TABLE `$table_name` (
        `message_id` int(11) NOT NULL AUTO_INCREMENT,
        `sender_id` bigint(20) unsigned DEFAULT NULL,
        `receiver_id` bigint(20) unsigned DEFAULT NULL,
        `message` text NOT NULL,
        `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`message_id`),
        KEY `fk_sender_id` (`sender_id`),
        KEY `fk_receiver_id` (`receiver_id`),
        CONSTRAINT `fk_receiver_id` FOREIGN KEY (`receiver_id`) REFERENCES `wp_users` (`ID`),
        CONSTRAINT `fk_sender_id` FOREIGN KEY (`sender_id`) REFERENCES `wp_users` (`ID`)
      )$charset_collate;";
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}


add_action('admin_menu', 'sa_display_chat_menu');
function sa_display_chat_menu()
{
    add_menu_page('CHAT', 'CHAT', 'manage_options', 'chat_list', 'sa_chat_list_callback', 'dashicons-list-view');
}


function sa_chat_list_callback(){
    
}




?>
