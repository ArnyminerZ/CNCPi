<?php

class MyDB extends SQLite3
{
    function __construct()
    {
        $this->open('settings.db');
    }
}

$n = "<br/>";

$db = new MyDB();
if (!$db) {
    // echo $db->lastErrorMsg();
    echo "Cannot open database$n";
} else {
    echo "Opened database successfully$n";
}

$sql = <<<EOF
      CREATE TABLE IF NOT EXISTS SETTINGS
      (
      NAME  TEXT UNIQUE NOT NULL,
      VALUE TEXT UNIQUE NOT NULL);
EOF;

$ret = $db->exec($sql);
if (!$ret) {
    echo $db->lastErrorMsg();
} else {
    echo "Table created successfully$n";
}

if (isset($_GET["language"])) {
    $language = $_GET['language'];
    $sql = <<<EOF
      INSERT OR REPLACE INTO SETTINGS (NAME, VALUE) VALUES ("language", "$language");
EOF;
    $ret = $db->exec($sql);
    if (!$ret) {
        echo $db->lastErrorMsg();
    } else {
        echo "Language updated correctly$n";
    }
}
if (isset($_GET["maxFileSize"])) {
    $maxFileSize = $_GET['maxFileSize'];
    $sql = <<<EOF
      INSERT OR REPLACE INTO SETTINGS (NAME, VALUE) VALUES ("maxFileSize", "$maxFileSize");
EOF;
    $ret = $db->exec($sql);
    if (!$ret) {
        echo $db->lastErrorMsg();
    } else {
        echo "maxFileSize updated correctly$n";
    }
}
if (isset($_GET["clickSound"])) {
    $clickSound = $_GET['clickSound'];
    $sql = <<<EOF
      INSERT OR REPLACE INTO SETTINGS (NAME, VALUE) VALUES ("clickSound", "$clickSound");
EOF;
    $ret = $db->exec($sql);
    if (!$ret) {
        echo $db->lastErrorMsg();
    } else {
        echo "clickSound updated correctly$n";
    }
}
if (isset($_GET["terminalLog"])) {
    $terminalLog = $_GET['terminalLog'];

    $sql = <<<EOF
      SELECT * from SETTINGS;
EOF;

    $oldTerminalLog = "";
    $ret = $db->query($sql);
    while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
        if($row['NAME'] == "terminalLog")
            $oldTerminalLog = $row["VALUE"];
    }
    $oldTerminalLog .= "\n$ ";
    $oldTerminalLog .= $terminalLog;

    $sql = <<<EOF
      INSERT OR REPLACE INTO SETTINGS (NAME, VALUE) VALUES ("terminalLog", "$oldTerminalLog");
EOF;
    $ret = $db->exec($sql);
    if (!$ret) {
        echo $db->lastErrorMsg();
    } else {
        echo "terminalLog updated correctly$n";
    }
}

if(!isset($_GET["language"]) &&
   !isset($_GET["maxFileSize"]) &&
    !isset($_GET["terminalLog"])){
    $sql = <<<EOF
      SELECT * from SETTINGS;
EOF;

    $ret = $db->query($sql);
    while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
        echo "NAME = " . $row['NAME'] . "$n";
        echo "VALUE = " . $row['VALUE'] . "$n$n";
    }
}

$db->close();

if (isset($_GET["returnTo"])) {
    header("Location: " . $_GET["returnTo"]);
    die();
}