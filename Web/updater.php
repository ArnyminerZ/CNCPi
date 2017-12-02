<?php
echo "Downloading from github...";
//file_put_contents("lastVersion.zip", file_get_contents("https://github.com/ArnyminerZ/CNCPi/archive/master.zip"));
echo 'ok<br/>';

echo "Decompressing downloaded file...";
/*$zip = new ZipArchive;
if ($zip->open('lastVersion.zip') === TRUE) {
    $zip->extractTo('./');
    $zip->close();
    echo 'ok<br/>';
} else {
    die('failed<br/>');
}*/

echo "Removing compressed file...<br/>";
exec('rm -rf /var/www/html/lastVersion.zip', $out);
var_dump($out);
echo "<br/>";
echo "ok<br/>";

echo "Moving downloaded web to current web...<br/>";
exec('cp -r /var/www/html/CNCPi-master/Web /var/www/html', $out);
var_dump($out);
echo "<br/>";
echo "ok<br/>";

echo "Removing downloaded web...<br/>";
exec('rm -rf /var/www/html/CNCPi-master/Web', $out);
var_dump($out);
echo "<br/>";
echo "ok<br/>";

echo "Removing downloaded github...<br/>";
exec('rm -rf /var/www/html/CNCPi-master', $out);
var_dump($out);
echo "<br/>";
echo "ok<br/>";