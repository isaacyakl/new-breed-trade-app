<?php

/* ***************************** */
/*   Result page & validation    */
/*      vars and funcs           */
/* ***************************** */

// Variable to track if all input is valid
$isFormInputValid = true;

// Form result message variable
$resultBody = "";

// Debug info variable
$debugInfo = "";

// Function to display form results and exit script
function displayResultPage() {
    // Import global variables
    global $isFormInputValid;
    global $resultBody;
    global $debugInfo;
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
                Thank you for submitting an offer! A copy of it was sent to {$email}. If you would like to add more detail or forgot to add something, simply reply to the email receipt you were sent.
            </p>
            <p>
                Please allow 48 business hours for us to get back with you. Do not submit multiple offers for the same items as this will only slow our response time.
			</p>
            <p class="text-center">
                <a href="https://newbreedpb.com" class="btn btn-primary" role="button">Return to Store</a>
            </p>
RTSBTN;
    }

    // Object to return
    $response = new stdClass();
    $response->isFormInputValid = $isFormInputValid; // <bool> Whether the form was valid or not
    $response->header = $resultHeader; // <string> Header for the result
    $response->body = $resultBody; // <string> Body of the result
    $response->debugInfo = $debugInfo; // <string> Debug information

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

        // If there are not enough pictures provided
        if(count($pictures[$i]) < retrieveScriptjsVar("minPicturesPerItem")) {
            addInvalidAlert("Not enough pictures provided for {$makeModel[$i]}!");
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
/*   Generate unique offer #     */
/* ***************************** */

$offerId = "";
$offerId .= ($sellOrTrade === "trade" ? "T" : "S"); // Add letter indicating offer type
$offerId .= strtoupper(bin2hex(random_bytes(6))); // Add unique string identifier

/* ***************************** */
/*          Debug print          */
/* ***************************** */
if($_POST['debug']=="true")
{
    $debugInfo .= print_r($fName,true);
    $debugInfo .= print_r($lName,true);
    $debugInfo .= print_r($email,true);
    $debugInfo .= print_r($phone,true);
    $debugInfo .= print_r($terms,true);
    
    $debugInfo .= print_r($makeModel,true);
    $debugInfo .= print_r($qty,true);
    $debugInfo .= print_r($condition,true);
    $debugInfo .= print_r($upgradesMods,true);
    $debugInfo .= print_r($accessories,true);
    $debugInfo .= print_r($pictures,true);
    $debugInfo .= print_r($video,true);
    
    $debugInfo .= print_r($sellOrTrade,true);
    $debugInfo .= print_r($tradeTowardsWhat,true);
    $debugInfo .= print_r($commentsNotes,true);

    $debugInfo .= print_r($offerId, true);
}

/* ***************************** */
/*         Compose Email         */
/* ***************************** */
date_default_timezone_set("America/New_York");
$timeReceived = date("F j, Y, g:i A T",$_SERVER['REQUEST_TIME_FLOAT']);

$tradeProcessorEmail = "trades@newbreedpb.com"; // Email to send trades offers to

$offerBody = <<<OFBDY
<table border="0" cellpadding="0" cellspacing="6" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; box-sizing: border-box; padding: 0px;">
    <tbody>
    <!-- Personal Info -->
    <tr>
        <td style="font-family: sans-serif; font-size: 20px; vertical-align: top; text-align: left; padding: 6px 0px; font-weight: bold;" colspan="2">
            Personal Information
        </td>
    </tr>
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; max-width: 50%; width: 50%; text-align: left; padding: 6px 0px; font-weight: bold;">
            First name
        </td>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; max-width: 50%; width: 50%; text-align: left; padding: 6px 0px; font-weight: bold;">
            Last name
        </td>
    </tr>
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; max-width: 50%; width: 50%; border: 1px solid #ced4da; border-radius: 3px; text-align: left; padding: 6px 12px;">
            {$fName}
        </td>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; max-width: 50%; width: 50%; border: 1px solid #ced4da; border-radius: 3px; text-align: left; padding: 6px 12px;">
            {$lName}
        </td>
    </tr>
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; max-width: 50%; width: 50%; text-align: left; padding: 6px 0px; font-weight: bold;">
            Email address
        </td>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; max-width: 50%; width: 50%; text-align: left; padding: 6px 0px; font-weight: bold;">
            Phone number
        </td>
    </tr>
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; max-width: 50%; width: 50%; border: 1px solid #ced4da; border-radius: 3px; text-align: left; padding: 6px 12px;">
            <a href="mailto:{$email}" title="Email {$fName}">{$email}</a>
        </td>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; max-width: 50%; width: 50%; border: 1px solid #ced4da; border-radius: 3px; text-align: left; padding: 6px 12px;">
          <a href="tel:+1{$phone}" title="Call {$fName}">{$phone}</a>
        </td>
    </tr>
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; text-align: left; padding-top: 6px" colspan="2">
            
        </td>
    </tr>
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: rgba(0, 60, 255, 0.1); border: blue solid 1px; border-radius: 3px; text-align: left; padding: 6px 12px;" colspan="2">
            <span style="font-family: sans-serif; font-size: 14px; display: block; padding: 6px 0px; font-weight: bold;">
                Trade Terms
            </span>
            <span style="font-family: sans-serif; font-size: 14px; display: block; padding: 6px 0px;">
                <input type="checkbox" name="terms" checked disabled> I have read and agree to the Trade Terms
            </span>
        </td>
    </tr>
OFBDY;

$offerBody .= <<<OFBDYIS
    <!-- Item(s) -->
    <tr>
        <td style="font-family: sans-serif; font-size: 20px; vertical-align: top; text-align: left; padding-bottom: 6px; font-weight: bold;" colspan="2">
            Item(s)
        </td>
    </tr>
OFBDYIS;

for($i = 1 ; $i < $numOfItems+1 ; $i++) {
$offerBody .= <<<OFBDYINBEG
    <tr>
        <td style="font-family: sans-serif; vertical-align: top; text-align: left;" colspan="2">
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 3px; padding: 6px;">
    <tbody>
    <!-- Item(s) -->
    <tr>
        <td style="font-family: sans-serif; font-size: 18px; vertical-align: top; text-align: left; padding: 6px 0px; font-weight: bold;">
            Item {$i}
        </td>
    </tr>
OFBDYINBEG;
$offerBody .= <<<OFBDYMM
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; text-align: left; padding: 6px 0px; font-weight: bold;">
            Make, Model, and Color
        </td>
    </tr>
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; border: 1px solid #ced4da; border-radius: 3px; text-align: left; padding: 6px 12px;" colspan="2">
            {$makeModel[$i]}
        </td>
    </tr>
OFBDYMM;

$offerBody .= <<<OFBDYQTY
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; text-align: left; padding: 6px 0px; font-weight: bold;">
            Quantity
        </td>
    </tr>
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; border: 1px solid #ced4da; border-radius: 3px; text-align: left; padding: 6px 12px;" colspan="2">
            {$qty[$i]}
        </td>
    </tr>
OFBDYQTY;

$offerBody .= <<<OFBDYCDTN
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; text-align: left; padding: 6px 0px; font-weight: bold;">
            Condition
        </td>
    </tr>
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; border: 1px solid #ced4da; border-radius: 3px; text-align: left; padding: 6px 12px;" colspan="2">
            {$condition[$i]}
        </td>
    </tr>
OFBDYCDTN;

$offerBody .= <<<OFBDYUPGRDMOD
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; text-align: left; padding: 6px 0px; font-weight: bold;">
            Upgrades / Modifications
        </td>
    </tr>
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; border: 1px solid #ced4da; border-radius: 3px; text-align: left; padding: 6px 12px;" colspan="2">
OFBDYUPGRDMOD;
$offerBody .= ($upgradesMods[$i] != "" ? $upgradesMods[$i] : "&nbsp;");
$offerBody .= <<<OFBDYUPGRDMODCNTD
        </td>
    </tr>
OFBDYUPGRDMODCNTD;
$offerBody .= <<<OFBDYACCSSRS
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; text-align: left; padding: 6px 0px; font-weight: bold;" colspan="2">
            Accessories
        </td>
    </tr>
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; border: 1px solid #ced4da; border-radius: 3px; text-align: left; padding: 6px 12px;" colspan="2">
OFBDYACCSSRS;
$offerBody .= ($accessories[$i] != "" ? $accessories[$i] : "&nbsp;");
$offerBody .= <<<OFBDYACCSSRSCNTD
        </td>
    </tr>
OFBDYACCSSRSCNTD;

$offerBody .= <<<OFBDYPICS
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; text-align: left; padding: 6px 0px; font-weight: bold;" colspan="2">
            Pictures
        </td>
    </tr>
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; border: 1px solid #ced4da; border-radius: 3px; text-align: left; padding: 6px 12px;" colspan="2">
OFBDYPICS;

  // Add pictures to HTML
  for($p=0;$p<count($pictures[$i]['name']);$p++) {
    $saniFileName = str_replace(" ","-",$pictures[$i]['name'][$p]);
    $offerBody .= "<img src=\"cid:{$saniFileName}\" alt=\"{$pictures[$i]['name'][$p]}\" style=\"max-width: 100%;\"/><br>";
  }

$offerBody .= <<<OFBDYPICS2
        </td>
    </tr>
OFBDYPICS2;

$offerBody .= <<<OFBDYVD
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; text-align: left; padding: 6px 0px; font-weight: bold;" colspan="2">
            Video
        </td>
    </tr>
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; border: 1px solid #ced4da; border-radius: 3px; text-align: left; padding: 6px 12px;" colspan="2">
OFBDYVD;
$offerBody .= ($video[$i] != "" ? "<a href=\"" . $video[$i] . "\" target=\"blank\" title=\"Video of the " . $makeModel[$i] . "\">" . $video[$i] . "</a>" : "&nbsp;");
$offerBody .= <<<OFBDYVDCNTD
        </td>
    </tr>
OFBDYVDCNTD;
$offerBody .= <<<OFBDYINEND
        </tbody>
    </table>
    </td>
</tr>
OFBDYINEND;
}

$offerBody .= <<<OFBDYTO
    <!-- Trade Options -->
    <tr>
        <td style="font-family: sans-serif; font-size: 20px; vertical-align: top; text-align: left; padding: 6px 0px; font-weight: bold;" colspan="2">
            Trade Options
        </td>
    </tr>
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; text-align: left; padding: 6px 0px; font-weight: bold;" colspan="2">
            Are you selling your item(s) or trading?
        </td>
    </tr>
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; border: 1px solid #ced4da; border-radius: 3px; text-align: left; padding: 6px 12px;" colspan="2">
OFBDYTO;
$offerBody .= ($sellOrTrade === "trade" ? "Trading (Receive Store Credit)" : "Selling (Receive Cash) Note: Trading pays much better");
$offerBody .= <<<OFBDYTOCNTD
        </td>
    </tr>
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; text-align: left; padding: 6px 0px; font-weight: bold;" colspan="2">
            If trading, what item(s) are you wanting to trade towards?
        </td>
    </tr>
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; border: 1px solid #ced4da; border-radius: 3px; text-align: left; padding: 6px 12px;" colspan="2">
OFBDYTOCNTD;
$offerBody .= ($tradeTowardsWhat != "" ? $tradeTowardsWhat : "&nbsp;");
$offerBody .= <<<OFBDYTOCNTD2
        </td>
    </tr>    
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; text-align: left; padding: 6px 0px; font-weight: bold;" colspan="2">
            Additional comments/notes
        </td>
    </tr>
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; border: 1px solid #ced4da; border-radius: 3px; text-align: left; padding: 6px 12px;" colspan="2">
OFBDYTOCNTD2;
$offerBody .= ($commentsNotes != "" ? $commentsNotes : "&nbsp;");
$offerBody .= <<<OFBDYTOCNTD3
        </td>
    </tr>    
    </tbody>
</table>
OFBDYTOCNTD3;

// Trade offer
$tradeOffer = ""; // The body of the email for the trade processor
$tradeOffer .= <<<TOHD
<!doctype html>
<html>
  <head>
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Offer ID {$offerId}</title>
    <style>
    /* -------------------------------------
        INLINED WITH htmlemail.io/inline
    ------------------------------------- */
    /* -------------------------------------
        RESPONSIVE AND MOBILE FRIENDLY STYLES
    ------------------------------------- */
    @media only screen and (max-width: 620px) {
      table[class=body] h1 {
        font-size: 28px !important;
        margin-bottom: 10px !important;
      }
      table[class=body] p,
            table[class=body] ul,
            table[class=body] ol,
            table[class=body] td,
            table[class=body] span,
            table[class=body] a {
        font-size: 16px !important;
      }
      table[class=body] .wrapper,
            table[class=body] .article {
        padding: 10px !important;
      }
      table[class=body] .content {
        padding: 0 !important;
      }
      table[class=body] .container {
        padding: 0 !important;
        width: 100% !important;
      }
      table[class=body] .main {
        border-left-width: 0 !important;
        border-radius: 0 !important;
        border-right-width: 0 !important;
      }
      table[class=body] .btn table {
        width: 100% !important;
      }
      table[class=body] .btn a {
        width: 100% !important;
      }
      table[class=body] .img-responsive {
        height: auto !important;
        max-width: 100% !important;
        width: auto !important;
      }
    }

    /* -------------------------------------
        PRESERVE THESE STYLES IN THE HEAD
    ------------------------------------- */
    @media all {
      .ExternalClass {
        width: 100%;
      }
      .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
        line-height: 100%;
      }
      .apple-link a {
        color: inherit !important;
        font-family: inherit !important;
        font-size: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
        text-decoration: none !important;
      }
      #MessageViewBody a {
        color: inherit;
        text-decoration: none;
        font-size: inherit;
        font-family: inherit;
        font-weight: inherit;
        line-height: inherit;
      }
      .btn-primary table td:hover {
        background-color: #34495e !important;
      }
      .btn-primary a:hover {
        background-color: #34495e !important;
        border-color: #34495e !important;
      }
    }
    </style>
  </head>
