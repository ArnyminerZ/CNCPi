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

if (isset($_GET["language"]) ||
    isset($_GET["maxFileSize"])) {

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

    $sql = <<<EOF
      SELECT * from SETTINGS;
EOF;

    $ret = $db->query($sql);
    while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
        echo "NAME = " . $row['NAME'] . "$n";
        echo "VALUE = " . $row['VALUE'] . "$n$n";
    }
} else {
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

if(isset($_GET["returnTo"])){
    header("Location: " . $_GET["returnTo"]);
    die();
}