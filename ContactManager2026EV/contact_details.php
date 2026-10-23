
<?php
    session_start();

    // Check if user is logged in
    if (empty($_SESSION['isLoggedIn']) || empty($_SESSION['userID']))
    {
        header("Location: login_form.php");
        die();
    }

    require("database.php");

    // Get contact id
    $contact_id = filter_input(INPUT_POST, 'contact_id', FILTER_VALIDATE_INT);

    if (!$contact_id) {
        header("Location: index.php");
        exit();
    }

    // Get contact belonging to the logged in user
    $queryContacts = "SELECT c.contactID, c.firstName, c.lastName, c.emailAddress, c.phoneNumber, c.dob,
        c.typeID, c.imageName, t.contactType FROM contacts c LEFT JOIN types t ON c.typeID = t.typeID
        WHERE c.contactID = :contactID AND c.userID = :userID";

    $statement = $db->prepare($queryContacts);
    $statement->bindValue(':contactID', $contact_id, PDO::PARAM_INT);
    $statement->bindValue(':userID', $_SESSION['userID'], PDO::PARAM_INT);
    $statement->execute();
    $contact = $statement->fetch();
    $statement->closeCursor();

    if (!$contact) {
        header("Location: index.php");
        exit();
    }

    // Convert _100 image to _400 version
    $imageName = $contact['imageName'];
    $dotPosition = strrpos($imageName, '.');

    $baseName = substr($imageName, 0, $dotPosition);
    $extension = substr($imageName, $dotPosition);

    if (str_ends_with($baseName, '_100'))
    {
        $baseName = substr($baseName, 0, -4);
    }

    $imageName_400 = $baseName . '_400' . $extension;

    // Use placeholder if the image does not exist
    if (!file_exists('./images/' . $imageName_400)) {
        $imageName_400 = 'placeholder_400.jpg';
    }

?>

<!DOCTYPE html>

<html>

    <head>

        <title>Contact Manager - Contact Details</title>
        <link rel="stylesheet" type="text/css" href="css/contact.css" />

    </head>

    <body>

        <?php include("header.php"); ?>

        <main>
            <div class="container">
                <h2>Contact Details</h2>

                <img class="contact-image" src="<?php echo htmlspecialchars('./images/' . $imageName_400); ?>"
                    alt="Contact Image" height="400" />

                <div class="contact-info">
                    <p><strong>First Name:</strong> <?php echo htmlspecialchars($contact['firstName']); ?></p>
                    <p><strong>Last Name:</strong> <?php echo htmlspecialchars($contact['lastName']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($contact['emailAddress']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($contact['phoneNumber']); ?></p>
                    <p><strong>Birth Date:</strong> <?php echo htmlspecialchars($contact['dob']); ?></p>
                    <p><strong>Contact Type:</strong> <?php echo htmlspecialchars($contact['contactType'] ?? ''); ?></p>
                </div>

                <p><a class="back-link" href="index.php">Back to Contact List</a></p>
            </div>
        </main>

        <?php include("footer.php"); ?>

    </body>

</html>
