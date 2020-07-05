<?php
function createSecret($psw, $username)
{
    $secret = hash("sha256", $psw.$username);

    return $secret;
}

function createPrincipal($username, $secret)
{
    $connection = @mysqli_connect("localhost", "iliyan", "pwd123", "principal_details");

    if (!$connection) {
        echo "Cannot connect to database. Try again later.";
        exit;
    }

    $sql_get_users_with_username = "SELECT * 
        FROM credentials 
        WHERE username = '" . mysqli_real_escape_string($connection, $username) . "'";

    $result = mysqli_query($connection, $sql_get_users_with_username);

    if (!$result) {
        echo "Database maintenance. Try again later.";
        exit;
    }

    if (mysqli_num_rows($result) == 0) {
        $sql_insert_user = "INSERT INTO `credentials`(`username`, `secret`) 
            VALUES ('" . mysqli_real_escape_string($connection, $username) . "','" . $secret . "')";

        $result = mysqli_query($connection, $sql_insert_user);

        if (!$result) {
            echo "Database maintenance. Try again later.";
            exit;
        }

        echo "<p style=\"color:green;\">Registration was successful.</p>";
    } else {
        echo "<p style=\"color:red;\">Username is already taken.</p>";
    }
}

if (isset($_POST['psw']) && isset($_POST['username'])) {
    createPrincipal($_POST['username'], createSecret($_POST['psw'], $_POST['username']));
}
?>

<!DOCTYPE html>

<head>
    <meta charset='utf-8'>
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Server</title>
</head>

<script>
function validateForm() {
    const psw = document.getElementById('psw');
    const pswRep = document.getElementById('psw-repeat');
    const btn = document.getElementById('sbm-btn');

    if (psw.value !== pswRep.value) {
        btn.disabled = true;
    } else {
        btn.disabled = false;
    }

}
</script>

<body>
    <form action="register.php" method="post">
        <div class="container">
            <h1>Register</h1>
            <p>Please fill in this form to create a principal.</p>
            <hr>

            <label for="username"><b>Username</b></label>
            <input type="text" placeholder="Enter Username" name="username" id="username" required>

            <label for="psw"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="psw" id="psw" oninput="validateForm()" required>

            <label for="psw-repeat"><b>Repeat Password</b></label>
            <input type="password" placeholder="Repeat Password" name="psw-repeat" id="psw-repeat"
                oninput="validateForm()" required>
            <hr>

            <button type="submit" class="registerbtn" id="sbm-btn" disabled>Register</button>
        </div>
    </form>
</body>

</html>
