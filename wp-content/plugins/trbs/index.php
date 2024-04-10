<?php

/**
 * Plugin Name: TRBS
 * Description: Plugin for TRBS
 * Version: 1.0
 * 
 *
 */
register_activation_hook(__FILE__, 'crudOperationsTable');
register_activation_hook(__FILE__, 'unapprovedTrbsTable');
function crudOperationsTable()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'trbs';
    $sql = "CREATE TABLE `$table_name` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `trb_code` varchar(20) NOT NULL,
    `tech_area` varchar(100) DEFAULT NULL,
    `year` varchar(10) DEFAULT NULL,
    `specific_invention` varchar(200) DEFAULT NULL,
    `level_no` varchar(50) DEFAULT NULL,
    `level_meaning` varchar(50) DEFAULT NULL,
    `user_id` varchar(5) NOT NULL,
    PRIMARY KEY(id)
    )$charset_collate;";
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

function unapprovedTrbsTable()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'unapproved_trbs';
    $sql = "CREATE TABLE `$table_name` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `trb_code` varchar(20) NOT NULL,
    `tech_area` varchar(100) DEFAULT NULL,
    `year` varchar(10) DEFAULT NULL,
    `specific_invention` varchar(200) DEFAULT NULL,
    `level_no` varchar(50) DEFAULT NULL,
    `level_meaning` varchar(50) DEFAULT NULL,
    `user_id` varchar(5) NOT NULL,
    PRIMARY KEY(id)
    )$charset_collate;";
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

//action for admin menu
// list for all the menu in the list
add_action('admin_menu', 'sa_display_trbs_menu');
function sa_display_trbs_menu()
{
    add_menu_page('TRBS', 'TRBS', 'manage_options', 'trbs_list', 'sa_trbs_list_callback', 'dashicons-list-view');
    add_submenu_page('trbs_list', 'TRBS List', 'TRBS List', 'manage_options', 'trbs_list', 'sa_trbs_list_callback');
    add_submenu_page('trbs_list', 'Add TRBS', 'Add TRBS', 'manage_options', 'trbs_add', 'sa_trbs_add_callback');
    add_submenu_page('trbs_list', 'Approve TRBS', 'Approve TRBS', 'manage_options', 'trbs_unapprove_list', 'sa_trbs_unapprove_list_callback');
    add_submenu_page(null, 'Edit TRBS', 'Edit TRBS', 'manage_options', 'trbs_edit', 'sa_trbs_edit_callback');
    add_submenu_page(null, 'Delete TRBS', 'Delete TRBS', 'manage_options', 'trbs_delete', 'sa_trbs_delete_callback');
    add_submenu_page(null, 'Add to Unapproved TRBS', 'Add to Unapproved TRBS', 'manage_options', 'addto_unapproved_trbs_list', 'sa_addto_unapproved_trbs_add_callback');
    add_submenu_page(null, 'Approve TRBS', 'Approve TRBS', 'manage_options', 'trbs_approve', 'sa_trbs_approve_callback');
}
//the function to delete trbs from the admin pannel/
// it takes in the parameter id and iterated through the data to find the data with passed id and deletes the data
function sa_trbs_delete_callback()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'trbs';
    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : "";
    if (!empty($id)) {
        $wpdb->delete("$table_name", ['id' => $id]);
    }
    wp_redirect(admin_url('admin.php?page=trbs_list'));
}

//the function to edit any given trbs from the list of the approved trbs the unapproved trbs cannot be edit , if an unaproved trbs need to be edited the trbs
//needs to be approved  and then the trbs can be edited
function sa_trbs_edit_callback()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'trbs';
    $msg = '';
    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : "";
    if (isset($_REQUEST['update'])) {
        if (!empty($id)) {
            $wpdb->update("$table_name", [
                'trb_code' => $_REQUEST['trb_code'],
                'tech_area' => $_REQUEST['tech_area'],
                'year' => $_REQUEST['year'],
                'specific_invention' => $_REQUEST['specific_invention'],
                'level_no' => $_REQUEST['level_no'],
                'level_meaning' => $_REQUEST['level_meaning']
            ], ['id' => $id]);
            $msg = 'Data updated successfully';
        };
    }
    $trbs = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name where id = %d", $id), ARRAY_A);