TOHD;

$tradeOffer .= <<<TOHDR
  <body class="" style="background-color: #212121; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
    <table border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background-color: #212121;">
      <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
        <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; Margin: 0 auto; max-width: 580px; padding: 10px; width: 580px;">
          <div class="content" style="box-sizing: border-box; display: block; Margin: 0 auto; max-width: 580px; padding: 10px;">

            <!-- START CENTERED WHITE CONTAINER -->
            <span class="preheader" style="color: transparent; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;"><img alt="New Breed Paintball &amp; Airsoft" src="https://cdn.shopify.com/s/files/1/1446/2796/t/7/assets/logo.png?v=16706659427728499645" width="300" height="64" border="0" style="border:0; outline:none; text-decoration:none; display:block; max-width: 300px;
            padding: 25px 0px 25px 0px; margin: auto;"></span>
            <table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background: #ffffff; border-radius: 3px; border-top: #8fca5b 10px solid;">
TOHDR;

$tradeOffer .= <<<TOMC
              <!-- START MAIN CONTENT AREA -->
              <tr>
                <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;">
                  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                    <tr>
                      <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px; font-weight: bold;">
                            Offer ID: {$offerId}<br>
                            <span style="font-family: sans-serif; font-size: 11px; font-weight: normal; margin: 0; Margin-top: 6px;">Received: {$timeReceived}</span>
                        </p>
                        {$offerBody}
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">&nbsp;</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

            <!-- END MAIN CONTENT AREA -->
            </table>
