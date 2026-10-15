<?php
    session_start();
    require("database.php");

    // get contact id
    $contact_id = filter_input(INPUT_POST, 'contact_id', FILTER_VALIDATE_INT);

    if (!$contact_id) {
        header("Location: index.php");
        exit();
    }

    $queryContacts = "SELECT c.contactID, c.firstName, c.lastName, c.emailAddress, c.phoneNumber, c.dob, 
        c.typeID, c.imageName, t.contactType FROM contacts c LEFT JOIN types t ON c.typeID = t.typeID 
        WHERE contactID = :contactID";

    $statement = $db->prepare($queryContacts);
    $statement->bindValue(':contactID', $contact_id);
    $statement->execute();
    $contact = $statement->fetch();
    $statement->closeCursor();

    if (!$contact) {
        echo "Contact not found.";
        exit();
    }

    // Convert _100 image to _400 version
    $imageName = $contact['imageName']; // example: Bugs_Bunny_100.png
    $dotPosition = strrpos($imageName, '.'); // example: 15 which is the position of the - in $imageName
    $baseName = substr($imageName, 0, $dotPosition); // example: Bugs_Bunny_100 which is the substring preceding
                                                     // the . in $imageName
    $extension = substr($imageName, $dotPosition); // example: .png which is starting at position 15
                                                   // until the end of the string

    if (str_ends_with($baseName, '_100'))
    {
        $baseName = substr($baseName, 0, -4); // example: Bugs_Bunny - remove the last 4 characters 
                                              // from the string
    }

    $imageName_400 = $baseName . '_400' . $extension; // example: Bugs_Bunny_400.png
                                                      // Bugs_Bunny + _400 + .png

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
                    <p><strong>Contact Type:</strong> <?php echo htmlspecialchars($contact['contactType']); ?></p>
                </div>
                
                <p><a class="back-link" href="index.php">Back to Contact List</a></p>
            </div>
        </main>

        <?php include("footer.php"); ?>
        
    </body>

</html>