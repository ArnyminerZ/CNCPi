<!DOCTYPE html>
<?php
session_start();

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
            position: fixed;
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
<nav>
    <div class="nav-wrapper teal darken-1">
        <a href="#!" class="brand-logo"><?php echo _TITLE; ?></a>
    </div>
</nav>
<div class="bottom-navbar">
    <nav>
        <div class="nav-wrapper teal darken-1">
            <ul class="row">
                <li class="col s3" style="text-align: center;" onclick="selectTab(0)">
                    <a class="grey-text text-lighten-3 tooltipped active" data-position="top" data-delay="50"
                       data-tooltip="<?php echo _HOME; ?>"><i class="material-icons">home</i></a>
                </li>
                <li class="col s3" style="text-align: center;" onclick="selectTab(1)">
                    <a class="grey-text text-lighten-3 tooltipped" data-position="top" data-delay="50"
                       data-tooltip="<?php echo _CLOUD; ?>"><i class="material-icons">cloud</i></a>
                </li>
                <li class="col s3" style="text-align: center;">
                    <a class="grey-text text-lighten-3 tooltipped modal-trigger" data-position="top" data-delay="50"
                       href="#messages-modal"
                       data-tooltip="<?php echo _MESSAGES; ?>"><i class="material-icons">message</i></a>
                </li>
                <li class="col s3" style="text-align: center;" onclick="selectTab(2)">
                    <a class="grey-text text-lighten-3 tooltipped" data-position="top" data-delay="50"
                       data-tooltip="<?php echo _SETTINGS; ?>"><i class="material-icons">settings</i></a>
                </li>
            </ul>
        </div>
    </nav>
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
        <ul class="collection">
            <!-- Messages Loader
                 Message:    '<li class="collection-item"><div>Message Content<a href="#!" class="secondary-content"><i class="material-icons">delete</i></a></div></li>'
                 Anymessage: 'Any Message' -->
        </ul>
        <?php echo _ANY_MESSAGE ?>
    </div>
</div>

<div id="uploadFileModal" class="modal">
    <form action="#" id="uploadFileModalForm">
        <div class="modal-content">
            <h4><?php echo _UPLOAD_FILE; ?></h4>
            <div class="file-field input-field">
                <div class="btn">
                    <span><?php echo _FILE; ?></span>
                    <input type="file">
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button"
                    onclick="document.getElementById('createFolderModalForm').submit();"
                    class="modal-action modal-close waves-effect waves-green btn-flat"><?php echo _UPLOAD; ?></button>
        </div>
    </form>
</div>
<div id="createFolder" class="modal">
    <form action="#" id="createFolderModalForm">
        <div class="modal-content">
            <h4><?php echo _CREATE_FOLDER; ?></h4>
            <div class="row">
                <div class="input-field col s6">
                    <input id="folder_name_modal_txt" name="folder_name" type="text" class="validate">
                    <label for="folder_name_modal_txt"><?php echo _FOLDER_NAME; ?></label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="document.getElementById('createFolderModalForm').submit();"
                    class="modal-action modal-close waves-effect waves-green btn-flat"><?php echo _CREATE; ?></button>
        </div>
    </form>
</div>