?>
    <style>
        input[type=text] {
            width: 100%;
        }

        input[type=submit] {
            background-color: lightblue;
            width: 150px;
            height: 40px;
            border-radius: 10px;
        }

        .label {
            font-weight: bold;
        }

        h2 {
            text-align: center;
        }
    </style>
    <h1 id="msg"><?php echo $msg; ?></h1>

    <form action="" method="post">
        <table style="min-width:50%">
            <tr colspan=2>
                <td>
                    <h2>Update TRBS</h2>
                </td>
            </tr>
            <tr>
                <td class="label">TRB Code</td>
                <td><input type="text" name="trb_code" id="trb_code" value="<?php echo $trbs['trb_code']; ?>" placeholder="Enter the TRB Code" required></td>
            </tr>
            <tr>
                <td class="label">Technology Area</td>
                <td><input type="text" name="tech_area" id="tech_area" value="<?php echo $trbs['tech_area']; ?>" placeholder="Enter the Technology Area" required></td>
            </tr>
            <tr>
                <td class="label">Year Commenced</td>
                <td><input type="text" name="year" id="year" value="<?php echo $trbs['year']; ?>" placeholder="Enter the year commenced" required></td>
            </tr>
            <tr>
                <td class="label">Specific Invention</td>
                <td><input type="text" name="specific_invention" id="specific_invention" value="<?php echo $trbs['specific_invention']; ?>" placeholder="Enter the specific invention" required></td>
            </tr>
            <tr>
                <td class="label">Level Number</td>
                <td><input type="text" name="level_no" id="level_no" value="<?php echo $trbs['level_no']; ?>" placeholder="Enter the level number" required></td>
            </tr>
            <tr>
                <td class="label">Level Meaning</td>
                <td><input type="text" name="level_meaning" id="level_meaning" value="<?php echo $trbs['level_meaning']; ?>" placeholder="Enter the level meaning" required></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" name="update" value="Update"></td>
            </tr>
        </table>


    <?php
}
//['trbs']
add_shortcode('trbs', 'sa_frontend_data');
function sa_frontend_data()
{
    if (isset($_REQUEST['submit'])) {
        $filter1 = $_REQUEST['table_name'];
        $filter2 = $_REQUEST['data_search'];
    } else {
        $filter1 = '';
        $filter2 = '';
    }
    ?>
        <div class="ct-search-form">
            <form id="trbs-search-form" action='' method='post' style="display: flex; align-items: center;">
                <select id="table" name="table_name" style="margin-right: 10px;width:200px">
                    <option value="trb_code">TRB Code</option>
                    <option value="tech_area">Technology Area</option>
                    <option value="year">Year Commenced</option>
                    <option value="specific_invention">Specific Invention</option>
                    <option value="level_no">Level Number</option>
                    <option value="level_meaning">Level Meaning</option>
                </select>
                <input type="text" id="data" name="data_search" style="margin-right: 10px;width:350px;">
                <input type="submit" value="Search" name="submit">
            </form>
            <?php
            if ($filter2 != '') {
                echo "<label style='text-align:center;margin-top:0px;'>Filters: Field =  $filter1 and Search Data =  $filter2 </label>";
            }
            ?>
        </div>
        <style>
            .modal {
                display: none;
                /* Hidden by default */
                position: fixed;
                /* Stay in place */
                z-index: 1;
                /* Sit on top */
                padding-top: 100px;
                /* Location of the box */
                left: 0;
                top: 0;
                width: 100%;
                /* Full width */
                height: 100%;
                /* Full height */
                overflow: auto;
                /* Enable scroll if needed */
                background-color: rgb(0, 0, 0);
                /* Fallback color */
                background-color: rgba(0, 0, 0, 0.4);
                /* Black w/ opacity */
                max-width:100vw !important;
            }

            /* Modal Content */
            .modal-content {
                background-color: #fefefe;
                margin: auto;
                padding: 20px;
                border: 1px solid #888;
                width: 40%;
            }

            /* The Close Button */
            .close {
                color: #aaaaaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
            }

            .close:hover,
            .close:focus {
                color: #000;
                text-decoration: none;
                cursor: pointer;
            }

            .pointer {
                cursor: pointer;
            }
        </style>
        <div id="myModal" class="modal">

            <!-- Modal content -->
            <div class="modal-content">
                <span class="close" id="span">&times;</span>
                <form action="" method="post">
                    <table>
                        <tr>
                            <td class="has-text-align-center"><strong>TRB Code</strong></td>
                            <td class="has-text-align-center" id="trb_code"></td>
                        </tr>
                        <tr>
                            <td class="has-text-align-center"><strong>Technology Area</strong></td>
                            <td class="has-text-align-center" id="tech_area"></td>
                        </tr>
                        <tr>
                            <td class="has-text-align-center"><strong>Year Commenced</strong></td>
                            <td class="has-text-align-center" id="year"></td>
                        </tr>
                        <tr>
                            <td class="has-text-align-center"><strong>Specific Invention</strong></td>
                            <td class="has-text-align-center" id="specific_invention"></td>
                        </tr>
                        <tr>
                            <td class="has-text-align-center"><strong>Level Number</strong></td>
                            <td class="has-text-align-center" id="level_no"></td>
                        </tr>
                        <tr>
                            <td class="has-text-align-center"><strong>Level Meaning</strong></td>
                            <td class="has-text-align-center" id="level_meaning"></td>
                        </tr>
                        <tr hidden>
                            <td class="has-text-align-center"><strong>User Id</strong></td>
                            <td class="has-text-align-center" id="userid"><input type="text" id="publisherid" name="userid"></td>
                        </tr>
                        <tr>
                            <td class="has-text-align-center"><strong>Send Message to publisher</strong></td>
                            <td class="has-text-align-center" id="">
                                <textarea id="message" name="message"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <button type="submit" name="sendmessages">
                                    Send&nbsp;Message
                                </button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>

        </div>
        <script>
            var modal = document.getElementById("myModal");
            var btn = document.getElementById("myBtn");

            function showPopUp(trb_code, tech_area, year, specific_invention, level_no, level_meaning, userid) {
                modal.style.display = "block";
                document.getElementById('trb_code').innerText = trb_code;
                document.getElementById('tech_area').innerText = tech_area;
                document.getElementById('year').innerText = year;
                document.getElementById('specific_invention').innerText = specific_invention;
                document.getElementById('level_no').innerText = level_no;
                document.getElementById('level_meaning').innerText = level_meaning;
                document.getElementById('userid').value = userid;
                document.getElementById('publisherid').value = userid;
                console.log(userid);

            };
            document.getElementById("span").onclick = function() {
                modal.style.display = "none";
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        </script>

        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . 'conversation';
        $msg = '';
        $userId = get_current_user_id();
        if (isset($_REQUEST['sendmessages'])) {
            // $wpdb->insert("$table_name", [
            //     'sender_id' => $userId,
            //     'receiver_id' => $_REQUEST['userid'],
            //     'message' => $_REQUEST['message'],
            // ]);
            function check_conversation($user1, $user2) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'conversation';
                $result = $wpdb->get_row( $wpdb->prepare(
                    "SELECT conversation_id FROM $table_name WHERE (user_one = %s AND user_two = %s) OR (user_one = %s AND user_two = %s)",
                    $user1,
                    $user2,
                    $user2,
                    $user1
                ) );
            
                return $result ? $result->conversation_id : false;
            }
    
            
            function insert_conversation_and_message($user1, $user2, $message, $sender) {
                global $wpdb;
                $conversation_table = $wpdb->prefix . 'conversation';
                $message_table = $wpdb->prefix . 'messages';
            
                // Insert into conversation table
                $wpdb->insert( $conversation_table, array(
                    'user_one' => $user1,
                    'user_two' => $user2
                ) );
                $conversation_id = $wpdb->insert_id;
            
                // Insert into messages table
                $wpdb->insert( $message_table, array(
                    'conversation_id' => $conversation_id,
                    'sender_id' => $sender,
                    'message' => $message
                ) );
            }
            
            // Function to insert a message into an existing conversation
            function insert_message($conversation_id, $sender, $message) {
                global $wpdb;
                $message_table = $wpdb->prefix . 'messages';
            
                $wpdb->insert( $message_table, array(
                    'conversation_id' => $conversation_id,
                    'sender_id' => $sender,
                    'message' => $message
                ) );
            }

            if(check_conversation($userId, $_REQUEST['userid'])) {
                insert_message(check_conversation($userId, $_REQUEST['userid']), $userId, $_REQUEST['message']);
            } else {
                insert_conversation_and_message($userId, $_REQUEST['userid'], $_REQUEST['message'], $userId);
            }

            // if ($wpdb->insert_id > 0) {
            //     $msg = "Saved Successfully";
            // } else {
            //     $msg = "Failed to save data $wpdb->last_error";
            // }
        }

        
        ?>

        <?php


        if ($filter2 == '') {
            echo "<h5 style='text-align:center;margin-top:0px;'>TRBS List</h5>";
        } else {
            echo "<h5 style='text-align:center;margin-top:0px;'>Search Results</h5>";
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'trbs';

        $sql = "SELECT * FROM $table_name WHERE 1=1 ";
        if ($filter1 == '' && $filter2 != '') {
            $sql .= "AND trb_code = '$filter2' ";
        } else if ($filter1 != '' && $filter2 != '') {
            $sql .= "AND $filter1 = '$filter2' ";
        }
        $results = $wpdb->get_results($sql);
        $i = 0;
        foreach ($results as $row) {
            if ($i == 0) {
        ?>
                <div class="wp-block-columns alignwide is-layout-flex wp-container-core-columns-layout-1 wp-block-columns-is-layout-flex <?php echo $i; ?>">
                <?php
            }
                ?>

                <div class="wp-block-column is-layout-flow wp-block-column-is-layout-flow pointer <?php echo $i; ?>" onclick="showPopUp(
                    '<?php echo $row->trb_code ?>',
                    '<?php echo $row->tech_area; ?>',
                    '<?php echo $row->year; ?>',
                    '<?php echo $row->specific_invention; ?>',
                    '<?php echo $row->level_no; ?>',
                    '<?php echo $row->level_meaning; ?>',
                    '<?php echo $row->user_id; ?>'
                    )">
                    <div class="wp-block-group has-background is-layout-flow wp-block-group-is-layout-flow" style="background-color:#ffffff">
                        <div class="wp-block-group__inner-container">
                            <p class="has-text-align-center"><strong>TRB Code</strong></p>
                            <p class="has-text-align-center"><?php echo $row->trb_code; ?></p>
                            <p class="has-text-align-center"><strong>Technology Area</strong></p>
                            <p class="has-text-align-center"><?php echo $row->tech_area; ?></p>
                            <p class="has-text-align-center"><strong>Year Commenced</strong></p>
                            <p class="has-text-align-center"><?php echo $row->year; ?></p>
                            <p class="has-text-align-center"><strong>Specific Invention</strong></p>
                            <p class="has-text-align-center"><?php echo $row->specific_invention; ?></p>
                            <p class="has-text-align-center"><strong>Level Number</strong></p>
                            <p class="has-text-align-center"><?php echo $row->level_no; ?></p>
                            <p class="has-text-align-center"><strong>Level Meaning</strong></p>
                            <p class="has-text-align-center"><?php echo $row->level_meaning; ?></p>
                        </div>
                    </div>
                </div>

                <?php
                if ($i  == 2) {
                    $i = 0;
                ?>
                </div>
            <?php

                } else {
                    $i++;
                }
            }
            if ($i != 0) {
            ?>
            </div>
        <?php
            }
        ?>


    <?php
}