TOMC;
            
$tradeOffer .= <<<TOFTR
            <!-- START FOOTER -->
            <div class="footer" style="clear: both; Margin-top: 10px; text-align: center; width: 100%;">
              <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                <tr>
                  <td class="content-block" style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;">
                    <span class="apple-link" style="color: #999999; font-size: 12px; text-align: center;">
                    New Breed Paintball & Airsoft, 7-05 Fair Lawn Ave, Fair Lawn, NJ 07410</span><br>
                    Phone: <a href="tel:+12017910377" style="text-decoration: underline; color: #999999; font-size: 12px; text-align: center;">(201) 791-0377</a>
                  </td>
                </tr>
              </table>
            </div>
            <!-- END FOOTER -->
TOFTR;
            
$tradeOffer .= <<<TOEND
            <!-- END CENTERED WHITE CONTAINER -->
            </div>
          </td>
          <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
        </tr>
      </table>
    </body>
</html>
TOEND;

// Trade receipt
$tradeReceipt = ""; // The body of the email for the customer
$tradeReceipt .= <<<TRHD
<!doctype html>
<html>
  <head>
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Receipt for offer {$offerId}</title>
    <style>
    /* -------------------------------------
        INLINED WITH htmlemail.io/inline
    ------------------------------------- */
    /* -------------------------------------
        RESPONSIVE AND MOBILE FRIENDLY STYLES
    ------------------------------------- */
    @media only screen and (max-width: 620px) {
      table[class=body] h1 {
        font-size: 28px !important;
        margin-bottom: 10px !important;
      }
      table[class=body] p,
            table[class=body] ul,
            table[class=body] ol,
            table[class=body] td,
            table[class=body] span,
            table[class=body] a {
        font-size: 16px !important;
      }
      table[class=body] .wrapper,
            table[class=body] .article {
        padding: 10px !important;
      }
      table[class=body] .content {
        padding: 0 !important;
      }
      table[class=body] .container {
        padding: 0 !important;
        width: 100% !important;
      }
      table[class=body] .main {
        border-left-width: 0 !important;
        border-radius: 0 !important;
        border-right-width: 0 !important;
      }
      table[class=body] .btn table {
        width: 100% !important;
      }
      table[class=body] .btn a {
        width: 100% !important;
      }
      table[class=body] .img-responsive {
        height: auto !important;
        max-width: 100% !important;
        width: auto !important;
      }
    }

    /* -------------------------------------
        PRESERVE THESE STYLES IN THE HEAD
    ------------------------------------- */
    @media all {
      .ExternalClass {
        width: 100%;
      }
      .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
        line-height: 100%;
      }
      .apple-link a {
        color: inherit !important;
        font-family: inherit !important;
        font-size: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
        text-decoration: none !important;
      }
      #MessageViewBody a {
        color: inherit;
        text-decoration: none;
        font-size: inherit;
        font-family: inherit;
        font-weight: inherit;
        line-height: inherit;
      }
      .btn-primary table td:hover {
        background-color: #34495e !important;
      }
      .btn-primary a:hover {
        background-color: #34495e !important;
        border-color: #34495e !important;
      }
    }
    </style>
  </head>