<div id="home-tab" class="col s12">
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
                               onclick="document.getElementById('home-tab').style.display='none';loadMachine('CNC 1')"><?php echo _GO_TO_MACHINE; ?></a>
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
                               onclick="document.getElementById('home-tab').style.display='none';loadMachine('CNC 2')"><?php echo _GO_TO_MACHINE; ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="cloud-tab" class="col s12">
    <div class="container">
        <div class="card-panel">
            <h1><?php echo _CLOUD; ?></h1>
            <a class="btn-floating btn-large red modal-trigger" href="#createFolder" style="position:absolute;right:18%;top:160px;"
               title="<?php echo _CREATE_FOLDER; ?>">
                <i class="large material-icons">create_new_folder</i>
            </a>
            <a class="btn-floating btn-large green modal-trigger" href="#uploadFileModal"
               style="position:absolute;right:22.5%;top:160px;"
               title="<?php echo _UPLOAD_FILE; ?>">
                <i class="large material-icons">file_upload</i>
            </a>

            <ul class="collection">
                <div class="main-directory-folder" id="mainf">
                    <!-- Folder -->
                    <li class="collection-item avatar"
                        onclick="document.getElementById('mainf').style.display='none';document.getElementById('flyingmachine-folder').style.display='block';">
                        <i class="mdi mdi-folder circle yellow"></i>
                        <span class="title"><b>Flying Machine</b></span>
                        <p>12/11/2017 09:12<br>
                            Folder - 12 kB
                        </p>
                        <a href="#!" class="secondary-content" style="right:25px;" title="<?php echo _RENAME; ?>"><i
                                    class="mdi mdi-pencil mdi-24px"></i></a>
                        <a href="#!" class="secondary-content" style="right:55px;"
                           title="<?php echo _DELETE_FOREVER; ?>"><i
                                    class="mdi mdi-delete-forever mdi-24px"></i></a>
                        <a href="#!" class="secondary-content" style="right:85px;" title="<?php echo _DOWNLOAD_ZIP; ?>"><i
                                    class="mdi mdi-download mdi-24px"></i></a>
                    </li>
                    <!-- /Folder -->

                    <li class="collection-item avatar">
                        <i class="mdi mdi-code-braces circle green"></i>
                        <span class="title"><b>Box Side 1</b></span>
                        <p>20/11/2017 19:46<br>
                            GCode - 16 kB
                        </p>
                        <a href="#!" class="secondary-content" style="right:25px;"
                           title="<?php echo _RENAME_FILE; ?>"><i
                                    class="mdi mdi-pencil mdi-24px"></i></a>
                        <a href="#!" class="secondary-content" style="right:55px;"
                           title="<?php echo _DELETE_FOREVER; ?>"><i
                                    class="mdi mdi-delete-forever mdi-24px"></i></a>
                        <a href="#!" class="secondary-content" style="right:85px;"
                           title="<?php echo _DOWNLOAD_FILE; ?>"><i
                                    class="mdi mdi-download mdi-24px"></i></a>
                        <a href="#!" class="secondary-content" style="right:115px;" title="<?php echo _LOAD_FILE; ?>"><i
                                    class="mdi mdi-upload-network mdi-24px"></i></a>
                    </li>
                </div>
                <!-- Folder Contents -->
                <div class="folder-contents" id="flyingmachine-folder" style="display:none;">
                    <li class="collection-item avatar"
                        onclick="document.getElementById('mainf').style.display='block';document.getElementById('flyingmachine-folder').style.display='none';">
                        <i class="mdi mdi-dots-horizontal circle"></i>
                        <span class="title"><b></b></span>
                    </li>

                    <li class="collection-item avatar">
                        <i class="mdi mdi-code-braces circle green"></i>
                        <span class="title"><b>This is an awesome machine model</b></span>
                        <p>19/09/2017 12:26<br>
                            GCode - 12.3 mB
                        </p>
                        <a href="#!" class="secondary-content" style="right:25px;"
                           title="<?php echo _RENAME_FILE; ?>"><i
                                    class="mdi mdi-pencil mdi-24px"></i></a>
                        <a href="#!" class="secondary-content" style="right:55px;"
                           title="<?php echo _DELETE_FOREVER; ?>"><i
                                    class="mdi mdi-delete-forever mdi-24px"></i></a>
                        <a href="#!" class="secondary-content" style="right:85px;"
                           title="<?php echo _DOWNLOAD_FILE; ?>"><i
                                    class="mdi mdi-download mdi-24px"></i></a>
                        <a href="#!" class="secondary-content" style="right:115px;" title="<?php echo _LOAD_FILE; ?>"><i
                                    class="mdi mdi-upload-network mdi-24px"></i></a>
                    </li>
                </div>
                <!-- /Folder Contents -->
            </ul>
        </div>
    </div>
