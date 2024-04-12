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
    $table_name = $wpdb->prefix . 'conversation';
    $user_table = $wpdb->prefix . 'users';
    $message_table = $wpdb->prefix . 'messages';
    $userId = get_current_user_id();
    $result =  $wpdb->get_results(
        "SELECT a.* ,b.user_nicename , b.user_nicename from $table_name a join $user_table b on ( case when a.user_one = $userId then a.user_two else a.user_one end ) = b.ID;"
    );


    // function getUserList($wpdb, $table_name, $user_table, $userId)
    // {
    //     $result = $wpdb->get_row(
    //         $wpdb->prepare(
    //             "SELECT a.* ,b.user_nicename from $table_name a join $user_table b on ( case when a.user_one = $userId then a.user_two else a.user_one end ) = b.ID;"
    //         )
    //     );
    //     return $result;
    // }
    // function getUserChat($conversation_id){
    //     global $wpdb;
    //     $table_name = $wpdb->prefix . 'conversation';
    //     $message_table = $wpdb->prefix . 'messages';
    //     $result =  $wpdb->get_results(
    //         $wpdb->get_results("SELECT a.message , a.sender_id , a.sent_at FROM $message_table a join $table_name b on a.conversation_id = b.conversation_id where a.conversation_id = $conversation_id order by a.sent_at asc;")
    //     );
    //     return $result;
    // }

    global $conversation_id;
    global $chatWithUserName;
    if (isset($_REQUEST['namebtn'])) {
        global $chatWithUserName;
        $conversation_id = $_REQUEST['conversation_id'];
        $chatWithUserName = $_REQUEST['user_nicename'];
        $chats = $wpdb->get_results("SELECT a.message , a.sender_id , a.sent_at FROM $message_table a join $table_name b on a.conversation_id = b.conversation_id where a.conversation_id = $conversation_id order by a.sent_at asc;
        ");
        
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
        $chats = $wpdb->get_results("SELECT a.message , a.sender_id , a.sent_at FROM $message_table a join $table_name b on a.conversation_id = b.conversation_id where a.conversation_id = $conversation_id order by a.sent_at asc;
        ");
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
                    echo "<input type='hidden' name='user_nicename' value='$row->user_nicename'>";
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
            if (isset($_REQUEST['namebtn']) || isset($_REQUEST['sendmessage'])) {
            ?>
                <div class="conversation" id="conversation">
                    <?php

                    foreach ($chats as $row) {
                        if ($row->sender_id == $userId) {
                            echo "<div class='message receiver'><span class='sender'>You</span><span class='message-content'>$row->message</span><span class='timestamp'>$row->sent_at</span></div>";
                        } else
                            echo "<div class='message'><span>$row->message</span><span class='timestamp'>$row->sent_at</span></div>";
                    }

                    ?>

                    <!-- Conversation will be populated here -->
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