
<?php
    session_start();

    // Check if user is logged in
    if (empty($_SESSION['isLoggedIn']) || empty($_SESSION['userID']))
    {
        header("Location: login_form.php");
        die();
    }

    require_once("database.php");

    // Get data from the form
    $contact_id = filter_input(INPUT_POST, 'contact_id', FILTER_VALIDATE_INT);

    // Validate contact id
    if (!$contact_id)
    {
        header("Location: index.php");
        die();
    }

    // Get current contact record belonging to logged in user
    $queryContacts = "SELECT * FROM contacts
        WHERE contactID = :contact_id AND userID = :userID";

    $statement = $db->prepare($queryContacts);
    $statement->bindValue(':contact_id', $contact_id, PDO::PARAM_INT);
    $statement->bindValue(':userID', $_SESSION['userID'], PDO::PARAM_INT);
    $statement->execute();
    $contact = $statement->fetch();
    $statement->closeCursor();

    // Check if contact exists and belongs to user
    if (!$contact)
    {
        header("Location: index.php");
        die();
    }

    // Delete contact from database
    $queryDelete = "DELETE FROM contacts
        WHERE contactID = :contact_id AND userID = :userID";

    $statement = $db->prepare($queryDelete);
    $statement->bindValue(':contact_id', $contact_id, PDO::PARAM_INT);
    $statement->bindValue(':userID', $_SESSION['userID'], PDO::PARAM_INT);
    $statement->execute();
    $statement->closeCursor();

    // Reload the index page
    $url = "index.php";
    header("Location: " . $url);
    die();

?>