function sa_trbs_list_callback()
{
    ?>
        <style>
            table {
                border-collapse: collapse;
                width: 90%;
            }

            th,
            td {
                text-align: left;
                padding: 8px;
            }

            tr:nth-child(even) {
                background-color: #fff;
            }

            .label {
                font-weight: bold;
            }

            h2 {
                text-align: center;
            }
        </style>
        <h2>TRBS List</h2>
        <table border="1">
            <tr>
                <th>S.N</th>
                <th>TRB Code</th>
                <th>Technology Area</th>
                <th>Year Commenced</th>
                <th>Specific Invention</th>
                <th>Level Number</th>
                <th>Level Meaning</th>
                <th>Action</th>
            </tr>
            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . 'trbs';
            $results = $wpdb->get_results("SELECT * FROM $table_name");
            $i = 1;
            foreach ($results as $row) {
            ?>
                <tr>
                    <td><?php echo $i++ ?></td>
                    <td><?php echo $row->trb_code; ?></td>
                    <td><?php echo $row->tech_area; ?></td>
                    <td><?php echo $row->year; ?></td>
                    <td><?php echo $row->specific_invention; ?></td>
                    <td><?php echo $row->level_no; ?></td>
                    <td><?php echo $row->level_meaning; ?></td>
                    <td><a href="<?php echo admin_url('admin.php?page=trbs_edit&id=' . $row->id); ?>">Edit</a> |
                        <a href="<?php echo admin_url('admin.php?page=trbs_delete&id=' . $row->id); ?>" class="delete-link">Delete</a>
                    </td>
                </tr>
            <?php
            }
            ?>
        </table>
        <script>
            jQuery(document).ready(function($) {
                $('.delete-link').click(function(e) {
                    e.preventDefault();
                    var confirmDelete = confirm('Are you sure you want to delete the item ?');
                    if (confirmDelete) {
                        window.location.href = $(this).attr('href');
                    }
                });
            });
        </script>
    <?php
}
function sa_trbs_add_callback()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'trbs';
    $msg = '';
    $userId = get_current_user_id();
    if (isset($_REQUEST['submit'])) {
        $wpdb->insert("$table_name", [
            'trb_code' => $_REQUEST['trb_code'],
            'tech_area' => $_REQUEST['tech_area'],
            'year' => $_REQUEST['year'],
            'specific_invention' => $_REQUEST['specific_invention'],
            'level_no' => $_REQUEST['level_no'],
            'level_meaning' => $_REQUEST['level_meaning'],
            'user_id' => $userId
        ]);


        if ($wpdb->insert_id > 0) {
            $msg = "Saved Successfully";
        } else {
            $msg = "Failed to save data";
        }
    }
    ?>
        <style>
            input[type=text] {
                width: 100%;
            }

            input[type=submit] {
                background-color: lightblue;
                width: 150px;
                height: 40px;
                border-radius: 10px;
            }

            .label {
                font-weight: bold;
            }

            h2 {
                text-align: center;
            }
        </style>
        <h1 id="msg"><?php echo $msg; ?></h1>

        <form action="" method="post">
            <table style="min-width:50%">
                <tr colspan=2>
                    <td>
                        <h2>Add TRBS</h2>
                    </td>
                </tr>
                <tr>
                    <td class="label">TRB Code</td>
                    <td><input type="text" name="trb_code" id="trb_code" placeholder="Enter the TRB Code" required></td>
                </tr>
                <tr>
                    <td class="label">Technology Area</td>
                    <td><input type="text" name="tech_area" id="tech_area" placeholder="Enter the Technology Area" required></td>
                </tr>
                <tr>
                    <td class="label">Year Commenced</td>
                    <td><input type="text" name="year" id="year" placeholder="Enter the year commenced" required></td>
                </tr>
                <tr>
                    <td class="label">Specific Invention</td>
                    <td><input type="text" name="specific_invention" id="specific_invention" placeholder="Enter the specific invention" required></td>
                </tr>
                <tr>
                    <td class="label">Level Number</td>
                    <td><input type="text" name="level_no" id="level_no" placeholder="Enter the level number" required></td>
                </tr>
                <tr>
                    <td class="label">Level Meaning</td>
                    <td><input type="text" name="level_meaning" id="level_meaning" placeholder="Enter the level meaning" required></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" name="submit" value="Save"></td>
                </tr>
            </table>
        </form>

    <?php
}
add_shortcode('add-trbs', 'sa_addto_unapproved_trbs_add_callback');
function sa_addto_unapproved_trbs_add_callback()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'unapproved_trbs';
    $msg = '';
    $userId = get_current_user_id();
    if (isset($_REQUEST['submit'])) {
        $wpdb->insert("$table_name", [
            'trb_code' => $_REQUEST['trb_code'],
            'tech_area' => $_REQUEST['tech_area'],
            'year' => $_REQUEST['year_no'],
            'specific_invention' => $_REQUEST['specific_invention'],
            'level_no' => $_REQUEST['level_no'],
            'level_meaning' => $_REQUEST['level_meaning'],
            'user_id' => $userId
        ]);


        if ($wpdb->insert_id > 0) {
            $msg = "Saved Successfully! Pending For approval";
        } else {
            $msg = "Failed to save data";
        }
    }
    ?>
        <style>
            input[type=text] {
                width: 100%;
            }

            input[type=submit] {
                background-color: blue;
                width: 150px;
                height: 40px;
                border-radius: 10px;
                font-size: 14px;
            }

            .label {
                font-weight: bold;
            }

            h2 {
                text-align: center;
            }

            table,
            tr,
            td {
                border: none;
            }
        </style>
        <h3 id="msg"><?php echo $msg; ?></h3>

        <form method="post">
            <table style="min-width:50%">
                <tr>
                    <td class="label">TRB Code</td>
                    <td><input type="text" name="trb_code" id="trb_code" placeholder="Enter the TRB Code" required></td>
                </tr>
                <tr>
                    <td class="label">Technology Area</td>
                    <td><input type="text" name="tech_area" id="tech_area" placeholder="Enter the Technology Area" required></td>
                </tr>
                <tr>
                    <td class="label">Year Commenced</td>
                    <td><input type="text" name="year_no" id="year" placeholder="Enter the year commenced" required></td>
                </tr>
                <tr>
                    <td class="label">Specific Invention</td>
                    <td><input type="text" name="specific_invention" id="specific_invention" placeholder="Enter the specific invention" required></td>
                </tr>
                <tr>
                    <td class="label">Level Number</td>
                    <td><input type="text" name="level_no" id="level_no" placeholder="Enter the level number" required></td>
                </tr>
                <tr>
                    <td class="label">Level Meaning</td>
                    <td><input type="text" name="level_meaning" id="level_meaning" placeholder="Enter the level meaning" required></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" name="submit" value="Save"></td>
                </tr>
            </table>
        </form>

    <?php
}

