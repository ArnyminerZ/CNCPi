<?php
session_start();

function Delete($path)
{
    if (is_dir($path) === true) {
        $files = array_diff(scandir($path), array('.', '..'));

        foreach ($files as $file) {
            Delete(realpath($path) . '/' . $file);
        }

        return rmdir($path);
    } else if (is_file($path) === true) {
        return unlink($path);
    }

    return false;
}

function Compress($path, $resultFile)
{
    // Get real path for our folder
    $rootPath = realpath($path);

// Initialize archive object
    $zip = new ZipArchive();
    $zip->open($resultFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

// Create recursive directory iterator
    /** @var SplFileInfo[] $files */
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($rootPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        // Skip directories (they would be added automatically)
        if (!$file->isDir()) {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($rootPath) + 1);

            // Add current file to archive
            $zip->addFile($filePath, $relativePath);
        }
    }

// Zip archive will be created only after closing object
    $zip->close();
}

// Operation codes:
// o = MKDIR     -> d = PATH
//     UPL_FILE  -> [file in $_FILES]
if (isset($_GET["o"])) {
    switch ($_GET["o"]) {
        // ?d as directory to make
        case "MKDIR":
            // Check if dir doesn't exist
            if (!file_exists("cloud/" . $_GET["d"]) && !is_dir("cloud/" . $_GET["d"])) {
                echo "Making dir " . $_GET["d"];
                if (!mkdir("cloud/" . $_GET["d"])) {
                    die();
                }
            }
            break;
        // ?d as directory to remove
        case "RMDIR":
            if (is_dir("cloud/" . $_GET["d"])) {
                echo "Removing dir \"" . $_GET["d"] . "\"<br/>";
                Delete("cloud/" . $_GET["d"]);
                if (file_exists("cloud/" . $_GET["d"])) {
                    die();
                }
            }
            break;
        // ?f as the file to remove
        case "RM":
            if (file_exists("cloud/" . $_GET["f"]) && !is_dir("cloud/" . $_GET["f"])) {
                echo "Removing file \"" . $_GET["f"] . "\"";
                if (!unlink("cloud/" . $_GET["f"])) {
                    die();
                }
            }
            break;
        // ?d as directory to rename
        // ?n as the new name
        case "RNDIR":
            if (is_dir("cloud/" . $_GET["d"])) {
                echo "Renaming dir " . $_GET["d"] . " to " . $_GET["n"];
                if (!rename("cloud/" . $_GET["d"], "cloud/" . $_GET["n"])) {
                    die();
                }
            }
            break;
        // ?f as the file to rename
        // ?n as the new name
        case "RN":
            if (file_exists("cloud/" . $_GET["f"]) && !is_dir("cloud/" . $_GET["f"])) {
                echo "Renaming file " . $_GET["f"] . " to " . $_GET["n"];
                if (!rename("cloud/" . $_GET["f"], "cloud/" . $_GET["n"])) {
                    die();
                }
            }
            break;
        // ?c as the file path
        // ?r as the result file
        case "COMPRESS":
            echo "Compressing " . $_GET["c"];
            Compress("cloud/" . $_GET["c"], "cloud/" . $_GET["r"]);
            if (file_exists("cloud/" . $_GET["r"]))
                header("Location: " . $_GET["r"]);
            else
                die("An error occurred while compressing");
            break;
        case "UPL_FILE":
            $target_dir = "cloud/";
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
                die("Sorry, your file was not uploaded.");
                // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    echo "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
                } else {
                    die("Sorry, there was an error uploading your file.");
                }
            }
            break;
        default:
            echo "Operation not detected";
            break;
    }

    if (isset($_GET["tab"])) {
        echo "<script>localStorage.setItem('tab', '" . $_GET["tab"] . "');</script>";
    }
    if (isset($_POST["tab"])) {
        echo "<script>localStorage.setItem('tab', '" . $_POST["tab"] . "');</script>";
    }

    if (isset($_POST["returnTo"])) {
        header("Location: " . $_POST["returnTo"]);
        die();
    } else if (isset($_GET["returnTo"])) {
        header("Location: " . $_GET["returnTo"]);
        die();
    }
}
