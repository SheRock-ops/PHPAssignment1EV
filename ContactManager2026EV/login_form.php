
<?php
    session_start();

    // Retrieve and clear the one-time login error message.
    $login_error = $_SESSION['login_error'] ?? '';
    unset($_SESSION['login_error']);

    // Calculate the remaining lockout time.
    $remaining = 0;

    if (isset($_SESSION['lockout_until']))
    {
        $remaining = (int) $_SESSION['lockout_until'] - time();

        if ($remaining <= 0)
        {
            $remaining = 0;
            unset($_SESSION['lockout_until']);

            // Do not display an expired lockout message.
            if (strpos($login_error, 'Account locked.') === 0)
            {
                $login_error = '';
            }
        }
    }

    $isLocked = $remaining > 0;
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Contact Manager - Login</title>

        <link rel="stylesheet" type="text/css"
              href="css/contact.css" />
    </head>

    <body>

        <?php include("header.php"); ?>

        <main>

            <h2>Login</h2>

            <?php if ($login_error !== ''): ?>
                <p id="loginError"
                   style="color: red; font-weight: bold;">
                    <?php echo htmlspecialchars($login_error); ?>
                </p>
            <?php endif; ?>

            <?php if ($isLocked): ?>

                <div id="lockoutMessage"
                     style="text-align: center; margin: 20px 0;">

                    <p style="color: red; font-weight: bold;">
                        Your account is temporarily locked.
                    </p>

                    <p>
                        You can try logging in again in:
                    </p>

                    <p id="countdown"
                       data-remaining="<?php echo $remaining; ?>"
                       style="font-size: 2em; font-weight: bold;">
                        <?php
                            printf(
                                "%02d:%02d",
                                floor($remaining / 60),
                                $remaining % 60
                            );
                        ?>
                    </p>

                </div>

            <?php endif; ?>

            <form action="login.php" method="post" id="login_form">

                <div id="data">

                    <label for="user_name">Username:</label>
                    <input type="text"
                           id="user_name"
                           name="user_name"
                           autocomplete="username"
                           required />
                    <br />

                    <label for="password">Password:</label>
                    <input type="password"
                           id="password"
                           name="password"
                           autocomplete="current-password"
                           required />
                    <br />

                </div>

                <div id="buttons">

                    <label>&nbsp;</label>
                    <input type="submit" value="Login" />
                    <br />

                </div>

            </form>

            <p>
                <a href="register_user_form.php">Register</a>
            </p>

        </main>

        <?php include("footer.php"); ?>

        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const countdown = document.getElementById('countdown');

                if (!countdown) {
                    return;
                }

                let remaining = parseInt(
                    countdown.dataset.remaining,
                    10
                );

                const loginError = document.getElementById('loginError');
                const lockoutMessage =
                    document.getElementById('lockoutMessage');

                function updateCountdown() {

                    const minutes = Math.floor(remaining / 60);
                    const seconds = remaining % 60;

                    countdown.textContent =
                        String(minutes).padStart(2, '0') + ':' +
                        String(seconds).padStart(2, '0');

                    if (remaining <= 0) {

                        countdown.textContent = '00:00';

                        if (lockoutMessage) {
                            lockoutMessage.innerHTML =
                                '<p style="color: green; font-weight: bold;">' +
                                'The lockout period has ended. ' +
                                'You may try logging in again.' +
                                '</p>';
                        }

                        if (loginError &&
                            loginError.textContent.trim().startsWith(
                                'Account locked.'
                            )) {
                            loginError.remove();
                        }

                        clearInterval(timer);
                        return;
                    }

                    remaining--;
                }

                updateCountdown();

                const timer = setInterval(updateCountdown, 1000);

            });
        </script>

    </body>

</html>
