<?php
session_start();

if (isset($_GET["o"])) {
    switch ($_GET["o"]) {
        // ?s as the session name
        // ?v as the value to set
        case "SESSION":
            $_SESSION[$_GET["s"]] = $_GET["v"];

            break;
    }

    if(isset($_GET["returnTo"])){
        header("Location: " . $_GET["returnTo"]);
        die();
    }
}