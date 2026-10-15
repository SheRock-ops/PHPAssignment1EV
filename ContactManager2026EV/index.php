<?php
    require("database.php");

    $queryContacts = "SELECT c.contactID, c.firstName, c.lastName, c.emailAddress, c.phoneNumber, c.dob, 
        c.typeID, c.imageName, t.contactType FROM contacts c LEFT JOIN types t ON c.typeID = t.typeID";

    $statement = $db->prepare($queryContacts);
    $statement->execute();
    $contacts = $statement->fetchAll();
    $statement->closeCursor();

?>

<!DOCTYPE html>

<html>

    <head>

        <title>Contact Manager - Home</title>
        <link rel="stylesheet" type="text/css" href="css/contact.css" />

    </head>

    <body>

        <?php include("header.php"); ?>

        <main>

            <h2>Contact List</h2>

            <table>

                <tr>

                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email Address</th>
                    <th>Phone Number</th>
                    <th>Birth Date</th>
                    <th>Contact Type</th>
                    <th>Photo</th>
                    <th>&nbsp;</th> <!-- for update -->
                    <th>&nbsp;</th> <!-- for delete -->
                    <th>&nbsp;</th> <!-- for view details -->

                </tr>

                <?php foreach ($contacts as $contact): ?>

                    <tr>
                        <td><?php echo htmlspecialchars($contact['firstName']); ?></td>
                        <td><?php echo htmlspecialchars($contact['lastName']); ?></td>
                        <td><?php echo htmlspecialchars($contact['emailAddress']); ?></td>
                        <td><?php echo htmlspecialchars($contact['phoneNumber']); ?></td>
                        <td><?php echo htmlspecialchars($contact['dob']); ?></td>
                        <td><?php echo htmlspecialchars($contact['contactType']); ?></td>

                        <td>
                            <img src="<?php echo htmlspecialchars('./images/' . $contact['imageName']); ?>"
                                alt="<?php echo htmlspecialchars($contact['firstName'] . ' ' . $contact['lastName']); ?>" />

                        </td>

                        <td>
                            <form action="update_contact_form.php" method="post">
                                <input type="hidden" name="contact_id" value="<?php echo $contact['contactID']; ?>" />
                                <input type="submit" value="Update" />
                            </form>
                        </td>

                        <td>
                            <form action="delete_contact.php" method="post">
                                <input type="hidden" name="contact_id" value="<?php echo $contact['contactID']; ?>" />
                                <input type="submit" value="Delete" />
                            </form>
                        </td>

                        <td>
                            <form action="contact_details.php" method="post">
                                <input type="hidden" name="contact_id" value="<?php echo $contact['contactID']; ?>" />
                                <input type="submit" value="View Details" />
                            </form>
                        </td>

                    </tr>

                <?php endforeach; ?>
           
            </table>

            <p><a href="add_contact_form.php">Add Contact</a></p>

            <!-- temporary link to the register user form for testing purposes -->
            <p><a href="register_user_form.php">Register User - Temporary</a></p>

            <!-- temporary link to the login form for testing purposes -->
            <p><a href="login_form.php">Login - Temporary</a></p>
            
            <p><a href="logout.php">Logout</a></p>

        </main>

        <?php include("footer.php"); ?>
        
    </body>

</html>