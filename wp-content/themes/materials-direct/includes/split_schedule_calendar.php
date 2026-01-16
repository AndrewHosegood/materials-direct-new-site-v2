<?php
function register_calendar() {
  add_menu_page('custom menu title', 'Calendar', 'manage_woocommerce', 'custompage', '_calendar', null, 6); 
  add_submenu_page('custompage', 'View Admin', 'View Admin', 'manage_woocommerce', 'view_admin', 'view_admin_content');
}
add_action('admin_menu', 'register_calendar');

function _calendar(){ 
  
    // Enqueue JavaScript
    wp_enqueue_script('calendar-js', '/wp-content/themes/materials-direct/js/calendar-script.js', array('jquery'), '1.0', true);

    wp_enqueue_style('calendar-css', get_stylesheet_directory_uri() . '/css/calendar.css', array(), '1.0', 'all');


    global $wpdb;

    // Your table name
    $table_name = $wpdb->prefix . 'split_schedule_orders';
    $domain = $_SERVER['HTTP_HOST'];

    // Your SQL query
    $sql = "SELECT * FROM $table_name";

    try {
        // Execute the query
        $results = $wpdb->get_results($sql, ARRAY_A);
        // echo "<pre>";
        // print_r($results);
        // echo "</pre>";

        // Initialize an array to hold events
        $jsonEvents = '';

        // Process each row and build JSON data
        foreach ($results as $row) {
          $id = esc_js($row['id']);
          $title = esc_js($row['title']);
          $firstname = esc_js($row['firstname']);
          $lastname = esc_js($row['lastname']);
          $company = esc_js($row['company']);
          $date = esc_js($row['date']);
          $order_no = esc_js($row['order_no']);
          $notes = esc_js($row['notes']);
          $part_shape = esc_js($row['part_shape']);
          $part_shape_link = esc_js($row['pdf_part_shape_link']);
          $dxf_part_shape_link = esc_js($row['dxf_part_shape_link']);
          $width = esc_js($row['width']);
          $length = esc_js($row['length']);
          $radius = esc_js($row['radius']);
          $dimension_type = esc_js($row['dimension_type']);
          $qty = esc_js($row['qty']);
          $status = esc_js($row['status']);
          $schedule = esc_js($row['schedule']);
          $schedule_qty = esc_js($row['schedule_qty']);
          $pdf = esc_js($row['pdf']);
          $dxf = esc_js($row['dxf']);

          $my_title = "#" . $order_no . "-" . $title;

            $jsonEvents .= "{id: '$id', title: '$my_title', start: '$date', extendedProps: {custom: '$order_no', custom_title: '$title', part_shape: '$part_shape', part_shape_link: '$part_shape_link', dxf_part_shape_link: '$dxf_part_shape_link', width: '$width', length: '$length', radius: '$radius', dimension_type: '$dimension_type', qty: '$qty', status: '$status', schedule: '$schedule', schedule_qty: '$schedule_qty', pdf: '$pdf', dxf: '$dxf', firstname: '$firstname', lastname: '$lastname', company: '$company', date: '$date' }},";
    
        }

        $jsonEvents = rtrim($jsonEvents, ',');
        $jsonEvents .= '';
        //print_r($jsonEvents);

    } catch (Exception $e) {
        // Handle any errors
        echo "Error: " . $e->getMessage();
    }

    ?>

    <script>

      function formatDate(date) {
          const year = date.getFullYear();
          const month = String(date.getMonth() + 1).padStart(2, '0');
          const day = String(date.getDate()).padStart(2, '0');
          
          return `${year}-${month}-${day}`;
      }

      const currentDate = new Date();
      const formattedDate = formatDate(currentDate);

      document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
          headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth'
            //right: 'dayGridMonth,timeGridWeek,timeGridDay'
          },
          //initialDate: '2024-04-01',
          initialDate: formattedDate,
          navLinks: true, // can click day/week names to navigate views
          selectable: true,
          selectMirror: true,
          editable: true,
          dayMaxEvents: true, // allow "more" link when too many events
          //eventColor: 'red',
          // events: [
          //   {
          //     id: '1', 
          //     title: 'Universal Science T-Pad 1500 - A0 - 0.23mm', 
          //     start: '2024-03-28',
          //     extendedProps: {
          //         custom: '38587', 
          //         notes: '50 parts to be dispatched in/on 28/03/2024 7 Days (working days) (2.5% Discount) 25 parts to be dispatched in/on 30/04/2024 35 Days (working days) (5% Discount) 25 parts to be dispatched in/on 30/06/2024 35 Days (working days) (5% Discount)', 
          //         part_shape: 'Square / Rectangle', 
          //         part_shape_link: '', 
          //         width: '10', 
          //         length: '10', 
          //         dimension_type: 'mm', 
          //         qty: '100', 
          //         status: 'pending' 
          //         }
          //   }
          // ],
          //events: [
            // {
            //     title: 'My Title',
            //     start: '2024-03-01',
            //     extendedProps: {
            //       custom: 'Custom Data'
            //     }
            // },
            // {
            //     title: 'My Title 2',
            //     start: '2024-03-09',
            //     extendedProps: {
            //       custom: 'Custom Data'
            //     }
            // },
            // {
            //     title: 'My Title 3',
            //     start: '2024-03-15',
            //     extendedProps: {
            //       custom: 'Custom Data'
            //     }
            // }
          //],
          events: [
            <?php echo $jsonEvents; ?>
          ],
          eventDidMount: function(info) {
            info.el.setAttribute('data-id', info.event.id);
            info.el.classList.add(info.event.extendedProps.status);
            console.log("Title " + info.event.title);
            console.log("Custom Title " + info.event.extendedProps.custom_title);
            console.log("Firstname " + info.event.extendedProps.firstname);
            console.log("Lastname " + info.event.extendedProps.lastname);
            console.log("Company " + info.event.extendedProps.company);
            console.log("Custom Data: " + info.event.extendedProps.custom);
            //console.log("Notes: " + info.event.extendedProps.notes);
            console.log("Part Shape: " + info.event.extendedProps.part_shape);
            console.log("Width: " + info.event.extendedProps.width);
            console.log("Length: " + info.event.extendedProps.length);
            console.log("Radius: " + info.event.extendedProps.radius);
            console.log("Dimension Type: " + info.event.extendedProps.dimension_type);
            console.log("Qty: " + info.event.extendedProps.qty);
            console.log("Status: " + info.event.extendedProps.status);
            console.log("Schedule: " + info.event.extendedProps.schedule);
            console.log("Schedule_qty: " + info.event.extendedProps.schedule_qty);
            console.log("PDF: " + info.event.extendedProps.pdf);
            console.log("DXF: " + info.event.extendedProps.dxf);
            console.log("Part Shape Link: " + info.event.extendedProps.part_shape_link);
            console.log("DXF Part Shape Link: " + info.event.extendedProps.dxf_part_shape_link);
            console.log("Date: " + info.event.extendedProps.date);
          },
          eventClick: function(info) {
              const modalTitle = document.getElementById('modalTitle');
              const eventTitle = info.event.title;
              const eventCustomTitle = info.event.extendedProps.custom_title
              modalTitle.textContent = info.event.title;
              if (eventTitle.length > 53) {
                  modalTitle.textContent = eventCustomTitle.substring(0, 53) + "...";
              } else {
                  modalTitle.textContent = eventCustomTitle;
              }
              //const modalId = document.getElementById('modalID');
              //modalId.textContent = info.event.id;
              const modalOrderNo = document.getElementById('modalOrderNo');
              const linkOrderNo = document.getElementById('linkOrderNo');
              modalOrderNo.textContent = info.event.extendedProps.custom;
              linkOrderNo.href = "https://<?php echo $domain; ?>/wp-admin/post.php?post=" + info.event.extendedProps.custom + "&action=edit";

              // const linkNotes = document.getElementById('linkNotes');
              // linkNotes.textContent = info.event.extendedProps.notes;

              const partShape = document.getElementById('partShape');
              partShape.textContent = info.event.extendedProps.part_shape;

              const dxfShapeLink = document.getElementById('dxfShapeLink');
              dxfShapeLink.innerHTML = info.event.extendedProps.dxf;
              dxfShapeLink.href = info.event.extendedProps.dxf_part_shape_link;

              const partShapeLink = document.getElementById('partShapeLink');
              partShapeLink.innerHTML = info.event.extendedProps.pdf;
              partShapeLink.href = info.event.extendedProps.part_shape_link;

              const modalPartsHeading = document.querySelector('.modal__parts-heading');

              if (info.event.extendedProps.pdf === '') {
                  modalPartsHeading.style.display = 'none';
              } else {
                  modalPartsHeading.style.display = 'inline';
              }

              const partWidth = document.getElementById('partWidth');
              partWidth.innerHTML = info.event.extendedProps.width;

              const partLength = document.getElementById('partLength');
              partLength.innerHTML = info.event.extendedProps.length;

              const partRadius = document.getElementById('partRadius');
              partRadius.innerHTML = info.event.extendedProps.radius;

              const partTotal = document.getElementById('partTotal');
              partTotal.innerHTML = info.event.extendedProps.qty;

              const partStatus = document.getElementById('partStatus');
              partStatus.innerHTML = info.event.extendedProps.status;          

              const batchStatus = document.getElementById('batchStatus');
              batchStatus.textContent = info.event.extendedProps.schedule; 

              const schedule_qty = document.getElementById('schedule_qty');
              schedule_qty.textContent = info.event.extendedProps.schedule_qty; 

              // Format the date so its readable
              function formatDate(dateString) {
                const date = new Date(dateString);
                const options = { day: 'numeric', month: 'long', year: 'numeric' };
                const formattedDate = date.toLocaleDateString('en-GB', options);
                return formattedDate;
              }
              // Format the date so its readable

              const originalDate = info.event.extendedProps.date;
              const formattedDate = formatDate(originalDate);
              //console.log(formattedDate); 

              const schedule_date = document.getElementById('schedule_date');
              schedule_date.textContent = formattedDate; 

              const schedule_date_2 = document.getElementById('schedule_date_2');
              schedule_date_2.textContent = formattedDate; 


              const modal = document.getElementById('modal');
              modal.style.display = 'block';

              const modal__inner = document.getElementById('modal__inner');
              modal__inner.style.display = 'block';

              const userFirstname = document.getElementById('userFirstname');
              userFirstname.textContent = info.event.extendedProps.firstname; 

              const userLastname = document.getElementById('userLastname');
              userLastname.textContent = info.event.extendedProps.lastname;

              const userCompany = document.getElementById('userCompany');
              userCompany.textContent = info.event.extendedProps.company; 

              const comapanyHyphen = document.querySelector('.modal__company-hyphen');

              if (info.event.extendedProps.company === '') {
                  comapanyHyphen.style.display = 'none';
              } else {
                  comapanyHyphen.style.display = 'inline';
              }

              //modal__inner.style.background = info.event.extendedProps.status;

              // Clear existing classes
              modal__inner.classList.remove('pending', 'made', 'complete');

              modal__inner.classList.add(info.event.extendedProps.status);

              const my_status = document.getElementById('status');
              my_status.textContent = info.event.extendedProps.status; 
              



              
          }
        });
        calendar.render();

        const modal = document.getElementById('modal');
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
              const myElement = document.getElementById("modal__inner");
              myElement.classList.remove("made");
              myElement.classList.remove("pending");
              myElement.classList.remove("dispatch");
              modal.style.display = 'none';
            }
        });
        
        const modalClose = document.getElementById('mc');
        modalClose.addEventListener('click', function(event) {
            if (event.target === modalClose) {
              const myElement = document.getElementById("modal__inner");
              myElement.classList.remove("made");
              myElement.classList.remove("pending");
              myElement.classList.remove("dispatch");
              modal.style.display = 'none';
            }
        });



      });
    </script>
    <div style="margin: 5px 15px 2px;">
    <div id='calendar'></div>

    <!-- MODAL -->
    <div class="modal" id="modal" data-id="<?php //echo $id; ?>">
              <div class="modal__inner" id="modal__inner">

                  <div class="modal__header">
                    <p>#<span id="modalOrderNo">Order No.</span></p>
                    <p id="modalTitle">Event Title</p>
                    <span class="modal__close-container"><img id="mc" class="modal__close" src="/wp-content/uploads/2024/03/close.svg"></span>
                  </div>
                  
                  <div class="modal__content">
                  <!-- <p>Order ID: <span id="modalID">Event ID</span></p> -->
                  <div class="modal__user">
                    <img alt="User Details" src="/wp-content/uploads/2024/03/user-alt-solid.svg" class="modal__user-icon"><span class="modal__name" id="userFirstname"></span><span class="modal__name" id="userLastname"></span><span class="modal__company-hyphen">-</span><span class="modal__name" id="userCompany"></span>
                  </div>
          
                  <p class=""><strong>Part Shape:</strong> <span id="partShape">Part Shape</span></p>
                  <p class=""><strong class="modal__parts-heading">PDF Upload:</strong> <a target="_blank" id="partShapeLink">PDF Shape Link</a></p>
                  <p class=""><strong class="modal__parts-heading">DXF Upload:</strong> <a target="_blank" id="dxfShapeLink">DXF Shape Link</a></p>
                  <p class=""><span><strong>Width:</strong> <span id="partWidth">Width</span><small class="modal__part-mm">mm</small></span></p>
                  <p class=""><span><strong>Length:</strong> <span id="partLength">Length</span><small class="modal__part-mm">mm</small></span></p>
                  <p class=""><span><strong>Radius:</strong> <span id="partRadius">Radius</span><small class="modal__part-mm">mm</small></span></p>
                  <p> <strong>Total number of parts:</strong> <span id="partTotal">Total number of parts</span></p>
                  <br>
                  <p><strong>Scheduled Deliveries (<span id="batchStatus">Batch Status</span>):</strong></p>
                  <p><strong>Quantity: </strong><span id="schedule_qty">Part Quantity</span></p>
                  <p><strong>Depatch Date: </strong><span id="schedule_date_2">[scheduled-date]</span></p>
                  <p><strong>Delivery Status:</strong> <span id="schedule_qty">Delivery Status</span> parts to be delivered on <span id="schedule_date">[scheduled-date]</span></p>
                  <p><strong>Delivery Status:</strong> <span id="status">Status</span></p>
                  

                  <!-- <p class="">
                    <span class="modal__part-dimensions">
                      
                      <span>
                      <strong>Width:</strong> <span id="partWidth">Width</span>
                      <small class="modal__part-mm">mm</small>
                      </span>
                      
                      <span>
                      <strong>Length:</strong> <span id="partLength">Length</span>
                      <small class="modal__part-mm">mm</small>
                      </span>

                      </span>

                  </p> -->
                  <!-- <p class="modal__notes"><span id="linkNotes">Notes</span></p> -->

                  

                  

                  </div>

                  <div class="modal__footer">
                    
                    <a target="_blank" class="modal__btn" id="linkOrderNo" href="">View Order</a>

                    <di class="modal__status"><strong>Status: </strong> <span id="partStatus">Status</span></div>

                  </div>

              </div>
    </div>  
    <!-- MODAL -->


    </div>
    <?php

}