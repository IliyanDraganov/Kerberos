<?php

function getSecret($table, $name)
{
    $connection = @mysqli_connect("localhost", "iliyan", "pwd123", "principal_details");

    if (!$connection) {
        http_response_code(500);
        echo "Cannot connect to database. Try again later.";
        exit;
    }

    $sql_get_users_with_name = "SELECT * 
        FROM " . $table . "
        WHERE name = '" . mysqli_real_escape_string($connection, $name) . "'";

    $result = mysqli_query($connection, $sql_get_users_with_name);

    if (!$result) {
        http_response_code(500);
        echo "Database maintenance. Try again later.";
        exit;
    }

    if (mysqli_num_rows($result) == 0) {
        return null;
    } else {
        return mysqli_fetch_array($result)['secret'];
    }
}
