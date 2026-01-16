<?php
add_action('wp_ajax_update_order_status_merged', 'update_order_status_merged');

function update_order_status_merged() {

    global $wpdb;

    $domain = $_SERVER['HTTP_HOST'];
    $http = "http"; //change to https for staging and live

    // Retrieve order ID and status from AJAX request
    $id = $_POST['id'];
    $status = $_POST['status'];
    $order_no = $_POST['order_no'];
    $new_date = $_POST['date'];
    $order = wc_get_order($order_no);
    $totals_html = '';



    // Update the status in the database
    $table_name = $wpdb->prefix . 'split_schedule_orders';
    $result = $wpdb->update(
        $table_name,
        array('status' => $status),
        array('id' => $id),
        array('%s'), // Data format
        array('%d') // Where format
    );

    // Check if update was successful
    if ($result !== false) {

        echo "Status updated successfully";

        // If the schedule is marked as MADE send an email to Admin
        if($status == "made"){
            echo "Run the admin email for merged dates!!!";
            global $wpdb;
            $table_name = $wpdb->prefix . 'split_schedule_orders';
            $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id);
            //$sql = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d ORDER BY id ASC", $id);
            try {
                $results = $wpdb->get_results($sql, ARRAY_A);
                foreach ($results as $row) {
                    $schedule = $row['schedule'];
                    $date = $row['date'];
                    $order_no = $row['order_no'];
                    $date = new DateTime($date);
                    $formatted_date_pdf = $date->format('jS F Y');

                    $admin_email = "andrewh@materials-direct.com";

                    $subject = 'Reminder - Delivery Options Order ('.$schedule.')';
                    $message = '<h2 style="display: block; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left; font-size: 26px; color: #000000;">Reminder for order #'.$order_no.'</h2>';
                    $message .= '<p>Hi Admin,<br>This is a reminder that delivery '.$schedule.' for #'.$order_no.' is due to be shipped on '.$formatted_date_pdf.'. Remember to aim to ship the delivery one week before due date.</p>';
                    $message .= '<span style="margin-right:10px;">You can view the order <a href="'.$http.'://'.$domain.'/wp-admin/post.php?post='.$order_no.'&action=edit">HERE</a></span><span>and you can view the calendar entries <a href="'.$http.'://'.$domain.'/wp-admin/admin.php?page=view_admin">HERE</a></span>';
                    $headers = array('Content-Type: text/html; charset=UTF-8');
                    $mail_sent_3 = wp_mail( $admin_email, $subject, $message, $headers);
                    if ($mail_sent_3) {
                        echo "Email with invoice sent successfully.";
                    } else {
                        echo "Error sending invoice email.";
                    }
                }
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        }
        // If the schedule is marked as MADE send an email to Admin


    } else {
        echo "Error updating status: " . $wpdb->last_error;
    }

    // Always exit to avoid further execution
    wp_die();
}