TRHD;

$tradeReceipt .= <<<TRHDR
  <body class="" style="background-color: #212121; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
    <table border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background-color: #212121;">
      <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
        <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; Margin: 0 auto; max-width: 580px; padding: 10px; width: 580px;">
          <div class="content" style="box-sizing: border-box; display: block; Margin: 0 auto; max-width: 580px; padding: 10px;">

            <!-- START CENTERED WHITE CONTAINER -->
            <span class="preheader" style="color: transparent; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;"><img alt="New Breed Paintball &amp; Airsoft" src="https://cdn.shopify.com/s/files/1/1446/2796/t/7/assets/logo.png?v=16706659427728499645" width="300" height="64" border="0" style="border:0; outline:none; text-decoration:none; display:block; max-width: 300px;
            padding: 25px 0px 25px 0px; margin: auto;"></span>
            <table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background: #ffffff; border-radius: 3px; border-top: #8fca5b 10px solid;">
TRHDR;

$tradeReceipt .= <<<TRMC
              <!-- START MAIN CONTENT AREA -->
              <tr>
                <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;">
                  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                    <tr>
                      <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
                            Hi {$fName},
                        </p>  
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
                            Thank you for submitting an offer! If you would like to add more detail or forgot to add something, simply reply to this email.
                        </p>
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
                            Please allow 48 business hours for us to get back with you. Do not submit multiple offers for the same items as this will only slow our response time.
                        </p>
                        <hr>
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px; font-weight: bold;">
                            Offer ID: {$offerId}<br>
                            <span style="font-family: sans-serif; font-size: 11px; font-weight: normal; margin: 0; Margin-top: 6px;">Received: {$timeReceived}</span>
                        </p>
                        {$offerBody}
                        <hr>
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Thanks for doing business with us!</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

            <!-- END MAIN CONTENT AREA -->
            </table>
