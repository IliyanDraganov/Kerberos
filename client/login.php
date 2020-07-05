<!DOCTYPE html>

<head>
    <meta charset='utf-8'>
    <link rel="stylesheet" type="text/css" href="../KDC/style.css">
    <title>Server</title>
</head>

<body>
    <form action="transactions.php" method="post">
        <div class="container">
            <h1>Log in</h1>
            <hr>

            <label for="username"><b>Username</b></label>
            <input type="text" placeholder="Enter Username" name="username" id="username" required>

            <label for="psw"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="psw" id="psw" oninput="validateForm()" required>

            <button type="submit" class="registerbtn" id="sbm-btn">Log in</button>
        </div>
    </form>
</body>

</html>
