<?php
function view_admin_content() {	

        // enqueue calendar.css
        wp_enqueue_style('calendar-css', get_stylesheet_directory_uri() . '/css/calendar.css', array(), '1.0', 'all');

        global $wpdb;

        if(isset($_GET['date'])){
            $m_date = $_GET['date'];
        } else {
            $m_date = "";
        }

        // Your table name
        $table_name = $wpdb->prefix . 'split_schedule_orders';

        $current_page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
        $results_per_page = 70; // Number of results you want to show per page
        $offset = ($current_page - 1) * $results_per_page;
        if(isset($_GET['sort_order'])){
            $sort = $_GET['sort_order'];
        } else {
            $sort = "DESC";
        }


        if(isset($_GET['Search']) && isset($_GET['calendar__search'])) {
            $search_term = sanitize_text_field($_GET['calendar__search']);
            $sql = $wpdb->prepare(
                "SELECT * FROM $table_name WHERE order_no LIKE %s ORDER BY date ASC LIMIT %d OFFSET %d",
                "%$search_term%",
                $results_per_page,
                $offset
            );
            $total_sql = $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE order_no LIKE %s",
                "%$search_term%"
            );
            // $sql = "SELECT * FROM $table_name WHERE order_no LIKE '%" . $wpdb->_real_escape($search_term) . "%' LIMIT $results_per_page OFFSET $offset";
            // $total_sql = "SELECT COUNT(*) FROM $table_name WHERE order_no LIKE '%" . $wpdb->_real_escape($search_term) . "%'";
        } else {
            $sql = "SELECT * FROM $table_name order by order_no $sort LIMIT $results_per_page OFFSET $offset";
            $total_sql = "SELECT COUNT(*) FROM $table_name";
            $search_term = ''; 
        }

        if (isset($_GET['Search']) && isset($_GET['calendar__search'])) {
            $search_term = sanitize_text_field($_GET['calendar__search']);
            $sql = $wpdb->prepare(
                "SELECT * FROM $table_name WHERE order_no LIKE %s ORDER BY date ASC LIMIT %d OFFSET %d",
                "%$search_term%",
                $results_per_page,
                $offset
            );
            $total_sql = $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE order_no LIKE %s",
                "%$search_term%"
            );
        }

        $total_results = $wpdb->get_var($total_sql);
        $total_pages = ceil($total_results / $results_per_page);

        ?>
        <div class="calendar">
        <div class="calendar__admin-col-left">
        <h1>Delivery Options Admin</h1>
        <table class="calendar__table">
            <thead>
                <tr>
                    <th style="height: 64px;" class="calendar__sort">
                        <span>Order No.</span>
                        <span class="calendar__sort-icons">
                            <a href="?page=view_admin&calendar__search=<?php echo urlencode($search_term); ?>&sort_order=ASC&page_num=<?php echo $current_page; ?>"><div class="calendar__triangle-up"></div></a>
                            <a href="?page=view_admin&calendar__search=<?php echo urlencode($search_term); ?>&sort_order=DESC&page_num=<?php echo $current_page; ?>"><div class="calendar__triangle-down"></div></a>
                        </span>
                    </th>
                    <th>Merged Shipment?</th>
                    <th>Product</th>
                    <th>Date</th>
                    <th>On Backorder</th>
                    <th>View Delivery Note</th>
                    <th>View Customer Invoice</th>
                    <th>Change Status</th>
                    <!-- <th style="width: 155px;">Merged Single Shipments</th> -->
                    <th style="width: 75px;">Select Shipments</th>
                    <th>Manage Merged Shipments</th>
                    <th>Shipment Tracking</th>
                    <th>Shipment Tracking URL</th>
                </tr>
            </thead>
            <tbody>
        <?php
        try {
            // Execute the query
            $results = $wpdb->get_results($sql, ARRAY_A);

            // Group dates by order_no and count each date occurrence per order
            $date_counts_by_order = [];

            foreach ($results as $row) {
                $order = $row['order_no'];
                $date = $row['date'];
                if (!isset($date_counts_by_order[$order])) {
                    $date_counts_by_order[$order] = [];
                }
                if (!isset($date_counts_by_order[$order][$date])) {
                    $date_counts_by_order[$order][$date] = 0;
                }
                $date_counts_by_order[$order][$date]++;
            }
            // Group dates by order_no and count each date occurrence per order

            if ($wpdb->num_rows > 0) {
            // Process each row and build HTML table rows

                    $date_counts = array_count_values(array_column($results, 'date'));
                    $date_seen_counter = [];

                    foreach ($results as $row) {
                        // Fix to remove query monitor notice errors
                        $id = isset($row['id']) ? $row['id'] : '';
                        $title = isset($row['title']) ? $row['title'] : '';
                        $date = isset($row['date']) ? $row['date'] : '';
                        $order_no = isset($row['order_no']) ? $row['order_no'] : '';
                        $notes = isset($row['notes']) ? $row['notes'] : '';
                        $part_shape = isset($row['part_shape']) ? $row['part_shape'] : '';
                        $part_shape_link = isset($row['part_shape_link']) ? $row['part_shape_link'] : '';
                        $width = isset($row['width']) ? $row['width'] : '';
                        $length = isset($row['length']) ? $row['length'] : '';
                        $dimension_type = isset($row['dimension_type']) ? $row['dimension_type'] : '';
                        $on_backorder = isset($row['on_backorder']) ? $row['on_backorder'] : 0;
                        $qty = isset($row['qty']) ? $row['qty'] : '';
                        $status = isset($row['status']) ? $row['status'] : '';
                        $schedule = isset($row['schedule']) ? $row['schedule'] : '';
                        $schedule_qty = isset($row['schedule_qty']) ? $row['schedule_qty'] : '';
                        $pdf = isset($row['pdf']) ? $row['pdf'] : '';
                        $last = isset($row['last']) ? $row['last'] : '';
                        $make_active = isset($row['make_active']) ? $row['make_active'] : '';
                        $is_merged = isset($row['is_merged']) ? $row['is_merged'] : '';
                        $is_merged_disable = isset($row['is_merged_disable']) ? $row['is_merged_disable'] : '';
                        $shipment_tracking = isset($row['shipment_tracking']) ? $row['shipment_tracking'] : '';
                        $shipment_tracking_url = isset($row['shipment_tracking_url']) ? $row['shipment_tracking_url'] : '';

                        /*
                        $id = $row['id'];
                        $title = $row['title'];
                        $date = $row['date'];
                        $order_no = $row['order_no'];
                        $notes = $row['notes'];
                        $part_shape = $row['part_shape'];
                        $part_shape_link = $row['part_shape_link'];
                        $width = $row['width'];
                        $length = $row['length'];
                        $dimension_type = $row['dimension_type'];
                        $on_backorder = $row['on_backorder'];
                        $qty = $row['qty'];
                        $status = $row['status'];
                        $schedule = $row['schedule'];
                        $schedule_qty = $row['schedule_qty'];
                        $pdf = $row['pdf'];
                        $last = $row['last'];
                        $make_active = $row['make_active'];
                        $is_merged = $row['is_merged'];
                        $is_merged_disable = $row['is_merged_disable'];
                        $shipment_tracking = $row['shipment_tracking'];
                        $shipment_tracking_url = $row['shipment_tracking_url'];
                        */

                        if (isset($date_counts_by_order[$order_no][$date]) && $date_counts_by_order[$order_no][$date] > 1) {
                            // Initialize if not already
                            if (!isset($date_seen_counter[$order_no][$date])) {
                                $date_seen_counter[$order_no][$date] = 1;
                            } else {
                                $date_seen_counter[$order_no][$date]++;
                            }
                        
                            $match_number = $date_seen_counter[$order_no][$date];
                            $merged_shipment_text = $match_number;
                        } else {
                            $merged_shipment_text = "Null";
                        }
                        


                        // HTML for status select dropdown
                        $status_select = '
                        <form action="" method="post" class="calendar__form-'. $last .'">
                        <select class="calendar__select-status" name="status">
                            <option value="pending" ' . ($status == "pending" ? "selected" : "") . '>Pending</option>
                            <option value="made" ' . ($status == "made" ? "selected" : "") . '>Made</option>
                            <option value="dispatch" ' . ($status == "dispatch" ? "selected" : "") . '>Dispatch</option>
                        </select>
                        <input type="hidden" name="id" value="' . $id . '">
                        <input type="hidden" name="order_no" value="' . $order_no . '">
                        <input type="hidden" name="date" value="' . $date . '">
                        <a class="calender__btn-update" href="/wp-admin/admin.php?page=view_admin&calendar__search='.$search_term.'&Search=search">Update</a>
                        </form>
                        ';


                        $status_select_merged_date = '
                        <form action="" method="post" class="calendar__form-'. $last .'">
                        <select class="calendar__select-status-merged-date" name="status">
                            <option value="pending" ' . ($status == "pending" ? "selected" : "") . '>Pending</option>
                            <option value="made" ' . ($status == "made" ? "selected" : "") . '>Made</option>
                            <option value="dispatch" ' . ($status == "dispatch" ? "selected" : "") . '>Dispatch</option>
                        </select>
                        <input type="hidden" name="id" value="' . $id . '">
                        <input type="hidden" name="order_no" value="' . $order_no . '">
                        <input type="hidden" name="date" value="' . $date . '">
                        <a class="calender__btn-update" href="/wp-admin/admin.php?page=view_admin&calendar__search='.$search_term.'&Search=search">Update</a>
                        </form>
                        ';

                        $calendar_search = $_GET['calendar__search'] ?? '';
                        $status_select_disable = '
                        <form action="" method="post" class="calendar__form-disable">
                        <select class="calendar__select-status" name="status" style="pointer-events: none; color:#999;">
                            <option value="dispatch">Dispatch</option>
                        </select>
                        <input type="hidden" name="id" value="' . $id . '">
                        <input type="hidden" name="order_no" value="' . $order_no . '">
                        <a class="calender__btn-update" href="/wp-admin/admin.php?page=view_admin&calendar__search='.$calendar_search.'&Search=search">Update</a>
                        </form>
                        ';
                        // HTML for status select dropdown

                        // HTML for tracking number details
                        $tracking_number_details = '
                        <form action="" method="post" style="flex-wrap:wrap; position:relative;" class="calendar__form-tracking">
                        <input type="text" style="width: 100%; margin-bottom: 5px; min-height: 22px; font-size: 12px;" name="tracking_number_details" value="'.$row['shipment_tracking'].'" placeholder="Add Tracking No.">
                        <input style="font-size: 12px;" class="calendar__tracking-number" type="submit" value="Submit" name="Submit">
                        <input type="hidden" name="id" value="' . $id . '">
                        <a style="margin-left: auto;" class="calender__btn-update" href="/wp-admin/admin.php?page=view_admin&calendar__search='.$search_term.'&Search=search">Update</a>
                        </form>
                        ';

                        $tracking_number_details_disable = '
                        <form action="" method="post" style="flex-wrap:wrap; position:relative;" class="calendar__form-tracking">
                        <input type="text" style="width: 100%; margin-bottom: 5px; min-height: 22px; font-size: 12px;" name="tracking_number_details" value="'.$row['shipment_tracking'].'" placeholder="Add Tracking No." disabled>
                        <input style="font-size: 12px;" class="calendar__tracking-number" type="submit" value="Submit" name="Submit" disabled>
                        <input type="hidden" name="id" value="' . $id . '">
                        </form>
                        ';
                         // HTML for tracking number details

                        // HTML for tracking url
                        $tracking_number_url = '
                        <form action="" method="post" style="flex-wrap:wrap; position:relative;" class="calendar__form-tracking-url">
                        <input type="text" style="width: 100%; margin-bottom: 5px; min-height: 22px; font-size: 12px;" name="tracking_number_url" value="'.$row['shipment_tracking_url'].'" placeholder="Add Tracking URL">
                        <input style="font-size: 12px;" class="calendar__tracking-url" type="submit" value="Submit" name="Submit">
                        <input type="hidden" name="id" value="' . $id . '">
                        <a style="margin-left: auto;" class="calender__btn-update" href="/wp-admin/admin.php?page=view_admin&calendar__search='.$search_term.'&Search=search">Update</a>
                        </form>
                        ';

                        $tracking_number_url_disable = '
                        <form action="" method="post" style="flex-wrap:wrap; position:relative;" class="calendar__form-tracking-url">
                        <input type="text" style="width: 100%; margin-bottom: 5px; min-height: 22px; font-size: 12px;" name="tracking_number_url" value="'.$row['shipment_tracking_url'].'" placeholder="Add Tracking URL" disabled>
                        <input style="font-size: 12px;" class="calendar__tracking-url" type="submit" value="Submit" name="Submit" disabled>
                        <input type="hidden" name="id" value="' . $id . '">
                        </form>
                        ';
                        // HTML for tracking url

                        if($status == "pending"){
                            $status_bg = 'style="background: #ff9a9a;"';
                        } elseif($status == "made"){
                            $status_bg = 'style="background: #ffc457;"';
                        } else {
                            $status_bg = 'style="background: #57e257;"';
                        }
                ?>
                        <tr <?php if($is_merged_disable === "1"){ echo 'class="tr-disabled"'; } ?>>
                            <td <?php echo $status_bg; ?>><?php echo $order_no; ?></td>
                            <td <?php echo $status_bg; ?>>
                                <?php 
                                    if (
                                        isset($date_seen_counter[$order_no]) &&
                                        isset($date_seen_counter[$order_no][$date]) &&
                                        $date_seen_counter[$order_no][$date] > 1
                                    ) {
                                        echo "YES";
                                    } else {
                                        echo "NO";
                                    }
                                ?>
                            </td>
                            <td <?php echo $status_bg; ?>><?php echo $title; ?> (<?php echo $schedule; ?>)</td>
                            <td <?php echo $status_bg; ?>><?php echo $date; ?></td>
                            <td <?php echo $status_bg; ?>>
                            <?php 
                                if($on_backorder == 1){
                                    echo "YES";
                                } else {
                                    echo "NO";
                                }
                            ?>
                            </td>
                            <td <?php echo $status_bg; ?>>
                            <?php if($status == "made" || $status == "dispatch"){ ?>
                                <?php //if($date_counts_by_order[$order_no][$date] == "1"){ ?>
                                    <a target="_blank" href="/pdf-generation/?id=<?php echo $id; ?>&order_no=<?php echo $order_no; ?>&date=<?php echo $date; ?>">Click Here</a>
                                <?php //} ?>
                            <?php } else {
                                echo "<p>Available once marked as made</p>";
                            } ?>
                            </td>
                            
                            <td class="calendar__btn-bg" <?php echo $status_bg; ?>>
                                
                                <?php if($status == "made" || $status == "dispatch"){ ?>
                                    <?php //if($date_counts_by_order[$order_no][$date] == "1"){ ?>
                                        <a target="_blank" href="/pdf-generation-invoice/?id=<?php echo $id; ?>&order_no=<?php echo $order_no; ?>&date=<?php echo $date; ?>&title=<?php echo $title; ?>">Click Here</a>
                                    <?php //} ?>
                                <?php } else {
                                    echo "<p>Available once marked as made</p>";
                                } ?>
                                <?php 
                                // if($status == "dispatch" && $last == 1){
                                //     echo '<a href="/send-customer-invoice/?order_id='.$order_no.'">Send customer final invoice</a>';
                                // } 
                                ?>
                               
                            </td>

                            <td class="calendar__btn-bg" <?php echo $status_bg; ?>>
                            <?php 
                            if($status == "dispatch"){ 
                                echo $status_select_disable; 
                            } else {
                                if($date_counts_by_order[$order_no][$date] > 1){
                                    echo $status_select_merged_date; 
                                } else {
                                    echo $status_select; 
                                }
                            }
                            

                            ?>
                        </td>
                        
                        
                        <td <?php echo $status_bg; ?>>

                            <?php if($date_counts_by_order[$order_no][$date] > 1){ ?>
                                <?php if($status != "dispatch"){ ?>
                                    <form action="" method="post" class="calendar__form-100">
                                        <input type="checkbox" value="1" class="calendar__select-shipments" <?php if($is_merged === "1"){ echo "checked"; } ?> disabled >
                                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                                <!-- WE ARE NOT ON DISPATCH -->
                                        <!-- <input type="hidden" name="is_merged" value="<?php //echo $is_merged; ?> "> -->
                                    </form>
                                <?php } else { ?>
                                    <form action="" method="post" class="calendar__form-100">
                                        <input type="checkbox" value="1" class="calendar__select-shipments" <?php if($is_merged === "1"){ echo "checked"; } ?>>
                                        <input type="hidden" name="id" value="<?php echo $id; ?> yyyyyy">
                                        <!-- <input type="hidden" name="is_merged" value="<?php //echo $is_merged; ?> "> -->
                                    </form>
                                <?php } ?>

                            <?php } ?>

                        </td>
                        <td class="calendar__btn-bg calender__column-flex" <?php echo $status_bg; ?>>
                        <?php $manage_shipment_link = '<a href="/wp-admin/admin.php?page=view_admin&calendar__search='.esc_html($search_term).'&date='.esc_html($date).'&Search=search">Manage Shipments</a>'; ?>
                            <?php if($date_counts_by_order[$order_no][$date] > 1){ ?>

                                <?php if($status === "dispatch"){ ?>
                                    <?php echo $manage_shipment_link; ?>
                                <?php } elseif($status === "dispatch") { ?> 
                                    <?php echo "<p>Available once marked as dispatch</p>"; ?>
                                <?php } else {
                                    echo "<p>Available once marked as dispatch</p>";
                                } ?>    
                                    
                            <?php } ?>    
                        </td>
                        <td class="calendar__btn-bg calender__column-flex" <?php echo $status_bg; ?>>
                            <?php 
                                if($status === "pending"){ 
                                    echo "<p>Available once marked as made</p>";
                                } 
                                elseif($status === "made"){
                                    echo $tracking_number_details; 
                                }
                                else {
                                    echo $tracking_number_details_disable; 
                                }
                            ?>
                        </td>
                        <td class="calendar__btn-bg calender__column-flex" <?php echo $status_bg; ?>>
                            <?php 
                                if($status === "pending"){ 
                                    echo "<p>Available once marked as made</p>";
                                } 
                                elseif($status === "made"){
                                    echo $tracking_number_url; 
                                }
                                else {
                                    echo $tracking_number_url_disable;
                                }
                            ?>
                        </td>
                        </tr>
                    <?php
                    } // end foreach
            } else {
                echo '<tr><td style="text-align:center;" colspan="12">No results found</td></tr>';
            }

        } catch (Exception $e) {
            // Handle any errors
            echo "Error: " . $e->getMessage();
        }
        ?>
            </tbody>
        </table>


        <?php
        echo '<div style="margin-top: 10px;" class="pagination">';

        if ($current_page > 1) {
            echo '<a style="border: 1px solid #777; padding: 3px 5px; border-radius: 4px; color: black; text-decoration: none;" href="?page=view_admin&calendar__search=' . urlencode($search_term) . '&page_num=1">First</a>';
            echo '<a style="border: 1px solid #777; padding: 3px 5px; border-radius: 4px; color: black; text-decoration: none;" href="?page=view_admin&calendar__search=' . urlencode($search_term) . '&page_num=' . ($current_page - 1) . '">Prev</a>';
        }

        if ($current_page < $total_pages) {
            echo '<a style="margin-right: 4px; border: 1px solid #777; padding: 3px 5px; border-radius: 4px; color: black; text-decoration: none;" href="?page=view_admin&calendar__search=' . urlencode($search_term) . '&page_num=' . ($current_page + 1) . '">Next</a>';
            echo '<a style="border: 1px solid #777; padding: 3px 5px; border-radius: 4px; color: black; text-decoration: none;" href="?page=view_admin&calendar__search=' . urlencode($search_term) . '&page_num=' . $total_pages . '">Last</a>';
        }

        echo '</div>';
        ?>


        </div>
        <div class="calendar__admin-col-right">
            <h1>Search</h1>
            <form class="calendar__search" method="get" action="">
                <input type="hidden" name="page" value="view_admin">
                <input id="searchInput" type="text" name="calendar__search" value="" placeholder="Enter Order Number" style="width: 90%; padding: 6px;">
                <input id="searchBtn" type="submit" name="Search" value="search" style="margin-top: 1rem;">
                <a style="border: 1px solid #777; padding: 3px 5px; border-radius: 4px; color: black; text-decoration: none;" href="/wp-admin/admin.php?page=view_admin">Reset</a>
            </form>
        </div>
        </div>

        <div class="calendar-admin-modal__outer">
            <div class="calendar-admin-modal__outer">
                    <?php 
                    
                    global $wpdb;

                    $order_id = $search_term;
                    $m_status = "dispatch";
                    $is_merged = "1";

                    

                    $table_name = $wpdb->prefix . 'split_schedule_orders';
                    //$query = $wpdb->prepare("SELECT * FROM $table_name WHERE order_no = %d", $order_id);
                    //$query = $wpdb->prepare("SELECT * FROM $table_name WHERE order_no = %d AND is_merged = %s AND date = %s", $order_id, $is_merged, $m_date);
                    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE order_no = %d AND is_merged = %s", $order_id, $is_merged);
                    $results = $wpdb->get_results($query);
                    echo '<div class="calendar">';
                    echo '<div class="calendar__admin-col-left">';
                    echo '<div class="custom-order-status">';
                    if($search_term){
                        echo '<h2>Order Info For Order No. '.esc_html($search_term).'</h2>';
                    }

                    if (!empty($results)) {
 

                        foreach ($results as $row) {
                            echo '<p style="margin: 0 0 7px 0;"><strong>Product:</strong> ' . esc_html($row->title) . '</p>';
                            echo '<p style="margin: 0 0 7px 0;"><strong>Order No:</strong> ' . esc_html($row->order_no) . '</p>';
                            echo '<p style="margin: 0 0 7px 0;"><strong>Date:</strong> ' . esc_html($row->date) . '</p>';
                            echo '<p style="margin: 0 0 7px; 0"><strong>Status:</strong> ' . esc_html($row->status) . '</p>';
                            echo '<p style="margin: 0 0 15px; 0"><strong>Is Merged?:</strong> ' . esc_html($row->is_merged) . '</p>';
                        }
                    
                        // Conditionally render dispatch complete box
                        if ($results) {

                            echo '<div style="position: relative; margin-top: 20px; padding: 15px; background: #e0ffe0; border: 1px solid #00aa00; width: 95%;">';
                            echo '
                                <form action="" method="post" class="calendar__form-100" style="display: inline-block; width: 100%;">
                                <span>All merged shipments are now complete.</span>
                                <a href="/wp-admin/admin.php?page=view_admin&calendar__search='.$row->order_no.'&date='.$row->date.'&Search=search" style="float: right; border: 1px solid #777; border-radius: 0.2rem; background: #eee; padding: 0.07rem 0.3rem; margin-left: 0.5rem; color: black;">Update</a>
                                <button style="float:right; margin: 0 0 0 12px;" class="calendar__select-shipments-send">Send Customer Invoice & Dispatch Note</button>

                                <a target="_blank" style="float:right; margin: 0 42px;" href="/pdf-generation-invoice/?id='.$row->id.'&order_no='.$row->order_no.'&date='.$row->date.'&is_merged=' .$row->is_merged. '&title='.$row->title.'">Preview Invoice</a>

                                <a target="_blank" style="float:right; margin: 0 42px;" href="/pdf-generation/?id='.$row->id.'&order_no='.$row->order_no.'&date='.$row->date.'&is_merged='.$row->is_merged.'&title='.$row->title.'">Preview Despatch Note</a>
                                <input type="hidden" name="id" value="'.$row->id.'">
                                <input type="hidden" name="status" value="'.$row->status.'">
                                <input type="hidden" name="order_no" value="'.$row->order_no.'">
                                <input type="hidden" name="date" value="'.$row->date.'">
                                <input type="hidden" name="is_merged" value="'.$row->is_merged.'">
                                </form>
                                ';
                            echo '</div>';
                        }
                        // Conditionally display sent email message
                        echo '<p id="customer-invoice-results" style="opacity: 0; position: relative; margin-top: 20px; padding: 15px; background: #e0ffe0; border: 1px solid #00aa00; width: 95%;">Customer email with delivery note and invoice PDF sent successfully</p>';



                    } else {
                        echo '<p>No results found for Order No ' . esc_html($order_id) . '.</p>';
                    }

                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    ?>
            </div>
        </div>
<?php
}