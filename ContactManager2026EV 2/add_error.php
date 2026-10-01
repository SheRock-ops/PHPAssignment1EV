<?php
    session_start();
?>

<!DOCTYPE html>

<html>

    <head>

        <title>Contact Manager - Add Error</title>
        <link rel="stylesheet" type="text/css" href="css/contact.css" />

    </head>

    <body>

        <?php include("header.php"); ?>

        <main>

            <h2>Add Error</h2>
            <p>There was an error adding to the database.</p>
            
            <p>Error Message: <?php echo $_SESSION["add_error"]; ?></p>

            <p><a href="add_contact_form.php">Add Contact</a></p>
            <p><a href="index.php">View Contact List</a></p>

        </main>

        <?php include("footer.php"); ?>

    </body>

</html>