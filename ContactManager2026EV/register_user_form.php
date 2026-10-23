
<?php
    session_start();

    $register_error = $_SESSION['register_error'] ?? '';
    unset($_SESSION['register_error']);
?>

<!DOCTYPE html>

<html>

    <head>

        <title>Contact Manager - Register User</title>
        <link rel="stylesheet" type="text/css" href="css/contact.css" />

    </head>

    <body>

        <?php include("header.php"); ?>

        <main>

            <h2>Register User</h2>

            <?php if ($register_error != ''): ?>
                <p style="color: red; font-weight: bold;">
                    <?php echo htmlspecialchars($register_error); ?>
                </p>
            <?php endif; ?>

            <form action="register_user.php" method="post" id="register_user_form">

                <div id="data">

                    <label>Username:</label>
                    <input type="text" name="user_name"
                           maxlength="50" required /><br />

                    <label>Password:</label>
                    <input type="password" name="password"
                           minlength="8" required /><br />

                    <label>Confirm Password:</label>
                    <input type="password" name="confirm_password"
                           minlength="8" required /><br />

                </div>

                <div id="buttons">

                    <label>&nbsp;</label>
                    <input type="submit" value="Register" /><br />

                </div>

            </form>

            <p><a href="login_form.php">View Login</a></p>

        </main>

        <?php include("footer.php"); ?>

    </body>

</html>