function sa_trbs_unapprove_list_callback()
{
    ?>
        <style>
            table {
                border-collapse: collapse;
                width: 90%;
            }

            th,
            td {
                text-align: left;
                padding: 8px;
            }

            tr:nth-child(even) {
                background-color: #fff;
            }

            .label {
                font-weight: bold;
            }

            h2 {
                text-align: center;
            }
        </style>
        <h2>TRBS List</h2>
        <table border="1">
            <tr>
                <th>S.N</th>
                <th>TRB Code</th>
                <th>Technology Area</th>
                <th>Year Commenced</th>
                <th>Specific Invention</th>
                <th>Level Number</th>
                <th>Level Meaning</th>
                <th>Action</th>
            </tr>
            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . 'unapproved_trbs';
            $results = $wpdb->get_results("SELECT * FROM $table_name");
            $i = 1;
            foreach ($results as $row) {
            ?>
                <tr>
                    <td><?php echo $i++ ?></td>
                    <td><?php echo $row->trb_code; ?></td>
                    <td><?php echo $row->tech_area; ?></td>
                    <td><?php echo $row->year; ?></td>
                    <td><?php echo $row->specific_invention; ?></td>
                    <td><?php echo $row->level_no; ?></td>
                    <td><?php echo $row->level_meaning; ?></td>
                    <td><a href="<?php echo admin_url('admin.php?page=trbs_approve&id=' . $row->id); ?>" class="approve-link">Approve</a>
                    </td>
                </tr>
            <?php
            }
            ?>
        </table>
        <script>
            jQuery(document).ready(function($) {
                $('.approve-link').click(function(e) {
                    e.preventDefault();
                    var confirmDelete = confirm('Are you sure you want to approve the item ?');
                    if (confirmDelete) {
                        window.location.href = $(this).attr('href');
                    }
                });
            });
        </script>
    <?php
}

function sa_trbs_approve_callback()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'unapproved_trbs';
    $new_table_name = $wpdb->prefix . 'trbs';

    $msg = '';
    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : "";

    if (!empty($id)) {
        $trbs = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name where id = %d", $id), ARRAY_A);

        $wpdb->insert("$new_table_name", [
            'trb_code' => $trbs['trb_code'],
            'tech_area' => $trbs['tech_area'],
            'year' => $trbs['year'],
            'specific_invention' => $trbs['specific_invention'],
            'level_no' => $trbs['level_no'],
            'level_meaning' => $trbs['level_meaning'],
            'user_id' => $trbs['user_id']
        ], ['%s', '%s', '%d', '%s', '%s', '%s', '%s']);
        $wpdb->delete("$table_name", ['id' => $id]);
        wp_redirect(admin_url('admin.php?page=trbs_unapprove_list'));
    };
}
    ?>