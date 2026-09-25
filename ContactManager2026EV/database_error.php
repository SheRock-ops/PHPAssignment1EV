<?php
    session_start();
?>

<!DOCTYPE html>

<html>

  <head>

    <title>Contact Manager - Database Error</title>

  </head>

  <body>

    <h1>Contact Manager System</h1>

    <main>

        <h2>Database Error</h2>
        <p> There was an error connecting to the database.</p>
        <p> The database must be installed.</p>
        <p> MySQL must be running.</p>
        <p> Error Message: <?php echo isset($_SESSION["database_error"]) ? htmlspecialchars($_SESSION["database_error"]) : "Unknown database error."; ?></p>

        <p><a href="index.php">View Contact</a></p>

    </main>

    <h2>Copyright &copy; 2026 - All Rights Reserved</h2>
    
  </body>

</html>