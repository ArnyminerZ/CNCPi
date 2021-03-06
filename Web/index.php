<!DOCTYPE html>
<?php
session_start();

class MyDB extends SQLite3
{
    function __construct()
    {
        $settingsDatabase = "settings.db";

        if(file_exists("settingsLocation.txt")) {
            $myfile = fopen("settingsLocation.txt", "r") or die("Unable to open file!");
            $settingsDatabase = fread($myfile, filesize("settingsLocation.txt"));
            fclose($myfile);
        }

        echo "Opening database in " . $settingsDatabase;

        $this->open($settingsDatabase);
    }
}

$lj = "<br/>";

$db = new MyDB();
if (!$db) {
    // echo $db->lastErrorMsg();
    echo "Cannot open database$lj";
}

$language = "";
$maxFileSize = "";
$clickSound = "";
$terminalLog = "";
$settingsLocation = "";

$sql = <<<EOF
      SELECT * from SETTINGS;
EOF;
$ret = $db->query($sql);
while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
    // $row['ID']
    if ($row['NAME'] == "language")
        $language = $row['VALUE'];
    else if ($row['NAME'] == "maxFileSize")
        $maxFileSize = $row['VALUE'];
    else if ($row['NAME'] == "clickSound")
        $clickSound = $row['VALUE'];
    else if ($row['NAME'] == "settingsLocation")
        $clickSound = $row['VALUE'];
    else if ($row['NAME'] == "terminalLog")
        $terminalLog = $row['VALUE'];
}
if ($language == "")
    $language = "en";
if ($maxFileSize == "")
    $maxFileSize = "0";
if ($terminalLog == "")
    $terminalLog = "";
if ($settingsLocation == "")
    $settingsLocation = "settings.db";

echo "<script>console.log('language=$language');console.log('maxFileSize=$maxFileSize');console.log('clickSound=$clickSound');console.log('settingsLocation=$settingsLocation');</script>";

if (!isset($_COOKIE["pref_maxFileSize"])) {
    // Set 0 for unlimited
    setcookie("pref_maxFileSize", "0", time() + (86400 * 30), "/");
}

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

include_once "lang/en.php";
?>
<html>
<head>
    <!--Import Google Icon Font-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="css/materialdesignicons.min.css" rel="stylesheet">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection"/>

    <link href="css/flag-icon.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet">

    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title><?php echo _TITLE; ?></title>

    <style>
        .bottom-navbar {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: 998;
        }

        .settings-tabcontrol {
            padding: 0;
        }

        .settings-tabcontrol .header {
            padding: 0;
        }

        .settings-tabcontrol .header .collection {
            margin: 0;
        }

        .settings-tabcontrol .header .collection .collection-item {
            cursor: pointer;
        }
    </style>
    <!-- Direction Buttons Style -->
    <style>
        .positionCameras {
            width: 125px;
            padding: 5px;
        }

        .positionCameras ul, .positionCameras li {
            margin: 0;
            padding: 0;
            list-style: none;
            display: inline-block;
            vertical-align: top;
        }

        .positionCameras li.cameraIcon {
            width: 25px;
            height: 25px;
            cursor: pointer;
            background: #cccccc;
            margin: 5px;
            border-radius: 5px;
            border: 1px solid #aaaaaa;
            box-shadow: 1px 2px 15px #cccccc;
            text-align: center;
            line-height: 20px;
        }

        .positionCameras li.cameraIcon:before {
            content: "";
            display: inline-block;
            width: 0;
            height: 0;
            vertical-align: middle;
            /*border-style: solid;
            border-width: 0 5px 10px 5px;
            border-color: transparent transparent #007bff transparent;*/
        }

        .cameraIcon:before {
            content: '';
            display: inline-block;
            vertical-align: middle;
            height: 10px;
            width: 10px;
            border-radius: 100%;
            box-shadow: 0 0 0 3px gray, inset -1px -1px 0 3px black;
        }

        .moveUpDownBox {
            text-align: center;
        }

        .movement-icon {
            font-size: 21px;
        }
    </style>
</head>
<body>
<script>
    var shouldSendUpdateNotification = false;

    function playClick() {
        new Audio('src/sound/click.wav').play();
    }
</script>
<nav>
    <div class="nav-wrapper teal darken-1" id="top-navbar">
        <a href="#!" class="brand-logo"><?php echo _TITLE; ?></a>
    </div>
</nav>
<div class="bottom-navbar">
    <nav>
        <div class="nav-wrapper teal darken-1">
            <ul class="row">
                <li class="col s3" style="text-align: center;" onclick="clickTabSelection(0)">
                    <a  id="home-tab-selector" class="grey-text text-lighten-3 tooltipped" data-position="bottom"
                       data-delay="50" data-tooltip="<?php echo _HOME; ?>">
                        <i class="material-icons">home</i>
                    </a>
                </li>
                <li class="col s2" style="text-align: center;" onclick="clickTabSelection(1)">
                    <a class="grey-text text-lighten-3 tooltipped" data-position="top"
                       data-delay="50"
                       data-tooltip="<?php echo _CLOUD; ?>">
                        <i class="material-icons">cloud</i>
                    </a>
                </li>
                <li class="col s2" style="text-align: center;">
                    <a class="grey-text text-lighten-3 tooltipped modal-trigger"
                       data-position="top" data-delay="50"
                       href="#messages-modal"
                       data-tooltip="<?php echo _MESSAGES; ?>">
                        <i id="messages-icon" class="material-icons">message</i>
                        <span class="new badge" data-badge-caption="<?php echo _NOTIFICATION_NEW; ?>"
                              id="notificationCounterBadge" style="display: none"></span>
                    </a>
                </li>
                <li class="col s2" style="text-align: center;" onclick="clickTabSelection(3)">
                    <a class="grey-text text-lighten-3 tooltipped" data-position="top"
                       data-delay="50"
                       data-tooltip="<?php echo _TERMINAL; ?>">
                        <i class="mdi mdi-console"></i>
                    </a>
                </li>
                <li class="col s3" style="text-align: center;" onclick="clickTabSelection(2)">
                    <a class="grey-text text-lighten-3 tooltipped" data-position="top"
                       data-delay="50"
                       data-tooltip="<?php echo _SETTINGS; ?>">
                        <i class="material-icons">settings</i>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</div>

