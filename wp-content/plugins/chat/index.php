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

        .sidebar ul li:hover {
            background-color: #e0e0e0;
        }

        .conversation {
            flex: 3;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
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
    </style>
    </style>
    <div class="container">
        <div class="sidebar">
            <ul id="message-list">
                <!-- Sample Message List -->
                <li>User 1</li>
                <li>User 2</li>
                <li>User 3</li>
                <!-- Add more users dynamically -->
            </ul>
        </div>
        <div class="conversation-container">
            <div class="conversation" id="conversation">
                <!-- Conversation will be populated here -->
            </div>
            <div class="input-container">
                <textarea id="message-input" placeholder="Type your message..."></textarea>
                <button id="send-btn">Send</button>
            </div>
        </div>
    </div>
    <script>
        // Sample data for demonstration
        const messages = {
            "User 1": [{
                    sender: "User 1",
                    message: "Hi there!",
                    timestamp: "12:30 PM"
                },
                {
                    sender: "User 2",
                    message: "Hey! How are you?",
                    timestamp: "12:32 PM"
                },
                {
                    sender: "User 1",
                    message: "I'm good, thanks!",
                    timestamp: "12:35 PM"
                }
            ],
            "User 2": [{
                    sender: "User 2",
                    message: "Hello!",
                    timestamp: "11:00 AM"
                },
                {
                    sender: "User 1",
                    message: "Hi! What's up?",
                    timestamp: "11:02 AM"
                },
                {
                    sender: "User 2",
                    message: "Not much, just chilling.",
                    timestamp: "11:05 AM"
                }
            ],
            "User 3": [{
                    sender: "User 3",
                    message: "Hey everyone!",
                    timestamp: "10:00 AM"
                },
                {
                    sender: "User 1",
                    message: "Hello!",
                    timestamp: "10:02 AM"
                }
            ]
        };

        const messageList = document.getElementById("message-list");
        const conversationDiv = document.getElementById("conversation");

        // Function to display conversation for a selected user
        function displayConversation(user) {
            conversationDiv.innerHTML = ""; // Clear previous conversation
            const messagesForUser = messages[user];
            if (messagesForUser) {
                messagesForUser.forEach(message => {
                    const messageDiv = document.createElement("div");
                    messageDiv.classList.add("message");
                    messageDiv.innerHTML = `
                        <span class="sender">${message.sender}:</span>
                        <span>${message.message}</span>
                        <span class="timestamp">${message.timestamp}</span>
                    `;
                    conversationDiv.appendChild(messageDiv);
                });
            }
        }

        // Populate message list
        for (const user in messages) {
            const listItem = document.createElement("li");
            listItem.textContent = user;
            listItem.addEventListener("click", () => displayConversation(user));
            messageList.appendChild(listItem);
        }

        // Display conversation for the first user by default
        displayConversation(Object.keys(messages)[0]);
    </script>
<?php
}




?>