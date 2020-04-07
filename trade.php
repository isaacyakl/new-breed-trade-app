<?php

/* ***************************** */
/*   Result page & validation    */
/*      vars and funcs           */
/* ***************************** */

// Variable to track if all input is valid
$isFormInputValid = true;

// Form result message variable
$resultBody = "";

// Function to display form results and exit script
function displayResultPage() {
    // Import global variables
    global $isFormInputValid;
    global $resultBody;
    global $email;

    // Result header
    $resultHeader = "";

    // If form input was invalid
    if($isFormInputValid === false) {
        // Add the failure header
        $resultHeader = <<<RHF
            <p class="text-danger text-center">
                <strong>Submission Failed</strong>
            </p>
            <p>
                Please correct the below and try again.
            </p>
RHF;
    }
    // Form input was valid
    else {
        // Add the success header
        $resultHeader = <<<RHS
            <p class="text-success text-center">
                <strong>Submitted Successfully</strong>
            </p>
RHS;
        // Add thank you and "Return to Store" button
        $resultBody .= <<<RTSBTN
            <p>
                Thank you for submitting a trade offer! A copy of it was sent to {$email}. If you would like to add more detail or forgot to add something, please reply to the email receipt you were sent.
            </p>
            <p>
                Please allow 48 business hours for us to get back with you. Do not submit multiple trade offers for the same items as this will only slow our response time to offers.
			</p>
            <p class="text-center">
                <a href="https://newbreedpb.com" class="btn btn-primary" role="button">Return to Store</a>
            </p>
RTSBTN;
    }

    // Object to return
    $response->isFormInputValid = $isFormInputValid; // <bool> Whether the form was valid or not
    $response->header = $resultHeader; // <string> Header for the result
    $response->body = $resultBody; // <string> Body of the result

    // Return json version of the $response object
    echo json_encode($response, JSON_HEX_QUOT | JSON_HEX_TAG);
    
    // End script
    exit;
}

// Function used for adding a invalid alert to the results page
function addInvalidAlert($alertMsg) {
    // Import global variable
    global $resultBody;

    // Add alert
    $resultBody .= <<<AMSG
        <div class="alert alert-danger" role="alert">
            $alertMsg
        </div>
AMSG;
}

// Function used for retrieving a specific variable's integer value from the script.js file
function retrieveScriptjsVar($varName) {
    preg_match("/(var|const|let)[ ]+{$varName}[ ]+=[ ]+\d+/",file_get_contents("script.js"),$varLine); // Search for variable line by name provided
    $varVal = filter_var($varLine[0], FILTER_SANITIZE_NUMBER_INT); // Pull variable integer value out
    return $varVal; // Return integer value
}



/* ***************************** */
/* Store and sanitize form input */
/* ***************************** */