<div id="loadingSpinner" style="display: none;position:fixed;top: 50%;left: 50%;">
    <div class="preloader-wrapper big active">
        <div class="spinner-layer spinner-blue-only">
            <div class="circle-clipper left">
                <div class="circle"></div>
            </div>
            <div class="gap-patch">
                <div class="circle"></div>
            </div>
            <div class="circle-clipper right">
                <div class="circle"></div>
            </div>
        </div>
    </div>
</div>

<!-- Add Machine Modal -->
<div id="add-machine-modal" class="modal">
    <div class="modal-content">
        <h4><?php echo _ADD_MACHINE; ?></h4>
        <div class="row">
            <form class="col s12">
                <div class="row">
                    <div class="input-field col s12">
                        <input id="machine_name" type="text" class="validate">
                        <label for="machine_name"><?php echo _MACHINE_NAME; ?></label>
                    </div>
                </div>
                <div class="input-field col s12">
                    <select>
                        <option value="" disabled selected><?php echo _CHOOSE_PORT; ?></option>
                        <option value="1">COM1</option>
                        <option value="2">COM2</option>
                        <option value="3">COM3</option>
                    </select>
                    <label><?php echo _PORT; ?></label>
                </div>
            </form>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat"><?php echo _CONNECT; ?></a>
    </div>
</div>
<div id="messages-modal" class="modal bottom-sheet">
    <div class="modal-content">
        <h4><?php echo _MESSAGES ?></h4>
        <ul class="collection" id="messages-container">
            <!-- Messages Loader
                 Message:    '<li class="collection-item"><div>Message Content<a href="#!" class="secondary-content"><i class="material-icons">delete</i></a></div></li>'
                 Anymessage: 'Any Message' -->
        </ul>
        <p id="anymessage-label"><?php echo _ANY_MESSAGE ?></p>
    </div>
</div>
<ul id='terminalDropdown' class='dropdown-content'>
    <li><a href="#!"><?php echo _CLEAR; ?></a></li>
    <li>
        <button class="copyTerminal" data-clipboard-target="#terminalContent"><?php echo _COPY; ?></button>
    </li>
</ul>

<div id="uploadFileModal" class="modal">
    <form action="fileSystem.php?o=UPL_FILE" method="post" id="uploadFileModalForm" enctype="multipart/form-data">
        <input type="text" name="returnTo"
               value="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>"
               style="display: none"/>
        <div class="modal-content">
            <h4><?php echo _UPLOAD_FILE; ?></h4>
            <div class="file-field input-field">
                <div class="btn">
                    <span><?php echo _FILE; ?></span>
                    <input type="file" name="fileToUpload" id="fileToUpload" required>
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit"
                    class="modal-action waves-effect waves-green btn-flat"><?php echo _UPLOAD; ?></button>
        </div>
    </form>
</div>
<div id="createFolder" class="modal">
    <form action="fileSystem.php" method="get" id="createFolderModalForm">
        <input type="text" name="o" value="MKDIR" style="display: none"/>
        <input type="text" name="tab" value="1" style="display: none"/>
        <input type="text" name="returnTo"
               value="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>"
               style="display: none"/>
        <div class="modal-content">
            <h4><?php echo _CREATE_FOLDER; ?></h4>
            <div class="row">
                <div class="input-field col s6">
                    <input id="folder_name_modal_txt" name="d" type="text" class="validate" autocomplete="off" required>
                    <label for="folder_name_modal_txt"><?php echo _FOLDER_NAME; ?></label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit"
                    class="modal-action waves-effect waves-green btn-flat"><?php echo _CREATE; ?></button>
        </div>
    </form>
</div>
<div id="renameFolder" class="modal">
    <form action="fileSystem.php" method="get" id="renameFolderModalForm">
        <input type="text" name="o" value="RNDIR" style="display: none"/>
        <input type="text" name="tab" value="1" style="display: none"/>
        <input type="text" name="d" id="oldfoldername" style="display: none"/>
        <input type="text" name="returnTo"
               value="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>"
               style="display: none"/>
        <div class="modal-content">
            <h4><?php echo _RENAME_FOLDER; ?></h4>
            <div class="row">
                <div class="input-field col s6">
                    <input id="old_folder_name_modal_txt" type="text" class="validate" disabled>
                    <label for="old_folder_name_modal_txt"><?php echo _OLD_FOLDER_NAME; ?></label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s6">
                    <input id="new_folder_name_modal_txt" name="n" type="text" class="validate" autocomplete="off"
                           required>
                    <label for="new_folder_name_modal_txt"><?php echo _NEW_FOLDER_NAME; ?></label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit"
                    class="modal-action waves-effect waves-green btn-flat"><?php echo _RENAME; ?></button>
        </div>
    </form>
</div>
<div id="renameFile" class="modal">
    <form action="fileSystem.php" method="get" id="renameFileModalForm">
        <input type="text" name="o" value="RN" style="display: none"/>
        <input type="text" name="tab" value="1" style="display: none"/>
        <input type="text" name="f" id="oldfilename" style="display: none"/>
        <input type="text" name="returnTo"
               value="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>"
               style="display: none"/>
        <div class="modal-content">
            <h4><?php echo _RENAME_FILE; ?></h4>
            <div class="row">
                <div class="input-field col s6">
                    <input id="old_file_name_modal_txt" type="text" class="validate" disabled>
                    <label for="old_file_name_modal_txt"><?php echo _OLD_FILE_NAME; ?></label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s6">
                    <input id="new_file_name_modal_txt" name="n" type="text" class="validate" autocomplete="off"
                           required>
                    <label for="new_file_name_modal_txt"><?php echo _NEW_FILE_NAME; ?></label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit"
                    class="modal-action waves-effect waves-green btn-flat"><?php echo _RENAME; ?></button>
        </div>
    </form>