TRMC;
            
$tradeReceipt .= <<<TRFTR
            <!-- START FOOTER -->
            <div class="footer" style="clear: both; Margin-top: 10px; text-align: center; width: 100%;">
              <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                <tr>
                  <td class="content-block" style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;">
                    <span class="apple-link" style="color: #999999; font-size: 12px; text-align: center;">
                    New Breed Paintball & Airsoft, 7-05 Fair Lawn Ave, Fair Lawn, NJ 07410</span><br>
                    Phone: <a href="tel:+12017910377" style="text-decoration: underline; color: #999999; font-size: 12px; text-align: center;">(201) 791-0377</a>
                  </td>
                </tr>
              </table>
            </div>
            <!-- END FOOTER -->
TRFTR;
            
$tradeReceipt .= <<<TREND
            <!-- END CENTERED WHITE CONTAINER -->
            </div>
          </td>
          <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
        </tr>
      </table>
    </body>
</html>
TREND;

/* 
 * Custom PHP function to send an email with multiple attachments from: https://www.codexworld.com/send-email-with-multiple-attachments-php/
 * $to Recipient email address 
 * $subject Subject of the email 
 * $message Mail body content 
 * $senderEmail Sender email address 
 * $senderName Sender name 
 * $files Files to attach with the email 
 */ 