</div>
<div id="settings-tab" class="col s12">
    <div class="container">
        <div class="card-panel">
            <h1><?php echo _SETTINGS; ?><h6><?php echo str_replace("&sl", _COOKIES, _COOKIES_SAVE); ?></h6></h1>
            <div class="card-panel row settings-tabcontrol">
                <div class="col s3 header">
                    <div class="collection">
                        <a onclick="selectSettingsTab(0)" id="generalSelectorS"
                           class="collection-item"><?php echo _GENERAL; ?></a>
                        <a onclick="selectSettingsTab(1)" id="aboutSelectorS"
                           class="collection-item"><?php echo _ABOUT; ?></a>
                    </div>
                </div>
                <div class="col s9 body">
                    <div id="s-general">
                        <br/>
                        <div class="input-field col s12">
                            <select>
                                <option value="" disabled selected><?php echo _CHOOSE_LANGUAGE; ?></option>
                                <option value="en">English</option>
                            </select>
                            <label><?php echo _LANGUAGE; ?></label>
                        </div>
                    </div>
                    <div id="s-about">
                        <p>
                        <h4><?php echo _SOFTWARE_VERSIONS; ?></h4>
                        <h6 style="color:#8bc34a"><i class="material-icons">info_outline</i><?php echo _SYSTEM_UPTODATE; ?></h6><a id="updateLink"><?php echo _CHECK_UPDATE; ?></a>
                        <?php echo _CNCPI_RELEASE_VERSION; ?>:
                        <i><?php $myfile = fopen("VERSION", "r") or die(_VERSION_LOAD_ERROR);
                            echo fread($myfile, filesize("VERSION"));
                            fclose($myfile); ?></i><br/>
                        <?php echo _CURRENT_PHP_VERSION . ': <i>' . phpversion() . '</i>'; ?>
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
                <div id="milling" class="col s12">Test 1</div>
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
                <div id="terminal" class="col s12">Test 3</div>
            </div>
        </div>
    </div>
</div>

<!--JavaScript at end of body for optimized loading-->
<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
<script>
    $(document).ready(function () {
        $('.dropdown-trigger').dropdown({
                inDuration: 300,
                outDuration: 225,
                constrainWidth: false,
                coverTrigger: false,
                alignment: 'left'
            }
        );

        $('.modal').modal();

        $('ul.tabs').tabs();

        $('select').select();

        selectTab(0);
        selectSettingsTab(0)
    });
    $(".bottom-navbar").ready(function () {
        $('.tooltipped').tooltip();
    });

    $("#updateLink").click(function(e) {
        // prevent the link from getting visited, for the time being
        e.preventDefault();

        //update the counter
        $.post("runCommand.php?c=cncpiupdate", function(resp) {
            if(resp === "success") {
                M.toast({html: 'Updated Correctly. Please, refresh'})
            } else {
                M.toast({html: 'Failed!'})
            }
            // updated. Now visit this link as normal
            window.location.href = this.href;
        });
    });

    function selectTab(tabIndex) {
        if (tabIndex === 0) {
            document.getElementById("home-tab").style.display = "block";
            document.getElementById("cloud-tab").style.display = "none";
            document.getElementById("settings-tab").style.display = "none";

            document.getElementById("machine-tab").style.display = "none";
        } else if (tabIndex === 1) {
            document.getElementById("home-tab").style.display = "none";
            document.getElementById("cloud-tab").style.display = "block";
            document.getElementById("settings-tab").style.display = "none";

            document.getElementById("machine-tab").style.display = "none";
        } else if (tabIndex === 2) {
            document.getElementById("home-tab").style.display = "none";
            document.getElementById("cloud-tab").style.display = "none";
            document.getElementById("settings-tab").style.display = "block";

            document.getElementById("machine-tab").style.display = "none";
        }
    }

    function selectSettingsTab(tabIndex) {
        switch (tabIndex) {
            case 0:
                document.getElementById("s-general").style.display = "block";
                document.getElementById("s-about").style.display = "none";

                document.getElementById("generalSelectorS").classList.add("active");
                document.getElementById("aboutSelectorS").classList.remove("active");
                break;
            case 1:
                document.getElementById("s-general").style.display = "none";
                document.getElementById("s-about").style.display = "block";

                document.getElementById("generalSelectorS").classList.remove("active");
                document.getElementById("aboutSelectorS").classList.add("active");
                break;
        }
    }

    function loadMachine(machineName) {
        if (machineName)
            document.getElementById("machine-title").innerHTML = machineName;

        document.getElementById("machine-tab").style.display = "block";
    }
</script>
</body>
</html>