</div>
<div id="deleteFile" class="modal">
    <form action="fileSystem.php" method="get" id="deleteFileModalForm">
        <input type="text" name="o" value="RM" style="display: none"/>
        <input type="text" name="tab" value="1" style="display: none"/>
        <input type="text" name="f" id="deleteFilename" style="display: none"/>
        <input type="text" name="returnTo"
               value="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>"
               style="display: none"/>
        <div class="modal-content">
            <h4><?php echo _DELETE; ?><span></span>?</h4>
        </div>
        <div class="modal-footer">
            <button type="button"
                    class="modal-action modal-close waves-effect waves-green btn-flat"><?php echo _CANCEL; ?></button>
            <button type="submit"
                    class="modal-action modal-close waves-effect waves-green btn-flat"><?php echo _DELETE; ?></button>
        </div>
    </form>
</div>
<div id="deleteFolder" class="modal">
    <form action="fileSystem.php" method="get" id="deleteFileModalForm">
        <input type="text" name="o" value="RMDIR" style="display: none"/>
        <input type="text" name="tab" value="1" style="display: none"/>
        <input type="text" name="d" id="deleteFoldername" style="display: none"/>
        <input type="text" name="returnTo"
               value="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>"
               style="display: none"/>
        <div class="modal-content">
            <h4><?php echo _DELETE; ?><span></span>?</h4>
        </div>
        <div class="modal-footer">
            <button type="button"
                    class="modal-action modal-close waves-effect waves-green btn-flat"><?php echo _CANCEL; ?></button>
            <button type="submit"
                    class="modal-action modal-close waves-effect waves-green btn-flat"><?php echo _DELETE; ?></button>
        </div>
    </form>
</div>

