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

    // get all types
    $queryTypes = "SELECT * FROM types";

    $statement = $db->prepare($queryTypes);
    $statement->execute();
    $types = $statement->fetchAll();
    $statement->closeCursor();

?>

<!DOCTYPE html>

<html>

    <head>

        <title>Contact Manager - Update Contact</title>
        <link rel="stylesheet" type="text/css" href="css/contact.css" />

    </head>

    <body>

        <?php include("header.php"); ?>

        <main>

            <h2>Update Contact</h2>

            <form action="update_contact.php" method="post" id="update_contact_form" enctype="multipart/form-data">

                <input type="hidden" name="contact_id" value="<?php echo $contact['contactID']; ?>" />
            
                <div id="data">

                    <label>First Name:</label>
                    <input type="text" name="first_name" value="<?php echo $contact['firstName']; ?>" /><br />

                    <label>Last Name:</label>
                    <input type="text" name="last_name" value="<?php echo $contact['lastName']; ?>" /><br />

                    <label>Email Address:</label>
                    <input type="text" name="email_address" value="<?php echo $contact['emailAddress']; ?>" /><br />

                    <label>Phone Number:</label>
                    <input type="text" name="phone_number" value="<?php echo $contact['phoneNumber']; ?>" /><br />

                    <label>Birth Date:</label>
                    <input type="date" name="birth_date" value="<?php echo $contact['dob']; ?>" /><br />

                    <label>Contact Type</label>
                    <select name="type_id">
                        
                        <?php foreach($types as $type): ?>
                            <option value="<?php echo $type['typeID']; ?>"
                             <?php if ($type['typeID'] == $contact['typeID']) echo 'selected'; ?>>
                                <?php echo $type['contactType']; ?>
                            </option>
                        <?php endforeach; ?>

                    </select><br />

                    <?php if (!empty($contact['imageName'])): ?>
                        <label>Current Image </label>
                        <img id="imagePreview" src="images/<?php echo htmlspecialchars($contact['imageName']); ?>"
                            alt="Contact Image" height="100" /><br />
                    <?php endif; ?>

                    <label>Update Image</label>
                    <input type="file" name="file1" id="file1" accept="image/*" /><br />

                </div>

                <div id="buttons">

                    <label>&nbsp;</label>
                    <input type="submit" value="Update Contact" /><br />
                </div>

            </form>

            <p><a href="index.php">View Contact List</a></p>

        </main>

        <?php include("footer.php"); ?>

        <script>

            const fileInput = document.getElementById("file1");
            const imagePreview = document.getElementById("imagePreview");
            
            fileInput.addEventListener("change", function () {
                const file = this.files[0];
                if (file)
                {
                    const imageURL = URL.createObjectURL(file);
                    imagePreview.src = imageURL;
                }            
            });





        </script>
        
    </body>

</html>