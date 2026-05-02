<?php

session_start();

function lireUsers(){
    return [
        [
            "email" => "admin@gmail.com",
            "password" => "admin123",
            "role" => "admin"
        ],
        [
            "email" => "enseignant@gmail.com",
            "password" => "user123",
            "role" => "enseignant"
        ]
    ];
}

?>

