php
<?php
header('Content-Type: application/json'); // Crucial: Set the content type

// ... your existing PHP code to process the form data ...

if (/* Variation added successfully */) {
  $response = array(
    'success' => true,
    'message' => 'Wariacja została dodana pomyślnie!'
  );
} else {
  //  Handle errors gracefully and provide a specific message
  $error_message = "Błąd podczas dodawania wariacji. ";
  if (/* specific error condition 1 */) {
    $error_message .= "Przyczyna 1.";
  } elseif (/* specific error condition 2 */) {
    $error_message .= "Przyczyna 2.";
  } else {
      $error_message .= "Nieznany błąd."; // Generic error
  }


  $response = array(
    'success' => false,
    'message' => $error_message
  );
}

echo json_encode($response); // Send JSON response
?>