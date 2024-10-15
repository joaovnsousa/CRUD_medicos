<?php
    define('HOST', '127.0.0.1:3306');
    define('USER', 'root');
    define('PASS', '47Lasanha*');
    define('BASE', 'crud_medico');

    $conn = new mysqli(HOST, USER, PASS, BASE);

    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    ?>