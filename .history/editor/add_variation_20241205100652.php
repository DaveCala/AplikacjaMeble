
   <?php
   // ... your existing PHP code to handle the form data ...

   // Example successful response:
   if ($success) { // Replace with your success condition
       $response = array('success' => true, 'message' => 'Variation added successfully!');
   } else {
       // Provide a more specific error message!
       $response = array('success' => false, 'message' => 'Error adding variation: ' . $error_message); // Include error details
   }

   header('Content-Type: application/json'); // Crucial: Set the content type to JSON
   echo json_encode($response); // Encode the PHP array as JSON and send it
   ?>