// Function for most common sanitization task 
function sanitizeInput($usrInput)
{
    // Remove html tags and characters over ASCII code 127
    return filter_var($usrInput, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
}

// Sanitize personal info
$fName = sanitizeInput($_POST['fName']);
$lName = sanitizeInput($_POST['lName']);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$phone = filter_var($_POST['phone'], FILTER_SANITIZE_NUMBER_INT); // Remove all but digits, plus signs, and minus signs
$terms = sanitizeInput($_POST['terms']); // checkbox (on/off) sanitize to be extra safe

// Sanitize item(s)
// Validate num of items
$numOfItems = filter_var($_POST['numTradeItems'], FILTER_VALIDATE_INT, array("options"=>array("min_range"=>retrieveScriptjsVar("minTradeItems"), "max_range"=>retrieveScriptjsVar("maxTradeItems")))); // Hidden input added on form submission. Validate it is in the desired item range.
// If number of items is false (not valid)
if($numOfItems === false) { // Do not sanitize
    addInvalidAlert("Invalid amount of items!"); // Alert
    $isFormInputValid = false; // Set form to invalid
}
else { // Number of items is within the valid range
    // Store and/or sanitize all items
    for($i = 1 ; $i < $numOfItems+1 ; $i++) {
        $makeModel[$i] = sanitizeInput($_POST["makeModel{$i}"]);
        $qty[$i] = filter_var($_POST["qty{$i}"],FILTER_SANITIZE_NUMBER_INT);
        $condition[$i] = sanitizeInput($_POST["condition{$i}"]);
        $upgradesMods[$i] = sanitizeInput($_POST["upgradesMods{$i}"]);
        $accessories[$i] = sanitizeInput($_POST["accessories{$i}"]);
        $pictures[$i] = $_FILES["compressedPictures{$i}"];
        $video[$i] = $_POST["video{$i}"];
    }
}

// Sanitize trade options
$sellOrTrade = sanitizeInput($_POST['sellOrTrade']); // dropdown menu (trade/cash) sanitize to be extra safe
$tradeTowardsWhat = sanitizeInput($_POST['tradeTowardsWhat']);
$commentsNotes = sanitizeInput($_POST['commentsNotes']);



/* ***************************** */
/*      Validate form input      */
/* ***************************** */

// If first name is missing
if($fName == "") {
    addInvalidAlert("First name missing!");
    $isFormInputValid = false; // Set form to invalid
}

// If last name is missing
if($lName == "") {
    addInvalidAlert("Last name missing!");
    $isFormInputValid = false; // Set form to invalid
}

// If email is invalid or missing
if(filter_var($email, FILTER_VALIDATE_EMAIL) === false || $email == "") {
    addInvalidAlert("Invalid or missing email address!"); // Add alert
    $isFormInputValid = false; // Set form to invalid
}

// If phone number is missing
If($phone == "") {
    addInvalidAlert("Phone number missing!");
    $isFormInputValid = false; // Set form to invalid
}

// If terms were not accepted
if($terms != "on") {
    addInvalidAlert("Terms not accepted!"); // Add alert
    $isFormInputValid = false; // Set form to invalid
}

// Item(s)
// If number of items is within the valid range
if($numOfItems !== false) {
    // Validate all items
    for($i = 1 ; $i < $numOfItems+1 ; $i++) {
        // If make and model text length is under 8 characters
        if(strlen($makeModel[$i]) < 8) {
            addInvalidAlert("Make & model description \"{$makeModel[$i]}\" is too short!"); // Add alert
            $isFormInputValid = false; // Set form to invalid
        }

        // If quantity is less than 1
        if($qty[$i] < 1) {
            addInvalidAlert("Quantity cannot be less than 1 for item \"{$makeModel[$i]}\"!"); // Add alert
            $isFormInputValid = false; // Set form to invalid
        }

        // If condition text length is under 4 characters
        if(strlen($condition[$i]) < 4) {
        $condition[$i] = sanitizeInput($_POST["condition{$i}"]);
            addInvalidAlert("Condition description is too short for item \"{$makeModel[$i]}\"!"); // Add alert
            $isFormInputValid = false; // Set form to invalid
        }
        
        // Verify the pictures are of the acceptable types (JPEG or PNG)
        for($p = 0 ; $p < count($pictures[$i]['name']) ; $p++) { // Move through each picture
            // Grab the first byte of the file to verify its type
            $typeCheck = exif_imagetype($pictures[$i]['tmp_name'][$p]);

            // If it is not a valid image type
            if ($typeCheck != IMAGETYPE_JPEG && $typeCheck != IMAGETYPE_PNG) {
                addInvalidAlert("{$pictures[$i]['tmp_name'][$p]} is not a valid image!");
                $isFormInputValid = false; // Set form to invalid
            }
        }
       
        // If video link is not empty and not a valid URL
        if($video[$i] != "" && filter_var($video[$i],FILTER_VALIDATE_URL) === false) {
            addInvalidAlert("Video URL \"{$video[$i]}\" is not a valid URL for item \"{$makeModel[$i]}\"!");
            $isFormInputValid = false; // Set form to invalid
        }
    }
}

// If sell or trade was not selected
if($sellOrTrade != "sell" && $sellOrTrade != "trade") {
    addInvalidAlert("Specify whether you want to sell or trade your items!");
    $isFormInputValid = false;
}

// If any of the input was invalid
if($isFormInputValid === false) {
    displayResultPage(); // Display the results page with error messages and exit script early
}


/* ***************************** */
/*          Debug print          */
/* ***************************** */
if($_POST['debug']=="true" || $_GET['debug']=="true")
{
    $resultBody .= "<pre>";

    $resultBody .= print_r($fName,true);
    $resultBody .= print_r($lName,true);
    $resultBody .= print_r($email,true);
    $resultBody .= print_r($phone,true);
    $resultBody .= print_r($terms,true);
    
    $resultBody .= print_r($makeModel,true);
    $resultBody .= print_r($qty,true);
    $resultBody .= print_r($condition,true);
    $resultBody .= print_r($upgradesMods,true);
    $resultBody .= print_r($accessories,true);
    $resultBody .= print_r($pictures,true);
    $resultBody .= print_r($video,true);
    
    $resultBody .= print_r($sellOrTrade,true);
    $resultBody .= print_r($tradeTowardsWhat,true);
    $resultBody .= print_r($commentsNotes,true);

    $resultBody .= "</pre>";
}



/* ***************************** */
/*         Compose Email         */
/* ***************************** */
$to = "newbreednj@gmail.com";
$subject = "";
$message = "";

$headers = "From: tradeform@newbreedpb.com" . "\r\n" .
"Reply-To: {$email}" . "\r\n" .
"Mime-Version: 1.0" . "\r\n" .
"Content-type:text/html;charset=UTF-8" . "\r\n";


/* ***************************** */
/*           Send Email          */
/* ***************************** */

// Send copy trade processor
// Send copy to trader


/* ***************************** */
/*      Display results page     */
/* ***************************** */
displayResultPage();
?>