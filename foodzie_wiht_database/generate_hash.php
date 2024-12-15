<?php
// Generate hashed password for the admin account
$hashed_password = password_hash('AdminPao', PASSWORD_BCRYPT);

// Output the hashed password to copy it
echo $hashed_password;
?>
