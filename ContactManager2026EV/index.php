<?php
    require("database.php");

    $queryContacts = "SELECT * FROM contacts";

    $statement = $db->prepare($queryContacts);
    $statement->execute();
    $contacts = $statement->fetchAll();
    $statement->closeCursor();

?>

<!DOCTYPE html>

<html>

  <head>

    <title>Contact Manager - Home</title>

  </head>

  <body>

    <h1>Contact Manager System</h1>

    <main>

        <h2>Contact List</h2>

        <table>

            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email Address</th>
                <th>Phone Number</th>
                <th>Birth Date</th>

            </tr>

            <?php foreach ($contacts as $contact): ?>
            
              <tr>
                <td><?php echo htmlspecialchars($contact['firstName']); ?></td>
                <td><?php echo htmlspecialchars($contact['lastName']); ?></td>
                <td><?php echo htmlspecialchars($contact['emailAddress']); ?></td>
                <td><?php echo htmlspecialchars($contact['phoneNumber']); ?></td>
                <td><?php echo htmlspecialchars($contact['dob']); ?></td>
              </tr>

            <?php endforeach; ?>


        </table>


    </main>

    <h2>Copyright &copy; 2026 - All Rights Reserved</h2>

  </body>

</html>