<div id="home-tab" style="display: none" class="col s12">
    <div class="container">
        <div class="card-panel">
            <h1><?php echo _DASHBOARD ?>
                <a class="waves-effect waves-circle waves-light btn-floating secondary-content modal-trigger"
                   href="#add-machine-modal">
                    <i class="material-icons">add</i>
                </a>
            </h1>

            <div class="row">
                <div class="col s6 m6">
                    <div class="card">
                        <div class="card-image">
                            <!-- <img src="http://next.materializecss.com/images/sample-1.jpg"> -->
                            <div style="width: 100%; height: 250px; background-color: white"></div>
                            <span class="card-title black-text">CNC 1</span>
                        </div>
                        <hr/>
                        <div class="card-content">
                            <p>
                                <?php echo _MILLING ?>: Box Side 1
                            </p>
                            <div class="progress" style="height: 25px">
                                <div class="determinate" style="width: 27%"></div>
                                <p style="position: absolute; width: 100%; text-align: center"><?php echo str_replace("&mn", "0", str_replace("&hr", "0", str_replace("&prp", "27", _ETA_SYNTAXIS))); ?></p>
                            </div>
                        </div>
                        <div class="card-action">
                            <a style="cursor:pointer;"
                               onclick="clickTabSelection(66, 'CNC 1');"><?php echo _GO_TO_MACHINE; ?></a>
                        </div>
                    </div>
                </div>

                <div class="col s6 m6">
                    <div class="card">
                        <div class="card-image">
                            <!-- <img src="http://next.materializecss.com/images/sample-1.jpg"> -->
                            <div style="width: 100%; height: 250px; background-color: white"></div>
                            <span class="card-title black-text">CNC 2</span>
                        </div>
                        <hr/>
                        <div class="card-content">
                            <p>
                                <?php echo _MILLING ?>: <?php echo _NO ?>
                            </p>
                            <div class="progress" style="height: 25px">
                                <div class="determinate" style="width: 0%"></div>
                                <p style="position: absolute; width: 100%; text-align: center"><?php echo _NOT_MILLING; ?></p>
                            </div>
                        </div>
                        <div class="card-action">
                            <a style="cursor:pointer;"
                               onclick="clickTabSelection(66, 'CNC 2');"><?php echo _GO_TO_MACHINE; ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="cloud-tab" style="display: none" class="col s12">
    <div class="container">
        <div class="card-panel">
            <h1><?php echo _CLOUD; ?></h1>
            <!--<a class="btn-floating btn-large red modal-trigger" href="#createFolder"
               style="position:absolute;right:18%;top:160px;"
               title="<?php //echo _CREATE_FOLDER; ?>">
                <i class="large material-icons">create_new_folder</i>
            </a>
            <a class="btn-floating btn-large green modal-trigger" href="#uploadFileModal"
               style="position:absolute;right:22.5%;top:160px;"
               title="<?php //echo _UPLOAD_FILE; ?>">
                <i class="large material-icons">file_upload</i>
            </a>-->
            <div class="fixed-action-btn" style="bottom: 75px">
                <a class="btn-floating btn-large red">
                    <i class="large material-icons">mode_edit</i>
                </a>
                <ul>
                    <li><a class="btn-floating red modal-trigger" href="#createFolder"
                           title="<?php echo _CREATE_FOLDER; ?>"><i class="material-icons">create_new_folder</i></a>
                    </li>
                    <li><a class="btn-floating yellow darken-1 modal-trigger" href="#uploadFileModal"
                           title="<?php echo _UPLOAD_FILE; ?>"><i class="material-icons">file_upload</i></a></li>
                </ul>
            </div>

            <!-- TODO: Rename, Delete and Download Zip buttons -->
            <!-- TODO: Rename modal -->
            <!-- TODO: Confirm delete modal -->
            <ul class="collection">
                <?php
                $cloudDir = "cloud/";

                if (!file_exists($cloudDir)) {
                    mkdir($cloudDir, 0777, true);
                }

                $files = scandir($cloudDir);
                foreach ($files as $file) {
                    if ($file == "." || $file == "..") continue;

                    $filePath = $cloudDir . $file;
                    if (is_dir($filePath)) {
                        ?>
                        <div class="main-directory-folder" id="mainf">
                            <li class="collection-item avatar"
                                onclick="setAllCloudItemsVisibility(false);document.getElementById('<?php echo $file; ?>-folder').style.display='block';">
                                <i class="mdi mdi-folder circle yellow"></i>
                                <span class="title"><b><?php echo $file ?></b></span>
                                <p><?php echo date("d/m/Y H:i:s", filemtime($filePath)); ?><br>
                                    Folder - <?php echo formatSizeUnits(filesize($filePath)); ?>
                                </p>
                                <a onclick="document.getElementById('old_folder_name_modal_txt').value='<?php echo $file; ?>';document.getElementById('oldfoldername').value='<?php echo $file; ?>';document.getElementById('old_folder_name_modal_txt').classList.add('active');new M.Modal(document.querySelector('#renameFolder')).open();event.stopPropagation();"
                                   class="secondary-content" style="cursor:pointer;right:25px;"
                                   title="<?php echo _RENAME; ?>"><i
                                            class="mdi mdi-pencil mdi-24px"></i></a>
                                <a onclick="document.getElementById('deleteFoldername').value='<?php echo $file; ?>';new M.Modal(document.querySelector('#deleteFolder')).open();event.stopPropagation();"
                                   class="secondary-content" style="cursor:pointer;right:55px;"
                                   title="<?php echo _DELETE_FOREVER; ?>"><i
                                            class="mdi mdi-delete-forever mdi-24px"></i></a>
                                <a onclick="event.stopPropagation();"
                                   href="fileSystem.php?o=COMPRESS&c=<?php echo $file; ?>&r=<?php echo $file . ".zip"; ?>"
                                   class="secondary-content" style="cursor:pointer;right:85px;"
                                   title="<?php echo _DOWNLOAD_ZIP; ?>"><i
                                            class="mdi mdi-download mdi-24px"></i></a>
                            </li>
                        </div>
                        <div class="folder-contents" id="<?php echo $file; ?>-folder" style="display:none;">
                            <li class="collection-item avatar"
                                onclick="setAllCloudItemsVisibility(true);document.getElementById('<?php echo $file; ?>-folder').style.display='none';">
                                <i class="mdi mdi-dots-horizontal circle"></i>
                                <span class="title"><b></b></span>
                            </li>
                            <?php
                            foreach (scandir($filePath) as $subfile) {
                                if ($subfile == "." || $subfile == "..") continue;

                                $subfilePath = $filePath . $subfile;
                                if (is_dir($subfile)) {
                                    echo _CANNOT_SUBFOLDER;
                                } else {
                                    ?>
                                    <li class="collection-item avatar">
                                        <i class="mdi mdi-code-braces circle green"></i>
                                        <span class="title"><b><?php echo $subfile; ?></b></span>
                                        <p><?php echo date("d/m/Y H:i:s", filemtime($subfilePath)); ?><br>
                                            <?php echo end(explode('.', $subfile)); ?>
                                            - <?php echo formatSizeUnits(filesize($subfilePath)); ?>
                                        </p>
                                        <a onclick="document.getElementById('old_file_name_modal_txt').value='<?php echo $file; ?>';document.getElementById('new_file_name_modal_txt').value='<?php echo $file; ?>';document.getElementById('oldfilename').value='<?php echo $file; ?>';document.getElementById('old_file_name_modal_txt').classList.add('active');new M.Modal(document.querySelector('#renameFile')).open();event.stopPropagation();"
                                           class="secondary-content" style="right:25px;"
                                           title="<?php echo _RENAME_FILE; ?>"><i
                                                    class="mdi mdi-pencil mdi-24px"></i></a>
                                        <a onclick="document.getElementById('deleteFilename').value='<?php echo $subfile; ?>';new M.Modal(document.querySelector('#deleteFile')).open();event.stopPropagation();"
                                           class="secondary-content" style="right:55px;"
                                           title="<?php echo _DELETE_FOREVER; ?>"><i
                                                    class="mdi mdi-delete-forever mdi-24px"></i></a>
                                        <a onclick="event.stopPropagation();" href="<?php echo $subfilePath; ?>"
                                           class="secondary-content"
                                           style="right:85px;"
                                           title="<?php echo _DOWNLOAD_FILE; ?>" download><i
                                                    class="mdi mdi-download mdi-24px"></i></a>
                                        <a onclick="event.preventDefault();" href="#!" class="secondary-content"
                                           style="right:115px;"
                                           title="<?php echo _LOAD_FILE; ?>"><i
                                                    class="mdi mdi-upload-network mdi-24px"></i></a>
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="main-directory-folder" id="mainf">
                            <li class="collection-item avatar">
                                <i class="mdi mdi-code-braces circle green"></i>
                                <span class="title"><b><?php echo $file; ?></b></span>
                                <p><?php echo date("d/m/Y H:i:s", filemtime($filePath)); ?><br>
                                    <?php echo end(explode('.', $file)); ?>
                                    - <?php echo formatSizeUnits(filesize($filePath)); ?>
                                </p>
                                <a onclick="document.getElementById('old_file_name_modal_txt').value='<?php echo $file; ?>';document.getElementById('new_file_name_modal_txt').value='<?php echo $file; ?>';document.getElementById('oldfilename').value='<?php echo $file; ?>';document.getElementById('old_file_name_modal_txt').classList.add('active');new M.Modal(document.querySelector('#renameFile')).open();"
                                   class="secondary-content" style="right:25px;"
                                   title="<?php echo _RENAME_FILE; ?>"><i
                                            class="mdi mdi-pencil mdi-24px"></i></a>
                                <a onclick="document.getElementById('deleteFilename').value='<?php echo $file; ?>';new M.Modal(document.querySelector('#deleteFile')).open();"
                                   class="secondary-content" style="right:55px;"
                                   title="<?php echo _DELETE_FOREVER; ?>"><i
                                            class="mdi mdi-delete-forever mdi-24px"></i></a>
                                <a href="<?php echo $filePath; ?>" class="secondary-content" style="right:85px;"
                                   title="<?php echo _DOWNLOAD_FILE; ?>" download><i
                                            class="mdi mdi-download mdi-24px"></i></a>
                                <a href="#!" class="secondary-content" style="right:115px;"
                                   title="<?php echo _LOAD_FILE; ?>"><i
                                            class="mdi mdi-upload-network mdi-24px"></i></a>
                            </li>
                        </div>
                        <?php
                    }
                }
                ?>
            </ul>
        </div>
    </div>
</div>
<div id="terminal-tab" style="display: none" class="col s12">
    <div class="container">
        <div class="card-panel">
            <h1><?php echo _TERMINAL ?></h1>

            <div class="row">
                <form class="col s12" method="get" action="runCommand.php">
                    <input type="text" name="returnTo"
                           value="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>"
                           style="display: none"/>
                    <?php
                    $terminalLog = str_replace("\n", "<br/>", $terminalLog);
                    ?>
                    <div class="row">
                        <div class="input-field col s12">
                            <div
                                    id="terminalContent"
                                    style="height: 250px; resize: none; padding-left: 10px; padding-top: 15px; font-size: large; border: none; cursor: default"><?php echo $terminalLog; ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s11">
                            <i class="material-icons prefix">attach_money</i>
                            <input id="command" name="c" type="text" class="validate">
                            <label for="command"><?php _COMMAND; ?></label>
                        </div>
                        <div class="input-field col s1">
                            <button type="submit" class="waves-effect waves-teal btn-flat"><i class="material-icons">send</i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="settings-tab" style="display: none" class="col s12">
    <div class="container">
        <div class="card-panel">
            <h1><?php echo _SETTINGS; ?></h1>
            <div class="card-panel row settings-tabcontrol">
                <div class="col s3 header">
                    <div class="collection">
                        <a onclick="clickSettingsSectionSelection(0);" id="generalSelectorS"
                           class="collection-item"><?php echo _GENERAL; ?></a>
                        <a onclick="clickSettingsSectionSelection(3);" id="interfaceSelectorS"
                           class="collection-item"><?php echo _INTERFACE; ?></a>
                        <a onclick="clickSettingsSectionSelection(2);" id="cloudSelectorS"
                           class="collection-item"><?php echo _CLOUD; ?></a>
                        <a onclick="clickSettingsSectionSelection(4);" id="advancedSelectorS"
                           class="collection-item"><?php echo _ADVANCED_SETTINGS; ?></a>
                        <a onclick="clickSettingsSectionSelection(1)" id="aboutSelectorS"
                           class="collection-item"><?php echo _ABOUT; ?></a>
                    </div>
                </div>
                <div class="col s9 body">
                    <div id="s-general">
                        <br/>
                        <div class="input-field col s12">
                            <select id="languageSelector">
                                <option value="" disabled><?php echo _CHOOSE_LANGUAGE; ?></option>
                                <option value="en" <?php if ($language == "en") {
                                    echo "selected";
                                } ?>>English
                                </option>
                            </select>
                            <label><?php echo _LANGUAGE; ?></label>
                        </div>
                        <button type="button" class="waves-effect waves-light btn"
                                onclick="saveSettings()"><?php echo _SAVE; ?></button>
                        <br/>
                        <br/>
                    </div>
                    <div id="s-interface">
                        <p>
                            <label>
                                <input type="checkbox" class="filled-in"
                                       id="clickSoundSelector" <?php if ($clickSound == "true" || $clickSound == "") {
                                    echo "checked";
                                } ?> />
                                <span><?php echo _CLICK_SOUND; ?></span>
                            </label>
                        </p>
                        <button type="button" class="waves-effect waves-light btn"
                                onclick="saveSettings()"><?php echo _SAVE; ?></button>
                        <br/>
                        <br/>
                    </div>
                    <div id="s-cloud">
                        <p><?php echo _SET_0_FOR_UNLIMITED; ?></p>
                        <div class="input-field col s12">
                            <input value="<?php echo $maxFileSize; ?>" id="maxFileSize" type="number"
                                   class="validate">
                            <label for="maxFileSize"><?php echo _MAX_FILE_SIZE; ?></label>
                        </div>
                        <button type="button" class="waves-effect waves-light btn"
                                onclick="saveSettings()"><?php echo _SAVE; ?></button>
                        <br/>
                        <br/>
                    </div>
                    <div id="s-advanced">
                        <br/>
                        <div class="input-field col s12">
                            <input id="settings_db_loc" type="text" class="validate" value="<?php echo $settingsLocation; ?>">
                            <label for="settings_db_loc"><?php echo _SETTINGS_DB_LOC; ?></label>
                        </div>
                        <button type="button" class="waves-effect waves-light btn"
                                onclick="saveSettings()"><?php echo _SAVE; ?></button>
                        <br/>
                        <br/>
                    </div>
                    <div id="s-about">
                        <!-- TODO: Update button still not working -->
                        <?php
                        $githubVersion = file_get_contents('https://raw.githubusercontent.com/ArnyminerZ/CNCPi/master/Web/VERSION');

                        $myfile = fopen("VERSION", "r") or die(_VERSION_LOAD_ERROR);
                        $localVersion = fread($myfile, filesize("VERSION"));
                        fclose($myfile);

                        $currentPHPVersion = phpversion();
                        ?>
                        <p>
                        <h4><?php echo _SOFTWARE_VERSIONS; ?></h4>
                        <?php
                        if ($githubVersion == $localVersion) {
                            echo '<h6><span style="color:#8bc34a"><i class="material-icons">info_outline</i>' . _SYSTEM_UPTODATE . '</span></h6>';
                        } else {
                            echo '<h6><span style="color:#f44336"><i class="material-icons">info_outline</i>' . _UPDATE_AVAILABLE . '</span> <a id="updateLink" style="cursor: pointer">' . _UPDATE . '</a></h6>';
                            echo "<script>shouldSendUpdateNotification = true;</script>";
                        }
                        ?>
                        <?php echo _CNCPI_RELEASE_VERSION . ": ";
                        echo "<i>" . $localVersion . "</i><br/>";
                        echo _CURRENT_PHP_VERSION . ': <i>' . $currentPHPVersion . '</i><br/>';

                        ob_start();
                        phpinfo(INFO_MODULES);
                        $info = ob_get_contents();
                        ob_end_clean();
                        $info = stristr($info, 'Client API version');
                        preg_match('/[1-9].[0-9].[1-9][0-9]/', $info, $match);
                        $gd = $match[0];
                        echo _MYSQL_VERSION . ':  <i>' . $gd . '<i><br />';
                        ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="machine-tab" class="col s12" style="display:none;">
    <div class="container">
        <div class="card-panel">
            <h1 id="machine-title"><?php echo _UNKNOWN_CNC; ?></h1>

            <div class="row">
                <div class="col s12">
                    <ul class="tabs tabs-fixed-width">
                        <li class="tab col s3"><a href="#milling" class="active"><?php echo _MILLING; ?></a></li>
                        <li class="tab col s3"><a href="#control"><?php echo _CONTROL; ?></a></li>
                        <li class="tab col s3"><a href="#terminal"><?php echo _TERMINAL; ?></a></li>
                    </ul>
                </div>
                <div id="milling" class="col s12">
                    <div class="row">
                        <div class="col s4">
                            <div class="card-panel">
                                <h6>
                                    <?php echo str_replace("&f", _NOT_MILLING, _PRINTING_FILE); ?><br/>
                                    <?php echo str_replace("&h", "1", str_replace("&m", "17", str_replace("&s", "53", _ETA_MILLING))); ?><br/>
                                </h6>
                            </div>
                        </div>
                        <div class="col s8">
                            <div class="card-panel">

                            </div>
                        </div>
                    </div>
                </div>
                <div id="control" class="col s12">
                    <!-- TODO: Movement controls script -->
                    <div class="positionCameras card-panel">
                        <ul>
                            <div class="moveUpDownBox">
                                <li title="<?php echo _MOVE_UP; ?>" class="cameraIcon" id="cameraUp"><a
                                            class="mdi mdi-arrow-up-bold movement-icon"></a></li>
                            </div>

                            <li title="<?php echo _MOVE_LEFT; ?>" class="cameraIcon" id="cameraLeft"><a
                                        class="mdi mdi-arrow-left-bold movement-icon"></a></li>
                            <li class="cameraIcon" style="cursor: default"></li>
                            <li title="<?php echo _MOVE_RIGHT; ?>" class="cameraIcon" id="cameraRight"><a
                                        class="mdi mdi-arrow-right-bold movement-icon"></a></li>

                            <div class="moveUpDownBox">
                                <li title="<?php echo _MOVE_DOWN; ?>" class="cameraIcon" id="cameraDown"><a
                                            class="mdi mdi-arrow-down-bold movement-icon"></a></li>
                            </div>
                        </ul>
                    </div>
                </div>
                <div id="terminal" class="col s12">
                    <div class="row">
                        <form class="col s12">
                            <div class="row">
                                <div class="input-field col s12">
                                    <div style="height: 250px;">$ sudo apt-get install apache2</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s11">
                                    <input id="command" type="text" class="validate">
                                    <label for="command">Command</label>
                                </div>
                                <div class="input-field col s1">
                                    <button type="submit" class="waves-effect waves-teal btn-flat"><i
                                                class="material-icons">send</i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--JavaScript at end of body for optimized loading-->
<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
<script src="dist/clipboard.min.js"></script>
<script>
    $(document).ready(function () {
        if (sessionStorage.getItem("tab") === null || sessionStorage.getItem("tab") === 0)
            clickTabSelection(0);
        else if (sessionStorage.getItem("machineName") !== null) {
            loadMachine(sessionStorage.getItem("machineName"));
            clickTabSelection(sessionStorage.getItem("tab"));
        } else
            clickTabSelection(sessionStorage.getItem("tab"));


        if (sessionStorage.getItem("tabSettings") === null || sessionStorage.getItem("tabSettings") === 0)
            clickSettingsSectionSelection(0);
        else
            clickSettingsSectionSelection(sessionStorage.getItem("tabSettings"));

        $('.dropdown-trigger').dropdown({
                inDuration: 300,
                outDuration: 225,
                constrainWidth: false,
                coverTrigger: false,
                alignment: 'left'
            }
        );
        $('.tooltipped').tooltip({
            margin: -Math.abs(5 - (((window.screen.height - document.getElementsByClassName('bottom-navbar')[0].style.height) - document.getElementById('top-navbar').style.height) - 5))
        }
        );

        $('.modal').modal();

        $('ul.tabs').tabs();

        $('select').select();

        $('.fixed-action-btn').floatingActionButton({
            direction: 'left', // Direction menu comes out
            hoverEnabled: false, // Hover enabled
            toolbarEnabled: false // Toolbar transition enabled
        });

        if (typeof(Storage) === "undefined") {
            // TODO: Translation
            M.toast({html: 'Your browser does not support data saving'});
        }
        if (window.File && window.FileReader && window.FileList && window.Blob) {
            // Great success! All the File APIs are supported.
        } else {
            // TODO: Translation
            M.toast({html: 'File APIs are not fully supported by this browser. You won\'t be able to save settings'});
        }
    });

    function addtoev() {
        var bns = document.getElementsByTagName("button");
        for (i = 0; i < bns.length; i++) {
            bns[i].addEventListener("click", function () {
                playClick();
            });
        }
        var as = document.getElementsByTagName("a");
        for (i = 0; i < as.length; i++) {
            as[i].addEventListener("click", function () {
                playClick();
            });
        }
        var lis = document.getElementsByTagName("li");
        for (i = 0; i < lis.length; i++) {
            lis[i].addEventListener("click", function () {
                playClick();
            });
        }
        var inputs = document.getElementsByTagName("input");
        for (i = 0; i < inputs.length; i++) {
            inputs[i].addEventListener("click", function () {
                playClick();
            });
        }
    }

    window.addEventListener("load", function () {
        <?php if ($clickSound == "true" || $clickSound == "") {
        echo "addtoev();";
    } ?>
    });

    if (shouldSendUpdateNotification)
        sendNotification('<?php echo _UPDATE_AVAILABLE; ?>', 'update()');

    $(".bottom-navbar").ready(function () {
        $('.tooltipped').tooltip();
    });

    $("#updateLink").click(function (e) {
        // prevent the link from getting visited, for the time being
        e.preventDefault();

        update();
    });

    function update() {
        $.get("runCommand.php?c=cncpiupdate", function (r) {
            if (r !== "")
                M.toast({html: r});
            else
                M.toast({html: "<?php echo _UPDATE_ERROR; ?>"});
        });
    }

    var x = null;
    var y = null;
    document.addEventListener('mousemove', onMouseUpdate, false);
    document.addEventListener('mouseenter', onMouseUpdate, false);
    function onMouseUpdate(e) {
        x = e.pageX;
        y = e.pageY;
    }
    function getMouseX() {
        return x;
    }
    function getMouseY() {
        return y;
    }

    if (document.getElementById("terminalContent").addEventListener) {
        document.getElementById("terminalContent").addEventListener('contextmenu', function (e) {
            document.getElementById("terminalDropdown").style.position = "fixed";
            document.getElementById("terminalDropdown").style.display = "block";
            document.getElementById("terminalDropdown").style.opacity = "1";

            document.getElementById("terminalDropdown").style.top = y + "px";
            document.getElementById("terminalDropdown").style.left = x + "px";

            e.preventDefault();
        }, false);
    } else {
        document.getElementById("terminalContent").attachEvent('oncontextmenu', function () {
            document.getElementById("terminalDropdown").style.position = "fixed";
            document.getElementById("terminalDropdown").style.display = "block";
            document.getElementById("terminalDropdown").style.opacity = "1";

            document.getElementById("terminalDropdown").style.top = y + "px";
            document.getElementById("terminalDropdown").style.left = x + "px";

            window.event.returnValue = false;
        });
    }

    $(window).click(function () {
        document.getElementById("terminalDropdown").style.display = "none";
        document.getElementById("terminalDropdown").style.opacity = "0";
    });
    $('#terminalDropdown').click(function (event) {
        event.stopPropagation();
    });

    var clipboard = new Clipboard('.copyTerminal');
    clipboard.on('success', function (e) {
        console.info('Action:', e.action);
        console.info('Text:', e.text);
        console.info('Trigger:', e.trigger);

        M.toast({html: '<?php echo _COPIED; ?>'});

        e.clearSelection();
    });
    clipboard.on('error', function (e) {
        console.error('Action:', e.action);
        console.error('Trigger:', e.trigger);

        M.toast({html: '<?php echo _CANNOT_COPY; ?>'});
    });

    function setAllCloudItemsVisibility(visibility) {
        for (var i = 0; i < document.getElementsByClassName('main-directory-folder').length; i++) {
            if (visibility)
                document.getElementsByClassName("main-directory-folder")[i].style.display = 'block';
            else
                document.getElementsByClassName("main-directory-folder")[i].style.display = 'none';
        }
    }

    var notificationCounter = 0;

    function countNotification() {
        notificationCounter += 1;
    }

    function sendNotification(notificationText, clickAction) {
        M.toast({html: notificationText});

        // <li class="collection-item"><div>Message Content<a href="#!" class="secondary-content"><i class="material-icons">delete</i></a></div></li>
        // Any Message id = anymessage-label

        if (notificationCounter === undefined)
            notificationCounter = 0;

        // TODO: Delete Button
        countNotification();

        console.log("notificationCounter:" + notificationCounter);
        document.getElementById("anymessage-label").style.display = "none";
        document.getElementById("messages-container").innerHTML += '<li class="collection-item" onclick="' + clickAction + '" style="cursor: pointer"><div>' + notificationText + '</div></li>';

        if(notificationCounter > 0)
            document.getElementById("messages-icon").classList.add("left");
        else
            document.getElementById("messages-icon").classList.remove("left");

        document.getElementById("notificationCounterBadge").style.display = "inline-block";
        document.getElementById("notificationCounterBadge").innerHTML = notificationCounter.toString();
    }

    function loadingSpinner(enabled) {
        if (enabled)
            document.getElementById("loadingSpinner").style.display = "block";
        else
            document.getElementById("loadingSpinner").style.display = "none";
    }

    function saveSettings() {
        // Dropdowns: languageSelector
        // Inputs: maxFileSize
        // Logic: clickSoundSelector

        loadingSpinner(true);
        selectTab("-1");

        window.location.replace("settingsSet.php?language="
            + document.getElementById("languageSelector").options[document.getElementById("languageSelector").selectedIndex].value
            + "&maxFileSize="
            + document.getElementById("maxFileSize").value
            + "&clickSound="
            + document.getElementById("clickSoundSelector").checked
            + "&settingsDatabase="
            + document.getElementById("settings_db_loc").value
            + "&returnTo=<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>");
    }

    function clickTabSelection(index, machineName = "") {
        if (machineName === "") {
            sessionStorage.setItem("tab", index);
            sessionStorage.removeItem("machineName");
            selectTab(index);
            console.log("tab=" + sessionStorage.getItem("tab"));
        } else {
            sessionStorage.setItem("tab", index);
            sessionStorage.setItem("machineName", machineName);
            loadMachine(machineName);
            selectTab(index);
            console.log("tab=" + sessionStorage.getItem("tab"));
        }
        console.log("machineName=" + machineName);
    }

    function clickSettingsSectionSelection(index) {
        sessionStorage.setItem("tabSettings", index);
        selectSettingsTab(index);
        console.log("tabSettings=" + sessionStorage.getItem("tabSettings"));
    }

    function selectTab(tabIndex, isInFirstBoot = false) {
        if (isInFirstBoot)
            console.log("preselected tab " + tabIndex);

        switch (tabIndex) {
            case "0":
            case 0:
                document.getElementById("home-tab").style.display = "block";
                document.getElementById("cloud-tab").style.display = "none";
                document.getElementById("terminal-tab").style.display = "none";
                document.getElementById("settings-tab").style.display = "none";

                document.getElementById("machine-tab").style.display = "none";
                break;
            case "1":
            case 1:
                document.getElementById("home-tab").style.display = "none";
                document.getElementById("cloud-tab").style.display = "block";
                document.getElementById("terminal-tab").style.display = "none";
                document.getElementById("settings-tab").style.display = "none";

                document.getElementById("machine-tab").style.display = "none";
                break;
            case "2":
            case 2:
                document.getElementById("home-tab").style.display = "none";
                document.getElementById("cloud-tab").style.display = "none";
                document.getElementById("terminal-tab").style.display = "none";
                document.getElementById("settings-tab").style.display = "block";

                document.getElementById("machine-tab").style.display = "none";
                break;
            case "3":
            case 3:
                document.getElementById("home-tab").style.display = "none";
                document.getElementById("cloud-tab").style.display = "none";
                document.getElementById("terminal-tab").style.display = "block";
                document.getElementById("settings-tab").style.display = "none";

                document.getElementById("machine-tab").style.display = "none";
                break;
            case "66":
            case 66:
                document.getElementById("home-tab").style.display = "none";
                document.getElementById("cloud-tab").style.display = "none";
                document.getElementById("terminal-tab").style.display = "none";
                document.getElementById("settings-tab").style.display = "none";

                document.getElementById("machine-tab").style.display = "block";
                break;
            default:
                document.getElementById("home-tab").style.display = "none";
                document.getElementById("cloud-tab").style.display = "none";
                document.getElementById("terminal-tab").style.display = "none";
                document.getElementById("settings-tab").style.display = "none";

                document.getElementById("machine-tab").style.display = "none";
                break;
        }
    }

    function selectSettingsTab(tabIndex, isInFirstBoot = false) {
        if (isInFirstBoot)
            console.log("preselected settings section " + tabIndex);

        switch (tabIndex) {
            case "0":
            case 0:
                document.getElementById("s-general").style.display = "block";
                document.getElementById("s-about").style.display = "none";
                document.getElementById("s-cloud").style.display = "none";
                document.getElementById("s-interface").style.display = "none";
                document.getElementById("s-advanced").style.display = "none";

                document.getElementById("generalSelectorS").classList.add("active");
                document.getElementById("aboutSelectorS").classList.remove("active");
                document.getElementById("cloudSelectorS").classList.remove("active");
                document.getElementById("interfaceSelectorS").classList.remove("active");
                document.getElementById("advancedSelectorS").classList.remove("active");
                break;
            case "1":
            case 1:
                document.getElementById("s-general").style.display = "none";
                document.getElementById("s-about").style.display = "block";
                document.getElementById("s-cloud").style.display = "none";
                document.getElementById("s-interface").style.display = "none";
                document.getElementById("s-advanced").style.display = "none";

                document.getElementById("generalSelectorS").classList.remove("active");
                document.getElementById("aboutSelectorS").classList.add("active");
                document.getElementById("cloudSelectorS").classList.remove("active");
                document.getElementById("interfaceSelectorS").classList.remove("active");
                document.getElementById("advancedSelectorS").classList.remove("active");
                break;
            case "2":
            case 2:
                document.getElementById("s-general").style.display = "none";
                document.getElementById("s-about").style.display = "none";
                document.getElementById("s-cloud").style.display = "block";
                document.getElementById("s-interface").style.display = "none";
                document.getElementById("s-advanced").style.display = "none";

                document.getElementById("generalSelectorS").classList.remove("active");
                document.getElementById("aboutSelectorS").classList.remove("active");
                document.getElementById("cloudSelectorS").classList.add("active");
                document.getElementById("interfaceSelectorS").classList.remove("active");
                document.getElementById("advancedSelectorS").classList.remove("active");
                break;
            case "3":
            case 3:
                document.getElementById("s-general").style.display = "none";
                document.getElementById("s-about").style.display = "none";
                document.getElementById("s-cloud").style.display = "none";
                document.getElementById("s-interface").style.display = "block";
                document.getElementById("s-advanced").style.display = "none";

                document.getElementById("generalSelectorS").classList.remove("active");
                document.getElementById("aboutSelectorS").classList.remove("active");
                document.getElementById("cloudSelectorS").classList.remove("active");
                document.getElementById("interfaceSelectorS").classList.add("active");
                document.getElementById("advancedSelectorS").classList.remove("active");
                break;
            case "4":
            case 4:
                document.getElementById("s-general").style.display = "none";
                document.getElementById("s-about").style.display = "none";
                document.getElementById("s-cloud").style.display = "none";
                document.getElementById("s-interface").style.display = "none";
                document.getElementById("s-advanced").style.display = "block";

                document.getElementById("generalSelectorS").classList.remove("active");
                document.getElementById("aboutSelectorS").classList.remove("active");
                document.getElementById("cloudSelectorS").classList.remove("active");
                document.getElementById("interfaceSelectorS").classList.remove("active");
                document.getElementById("advancedSelectorS").classList.add("active");
                break;
        }
    }

    function loadMachine(machineName = "Error!") {
        document.getElementById("machine-title").innerHTML = machineName;

        document.getElementById("machine-tab").style.display = "block";
    }

</script>
</body>
</html>