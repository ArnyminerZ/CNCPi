<?php
session_start();

// Operation codes:
// o = MKDIR     -> d = PATH
//     UPL_FILE  -> [file in $_POST]
if (isset($_GET["o"])) {
    switch ($_GET["o"]) {
        case "MKDIR":
            echo "Making dir " . $_GET["d"];
            echo mkdir("cloud/" . $_GET["d"]);
            break;
        case "UPL_FILE":
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
            $uploadOk = 1;
            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
            // Check if file already exists
            if (file_exists($target_file)) {
                echo "Sorry, file already exists.";
                $uploadOk = 0;
            }
            // Check file size
            if (isset($_COOKIE["pref_maxFileSize"]))
                if ($_COOKIE["pref_maxFileSize"] != "0")
                    if ($_FILES["fileToUpload"]["size"] > 500000) {
                        echo "Sorry, your file is too large.";
                        $uploadOk = 0;
                    }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                echo "Sorry, your file was not uploaded.";
                // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    echo "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
            break;
    }

    if(isset($_GET["tab"])){
        $_SESSION["tab"] = $_GET["tab"];
    }
    if(isset($_POST["tab"])){
        $_SESSION["tab"] = $_GET["tab"];
    }

    if (isset($_POST["returnTo"])) {
        header("Location: " . $_POST["returnTo"]);
        die();
    } else if (isset($_GET["returnTo"])) {
        header("Location: " . $_GET["returnTo"]);
        die();
    }
}