function multi_attach_mail($to, $subject, $message, $senderEmail, $senderName, $replyTo, $files = array()){ 
 
  $from = $senderName." <".$senderEmail.">";  
  $headers = "From: {$from}";
  $headers .= "\nReply-To: {$replyTo}";

  // Boundary  
  $semi_rand = md5(time());  
  $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";  

  // Headers for attachment  
  $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";  

  // Multipart boundary  
  $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" . 
  "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";  

  // Preparing attachment 
  if(!empty($files)){ 
      for($i=0;$i<count($files);$i++){ 
          if(is_file($files[$i])){ 
              $file_name = basename($files[$i]); 
              $file_size = filesize($files[$i]); 
               
              $message .= "--{$mime_boundary}\n"; 
              $fp =    @fopen($files[$i], "rb"); 
              $data =  @fread($fp, $file_size); 
              @fclose($fp); 
              $data = chunk_split(base64_encode($data)); 
              $message .= "Content-Type: application/octet-stream; name=\"".$file_name."\"\n" .  
              "Content-Description: ".$file_name."\n" . 
              "Content-ID: <".$file_name.">\n" . 
              "Content-Disposition: inline;\n" . " filename=\"".$file_name."\"; size=".$file_size.";\n" .  
              "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n"; 
          } 
      } 
  } 
   
  $message .= "--{$mime_boundary}--";
  $returnpath = "-f" . $senderEmail; 
   
  // Send email 
  $mail = @mail($to, $subject, $message, $headers, $returnpath);  
   
  // Return true, if email sent, otherwise return false 
  if($mail){ 
      return true; 
  }else{ 
      return false; 
  } 
}

// Make directory for storing uploaded pictures based on offer id
mkdir("./uploads/{$offerId}");

// Build file array for attaching to email
$files = [];
for($i=0;$i<count($pictures);$i++) { // Move through items picture sets
  for($j=0;$j<count($pictures[$i+1]['tmp_name']);$j++) { // Move through each item's pictures
    $saniFileName = str_replace(" ","-","{$pictures[$i+1]['name'][$j]}"); // Replace spaces with dashes in file name
    move_uploaded_file($pictures[$i+1]['tmp_name'][$j],"./uploads/{$offerId}/{$saniFileName}");
    $files[] = "./uploads/{$offerId}/{$saniFileName}"; // Add to attachment array
  }
}

// Send trade offer to trade processor
$sendEmail = multi_attach_mail($tradeProcessorEmail, "{$fName} {$lName[0]}. wants to " . ($sellOrTrade === "trade" ? "trade a" : "sell a") . " {$makeModel[1]}", $tradeOffer, "noreply@newbreedpb.com", "Trade Form", $email, $files);

// Send trade receipt to customer
$sendEmail = multi_attach_mail($email, "Receipt for your " . ($sellOrTrade === "trade" ? "trade" : "sell") . " offer of a {$makeModel[1]}", $tradeReceipt, "noreply@newbreedpb.com", "New Breed Paintball & Airsoft", $tradeProcessorEmail, $files);
 
// // Email sending status 
// if($sendEmail){ 
//     echo 'The email has sent successfully.'; 
// }else{ 
//     echo 'Mail sending failed!'; 
// }

/* ***************************** */
/*      Display results page     */
/* ***************************** */
displayResultPage();

?>