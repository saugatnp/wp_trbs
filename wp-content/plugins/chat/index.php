<?php

/**
 * Plugin Name: CHAT
 * Description: Plugin for Chat System
 * Version: 1.0
 *
 */
register_activation_hook(__FILE__, 'chatSystemTable');
register_activation_hook(__FILE__, 'messageTable');
function chatSystemTable()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'conversation';
    $user_table = $wpdb->prefix . 'users';
    $sql = "CREATE TABLE `$table_name` (
       `conversation_id` int(11) NOT NULL AUTO_INCREMENT,
        `user_one` bigint(20) unsigned DEFAULT NULL,
        `user_two` bigint(20) unsigned DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`conversation_id`),
        KEY `fk_user_one` (`user_one`),
        KEY `fk_user_two` (`user_two`),
        CONSTRAINT `fk_user_one` FOREIGN KEY (`user_one`) REFERENCES $user_table (`ID`),
        CONSTRAINT `fk_user_two` FOREIGN KEY (`user_two`) REFERENCES $user_table (`ID`)
      )$charset_collate;";
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

function messageTable()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'messages';
    $conversation_table = $wpdb->prefix . 'conversation';
    $user_table = $wpdb->prefix . 'users';

    $sql = "CREATE TABLE `$table_name` (
        `message_id` int(11) NOT NULL AUTO_INCREMENT,
        `conversation_id` int(11) NOT NULL,
        `message` text NOT NULL,
        `sender_id` bigint(20) unsigned DEFAULT NULL,
        `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`message_id`),
        KEY `fk_conversation_id` (`conversation_id`),
        KEY `fk_sender_id` (`sender_id`),
        CONSTRAINT `fk_conversation_id` FOREIGN KEY (`conversation_id`) REFERENCES $conversation_table (`conversation_id`),
        CONSTRAINT `fk_sender_id` FOREIGN KEY (`sender_id`) REFERENCES $user_table (`ID`)
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

add_shortcode('chat', 'sa_chat_list_callback');
function sa_chat_list_callback()
{
?>

    <style>
        .container {
            display: flex;
            min-height: 60vh;
            max-height: 100vh;
            max-width: 100% !important;
            margin: 0;
        }

        .conversation-container {
            flex: 3;
            display: flex;
            flex-direction: column;
        }

        .sidebar {
            flex: 1;
            background-color: #f2f2f2;
            border-right: 1px solid #ccc;
            overflow-y: auto;
            padding: 20px;
            min-width: 10vw;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* max-width: 20vw; */
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            margin-bottom: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            border-bottom: 1px solid #ddd;
            /* Add a bottom border to separate users */
            padding-bottom: 10px;
            /* Add some padding below each user */
        }

        .sidebar ul li:last-child {
            border-bottom: none;
            /* Remove bottom border from the last user */
        }

        /* .sidebar ul li:hover {
            background-color: #e0e0e0;
        } */

        .conversation {
            flex: 3;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            max-height: 600;
            overflow-y: scroll;
        }

        .message {
            margin-bottom: 10px;
            display: flex;
            flex-direction: column;
            border: 1px solid #ccc;
            /* Add border around each message */
            border-radius: 10px;
            /* Add border radius for rounded corners */
            padding: 0 10px 0 10px;
            background-color: white;
        }

        .sender {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .message-content {
            background-color: #e0e0e0;
            padding: 10px;
            border-radius: 10px;
            max-width: 70%;
            align-self: flex-start;
            /* Align sender's message to the left */
        }

        .receiver .message-content {
            background-color: #7fd2ff;
            /* Example color for receiver's message */
            align-self: flex-end;
            /* Align receiver's message to the right */
        }

        .receiver span {
            align-self: flex-end;
        }

        .timestamp {
            color: #666;
            font-size: 0.8em;
            align-self: flex-end;
            /* Align timestamp to the right */
        }

        .input-container {
            padding: 20px;
            background-color: #f2f2f2;
            border-top: 1px solid #ccc;
        }

        textarea {
            width: calc(100% - 20px);
            /* Adjust width to exclude padding */
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: none;
        }

        button {
            margin-top: 10px;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .name-btn {
            background-color: lightgray;
            color: black;
            width: 100%;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .name-btn:hover {
            background-color: darkgray;
        }

        center,
        b {
            font-size: 20px;
        }
    </style>


    <?php
    global $wpdb;
    $chats = [];
    global $chatWithUserName;
    $table_name = $wpdb->prefix . 'conversation';
    $user_table = $wpdb->prefix . 'users';
    $message_table = $wpdb->prefix . 'messages';
    $userId = get_current_user_id();
    $result =  $wpdb->get_results(
        "SELECT a.* ,b.user_nicename , b.user_nicename from $table_name a join $user_table b on ( case when a.user_one = $userId then a.user_two else a.user_one end ) = b.ID  where (a.user_one = $userId or a.user_two = $userId);"
    );
    if ($result) {
        $conversation_id = $result[0]->conversation_id;
        $chats = $wpdb->get_results("SELECT a.message , a.sender_id , user_one.user_nicename AS user_one_name, user_one.ID AS user_one_id, user_two.user_nicename AS user_two_name, user_two.ID AS user_two_id,a.sent_at FROM $message_table a join $table_name b on a.conversation_id = b.conversation_id JOIN $user_table user_one ON user_one.ID = b.user_one JOIN $user_table user_two ON user_two.ID = b.user_two WHERE a.conversation_id = $conversation_id order by a.sent_at asc
        ");
        if ($chats[0]->user_one_id == $userId) {
            $chatWithUserName = $chats[0]->user_two_name;
        } else {
            $chatWithUserName = $chats[0]->user_one_name;
        }
    }

    global $conversation_id;
    if (isset($_REQUEST['namebtn'])) {
        global $chatWithUserName;
        $conversation_id = $_REQUEST['conversation_id'];
        $chats = $wpdb->get_results("SELECT a.message , a.sender_id , user_one.user_nicename AS user_one_name, user_one.ID AS user_one_id, user_two.user_nicename AS user_two_name, user_two.ID AS user_two_id,a.sent_at FROM $message_table a join $table_name b on a.conversation_id = b.conversation_id JOIN $user_table user_one ON user_one.ID = b.user_one JOIN $user_table user_two ON user_two.ID = b.user_two WHERE a.conversation_id = $conversation_id order by a.sent_at asc
        ");
        if ($chats[0]->user_one_id == $userId) {
            $chatWithUserName = $chats[0]->user_two_name;
        } else {
            $chatWithUserName = $chats[0]->user_one_name;
        }
    }



    if (isset($_REQUEST['sendmessage'])) {
        $message = $_REQUEST['message'];
        $conversation_id = $_REQUEST['conversation_id'];
        $wpdb->insert(
            $message_table,
            array(
                'conversation_id' => $conversation_id,
                'sender_id' => $userId,
                'message' => $message
            )
        );
        $chats = $wpdb->get_results("SELECT a.message , a.sender_id , user_one.user_nicename AS user_one_name, user_one.ID AS user_one_id, user_two.user_nicename AS user_two_name, user_two.ID AS user_two_id,a.sent_at FROM $message_table a join $table_name b on a.conversation_id = b.conversation_id JOIN $user_table user_one ON user_one.ID = b.user_one JOIN $user_table user_two ON user_two.ID = b.user_two WHERE a.conversation_id = $conversation_id order by a.sent_at asc
        ");
        if ($chats[0]->user_one_id == $userId) {
            $chatWithUserName = $chats[0]->user_two_name;
        } else {
            $chatWithUserName = $chats[0]->user_one_name;
        }
    }








    ?>
    <div class="container">
        <div class="sidebar">
            <ul id="message-list">
                <li>
                    <center><b>User&nbsp;List</b></center>
                </li>
                <?php
                foreach ($result as $row) {
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='conversation_id' value='$row->conversation_id'>";
                    echo "<li><button class='name-btn' name='namebtn' type='submit' value='namebtn'>" . $row->user_nicename . "</button></li>";
                    echo "</form>";
                }
                ?>
            </ul>
        </div>
        <div class="conversation-container">
            <center><b>Messages&nbsp;(<?php echo $chatWithUserName ?>)</b></center>
            <?php
            if (isset($_REQUEST['namebtn']) || isset($_REQUEST['sendmessage']) || $chats) {
            ?>
                <div class="conversation" id="conversation">
                    <?php

                    foreach ($chats as $row) {
                        if ($row->sender_id == $userId) {
                            echo "<div class='message receiver'><span class='sender'>You</span><span class='message-content'>$row->message</span><span class='timestamp'>$row->sent_at</span></div>";
                        } else
                            echo "<div class='message'><span class='sender'>$chatWithUserName</span><span>$row->message</span><span class='timestamp'>$row->sent_at</span></div>";
                    }
                    ?>
                </div>

                <div class="input-container">
                    <form method="post">
                        <input type="hidden" name="conversation_id" value="<?php echo $conversation_id; ?>">
                        <textarea id="message-input" name="message" placeholder="Type your message..."></textarea>
                        <button id="send-btn" name="sendmessage">Send</button>
                    </form>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
<?php
}
?>