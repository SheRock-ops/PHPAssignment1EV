<?php

    require_once("database.php");

    // get data from the form
    $contact_id = filter_input(INPUT_POST, 'contact_id', FILTER_VALIDATE_INT);

    // get current contact record
    $queryContacts = "SELECT * FROM contacts WHERE contactID = :contact_id";

    $statement = $db->prepare($queryContacts);
    $statement->bindValue(':contact_id', $contact_id);
    $statement->execute();
    $contact = $statement->fetch();
    $statement->closeCursor();

    // delete contact from database
    $queryDelete = "DELETE FROM contacts WHERE contactID = :contact_id";

    $statement = $db->prepare($queryDelete);
    $statement->bindValue(':contact_id', $contact_id);
    $statement->execute();
    
    $statement->closeCursor();

    // reload the index page
    $url = "index.php";
    header("Location: " . $url);
    die();

?>