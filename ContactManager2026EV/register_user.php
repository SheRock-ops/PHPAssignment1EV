<?php
    session_start();

    // Retrieve data from form
    $user_name = trim((string) filter_input(INPUT_POST, 'user_name'));
    $user_password = (string) filter_input(INPUT_POST, 'password');
    $confirm_password = (string) filter_input(INPUT_POST, 'confirm_password');

    require_once("database.php"); // prevent duplicate connections

    // Validate registration data
    if ($user_name == '' || $user_password == '' ||
        $confirm_password == '') {

        $_SESSION["register_error"] =
            "Invalid registration data. Check all fields and try again.";

        header("Location: register_user_form.php");
        die();
    }

    if (strlen($user_name) > 50 || strlen($user_password) < 8) {

        $_SESSION["register_error"] =
            "Username must be 50 characters or less and password must be at least 8 characters.";

        header("Location: register_user_form.php");
        die();
    }

    // Check if passwords match
    if ($user_password !== $confirm_password) {

        $_SESSION["register_error"] =
            "Passwords do not match. Please try again.";

        header("Location: register_user_form.php");
        die();
    }

    // Check for duplicate username
    $queryRegistrations =
        "SELECT userID FROM registrations WHERE userName = :userName";

    $statement = $db->prepare($queryRegistrations);
    $statement->bindValue(':userName', $user_name);
    $statement->execute();
    $user = $statement->fetch();
    $statement->closeCursor();

    if ($user) {

        $_SESSION["register_error"] =
            "Username already exists. Please choose another.";

        header("Location: register_user_form.php");
        die();
    }

    // Hash password
    $hash = password_hash($user_password, PASSWORD_DEFAULT);

    // Add Registration
    $query = 'INSERT INTO registrations (userName, password)
              VALUES (:userName, :password)';

    $statement = $db->prepare($query);

    $statement->bindValue(':userName', $user_name);
    $statement->bindValue(':password', $hash);

    try {
        $statement->execute();
    }
    catch (PDOException $e) {

        $_SESSION["register_error"] =
            "Registration could not be completed. Please try again.";

        header("Location: register_user_form.php");
        die();
    }

    // Get the newly registered user's ID
    $user_id = $db->lastInsertId();

    $statement->closeCursor();

    // Automatically log in the new user
    session_regenerate_id(true);

    $_SESSION['isLoggedIn'] = TRUE;
    $_SESSION['userName'] = $user_name;
    $_SESSION['userID'] = (int) $user_id;

    unset($_SESSION['register_error']);
    unset($_SESSION['login_error']);
    unset($_SESSION['lockout_until']);

    // Redirect to login confirmation page
    $url = "login_confirmation.php";
    header("Location: " . $url);
    die();

?>
