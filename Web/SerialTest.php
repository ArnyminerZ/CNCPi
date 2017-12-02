<?php
include 'PhpSerial.php';

$comPort = "/dev/ttyACM0"; //The com port address. This is a debian address

$msg = '';

if (isset($_POST["hi"])) {
    $comPort = $_POST["port"];

    $serial = new PhpSerial;
    $serial->deviceSet($comPort);
    $serial->confBaudRate(9600);
    $serial->confParity("none");
    $serial->confCharacterLength(8);
    $serial->confStopBits(1);
    $serial->deviceOpen();
    sleep(2); //Unfortunately this is nessesary, arduino requires a 2 second delay in order to receive the message
    $serial->sendMessage("Well hello!");
    $serial->deviceClose();
    $msg = "You message has been sent! WOHOO!";
}
?>
<html>
<head>
    <title>Arduino control</title>
</head>
<body>
<form method="POST">
    <select name="port">
        <?php
        echo exec('whoami');

        $ports = exec("ls /dev/serial/by-id/");

        echo $ports;

        foreach (explode(" ", $ports) as $port){
            // <option value="volvo">Volvo</option>
            echo '<option value="' . $port . '">' . $port . '</option>';
        }
        ?>
    </select>

    <input type="submit" value="Send" name="hi">
</form>
<br>

<?= $msg ?>

</body>
</html>