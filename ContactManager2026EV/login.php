
<?php
    session_start();
    date_default_timezone_set("America/Toronto");

    // Those on Atlantic Time Zone can use:
    // date_default_timezone_set("America/Halifax");

    require_once("database.php");

    $user_name = filter_input(INPUT_POST, 'user_name');
    $user_password = filter_input(INPUT_POST, 'password');

    // Retrieve the user.
    $queryUsers = 'SELECT * FROM registrations WHERE userName = :userName';

    $statement = $db->prepare($queryUsers);
    $statement->bindValue(':userName', $user_name);
    $statement->execute();
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    $statement->closeCursor();

    if ($user)
    {
        $failedAttempts = (int) ($user['failedAttempts'] ?? 0);
        $lastFailedLogin = $user['lastFailedLogin'];

        // Check whether the account is currently locked.
        if ($failedAttempts >= 3 && $lastFailedLogin !== NULL)
        {
            $lockoutEnd = strtotime($lastFailedLogin) + 300;
            $remaining = $lockoutEnd - time();

            if ($remaining > 0)
            {
                $_SESSION['lockout_until'] = $lockoutEnd;
                $_SESSION['login_error'] =
                    "Account locked. Please wait for the countdown to finish.";

                header("Location: login_form.php");
                exit();
            }

            // The five-minute lockout has expired.
            // Reset the counter so a new set of attempts can begin.
            $query = "UPDATE registrations
                      SET failedAttempts = 0,
                          lastFailedLogin = NULL
                      WHERE userName = :userName";

            $statement = $db->prepare($query);
            $statement->bindValue(':userName', $user_name);
            $statement->execute();
            $statement->closeCursor();

            $failedAttempts = 0;

            // Update the local copy of the user data.
            $user['failedAttempts'] = 0;
            $user['lastFailedLogin'] = NULL;
        }

        // Verify the submitted password.
        if (password_verify($user_password, $user['password']))
        {
            // Login successful.
            session_regenerate_id(true);

            $_SESSION['isLoggedIn'] = TRUE;
            $_SESSION['userName'] = $user['userName'];
            $_SESSION['userID'] = $user['userID'];

            // Reset failed attempts after successful login.
            $query = "UPDATE registrations
                      SET failedAttempts = 0,
                          lastFailedLogin = NULL
                      WHERE userName = :userName";

            $statement = $db->prepare($query);
            $statement->bindValue(':userName', $user_name);
            $statement->execute();
            $statement->closeCursor();

            unset($_SESSION['login_error']);
            unset($_SESSION['lockout_until']);

            header("Location: login_confirmation.php");
            exit();
        }
        else
        {
            // Incorrect password: record the failed attempt.
            $query = "UPDATE registrations
                      SET failedAttempts = COALESCE(failedAttempts, 0) + 1,
                          lastFailedLogin = NOW()
                      WHERE userName = :userName";

            $statement = $db->prepare($query);
            $statement->bindValue(':userName', $user_name);
            $statement->execute();
            $statement->closeCursor();

            // Retrieve the updated counter and timestamp.
            $query = "SELECT failedAttempts, lastFailedLogin
                      FROM registrations
                      WHERE userName = :userName";

            $statement = $db->prepare($query);
            $statement->bindValue(':userName', $user_name);
            $statement->execute();
            $updatedUser = $statement->fetch(PDO::FETCH_ASSOC);
            $statement->closeCursor();

            $updatedAttempts = (int) $updatedUser['failedAttempts'];

            if ($updatedAttempts >= 3)
            {
                // Lock the account for five minutes from the third
                // incorrect password attempt.
                $lockoutEnd = strtotime($updatedUser['lastFailedLogin']) + 300;

                $_SESSION['lockout_until'] = $lockoutEnd;
                $_SESSION['login_error'] =
                    "Account locked. Please wait for the countdown to finish.";
            }
            else
            {
                unset($_SESSION['lockout_until']);

                $_SESSION['login_error'] =
                    "Incorrect password. Failed attempt "
                    . $updatedAttempts . " of 3.";
            }

            header("Location: login_form.php");
            exit();
        }
    }
    else
    {
        unset($_SESSION['lockout_until']);

        $_SESSION['login_error'] = "User not found.";

        header("Location: login_form.php");
        exit();
    }
?>
