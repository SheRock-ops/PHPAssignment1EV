
<?php
    session_start();

    // Check if user is logged in
    if (empty($_SESSION['isLoggedIn']) || empty($_SESSION['userID']))
    {
        header("Location: login_form.php");
        die();
    }

    // Retrieve data from form
    $first_name = filter_input(INPUT_POST, 'first_name');
    $last_name = filter_input(INPUT_POST, 'last_name');
    $email_address = filter_input(INPUT_POST, 'email_address');
    $phone_number = filter_input(INPUT_POST, 'phone_number');
    $birth_date = filter_input(INPUT_POST, 'birth_date');
    $type_id = filter_input(INPUT_POST, 'type_id', FILTER_VALIDATE_INT);
    $image = $_FILES['file1'] ?? null;

    require_once("database.php"); //prevent duplicate connections
    require_once("image_util.php");

    $base_dir = 'images/';

    // Validate contact data
    if ($first_name == null || $last_name == null || $email_address == null ||
        $phone_number == null || $birth_date == null || $type_id == null) {

        $_SESSION["add_error"] = "Invalid contact data. Check all fields and try again.";

        $url = "add_error.php";
        header("Location: " . $url);
        die();
    }

    // Check for duplicate email address for logged in user
    $queryContacts = "SELECT * FROM contacts WHERE userID = :userID";

    $statement = $db->prepare($queryContacts);
    $statement->bindValue(':userID', $_SESSION['userID']);
    $statement->execute();
    $contacts = $statement->fetchAll();
    $statement->closeCursor();

    foreach ($contacts as $contact) {
        if ($email_address == $contact["emailAddress"]) {
            $_SESSION["add_error"] = "Invalid data. Duplicate Email Address. Try again.";

            $url = "add_error.php";
            header("Location: " . $url);
            die();
        }
    }

    $image_name = ''; //default empty

    // ***** Image Upload *****

    if ($image && $image['error'] == UPLOAD_ERR_OK) {
        // process new image
        $original_filename = basename($image['name']);
        $upload_path = $base_dir . $original_filename;

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $detected_type = mime_content_type($image['tmp_name']);

        if (!in_array($detected_type, $allowed_types, true)) {
            $_SESSION["add_error"] = "Invalid image type. Please upload a JPG, PNG, or GIF image.";

            header("Location: add_error.php");
            die();
        }

        // Generate a unique filename to prevent overwriting images
        $extension = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));

        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'], true)) {
            $_SESSION["add_error"] = "Invalid image extension.";

            header("Location: add_error.php");
            die();
        }

        $original_filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $upload_path = $base_dir . $original_filename;

        if (!move_uploaded_file($image['tmp_name'], $upload_path)) {
            $_SESSION["add_error"] = "Image upload failed. Please try again.";

            header("Location: add_error.php");
            die();
        }

        process_image($base_dir, $original_filename);

        // save _100 version in DB
        $dot_pos = strrpos($original_filename, '.');
        $name_100 = substr($original_filename, 0, $dot_pos) . '_100' . substr($original_filename, $dot_pos);
        $image_name = $name_100;
    }
    else {
        // Use placeholder
        $placeholder = 'placeholder.jpg';
        $placeholder_100 = 'placeholder_100.jpg';
        $placeholder_400 = 'placeholder_400.jpg';

        if (!file_exists($base_dir . $placeholder_100) || !file_exists($base_dir . $placeholder_400)) {
            process_image($base_dir, $placeholder);
        }

        $image_name = $placeholder_100;
    }

    // Add Contact

    $query = 'INSERT INTO contacts (firstName, lastName, emailAddress, phoneNumber, dob, typeID, imageName, userID)
        VALUES (:firstName, :lastName, :emailAddress, :phoneNumber, :dob, :typeID, :imageName, :userID)';

    $statement = $db->prepare($query);

    $statement->bindValue(':firstName', $first_name);
    $statement->bindValue(':lastName', $last_name);
    $statement->bindValue(':emailAddress', $email_address);
    $statement->bindValue(':phoneNumber', $phone_number);
    $statement->bindValue(':dob', $birth_date);
    $statement->bindValue(':typeID', $type_id);
    $statement->bindValue(':imageName', $image_name);
    $statement->bindValue(':userID', $_SESSION['userID'], PDO::PARAM_INT);

    $statement->execute();
    $statement->closeCursor();

    // Redirect to the confirmation page

    $_SESSION["fullName"] = $first_name . " " . $last_name;
    $url = "add_contact_confirmation.php";
    header("Location: " . $url);
    die();

?>
