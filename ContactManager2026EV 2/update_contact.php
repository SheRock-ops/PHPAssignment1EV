<?php
    session_start();

    // Retrieve data from form
    $contact_id = filter_input(INPUT_POST, 'contact_id', FILTER_VALIDATE_INT);
    $first_name = filter_input(INPUT_POST, 'first_name');
    $last_name = filter_input(INPUT_POST, 'last_name');
    $email_address = filter_input(INPUT_POST, 'email_address');
    $phone_number = filter_input(INPUT_POST, 'phone_number');
    $birth_date = filter_input(INPUT_POST, 'birth_date');
    $type_id = filter_input(INPUT_POST, 'type_id', FILTER_VALIDATE_INT);

    require_once("database.php"); //prevent duplicate connections

    // Validation to be added later to prevent duplicate contacts and ensure no null data

    $queryContacts = "SELECT * FROM contacts";

    $statement = $db->prepare($queryContacts);
    $statement->execute();
    $contacts = $statement->fetchAll();
    $statement->closeCursor();

    foreach ($contacts as $contact) {
        if ($email_address == $contact["emailAddress"] && $contact_id != $contact["contactID"]) {
            $_SESSION["update_error"] = "Invalid data. Duplicate Email Address. Try again.";

            $url = "update_error.php";
            header("Location: " . $url);
            die();
        }
    }

    if ($first_name == null || $last_name == null || $email_address == null ||
        $phone_number == null || $birth_date == null || $type_id == null) {
            $_SESSION["update_error"] = "Invalid contact data. Check all fields and try again.";

            $url = "update_error.php";
            header("Location: " . $url);
            die();
        }



    // Update Contact

    $query = 'UPDATE contacts 
        SET firstName = :firstName,
            lastName = :lastName,
            emailAddress = :emailAddress,
            phoneNumber = :phoneNumber,
            dob = :dob,
            typeID = :typeID
        WHERE contactID = :contactID';

    $statement = $db->prepare($query);

    $statement->bindValue(':firstName', $first_name);
    $statement->bindValue(':lastName', $last_name);
    $statement->bindValue(':emailAddress', $email_address);
    $statement->bindValue(':phoneNumber', $phone_number);
    $statement->bindValue(':dob', $birth_date);
    $statement->bindValue(':typeID', $type_id);
    $statement->bindValue(':contactID', $contact_id);

    $statement->execute();    
    $statement->closeCursor();


    // Redirect to the confirmation page

    $_SESSION["fullName"] = $first_name . " " . $last_name;
    $url = "update_contact_confirmation.php";
    header("Location: " . $url);
    die();

?>
