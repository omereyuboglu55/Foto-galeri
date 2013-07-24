<?php
error_reporting(E_ERROR);
if (!ini_get('display_errors')) {
    ini_set('display_errors', 1);
}
/**
 * Created by JetBrains PhpStorm.
 * User: sametatabasch
 * Date: 18.07.2013
 * Time: 22:58
 * To change this template use File | Settings | File Templates.
 */
include "fotoGaleri.php";
$fotogaleri = new fotoGaleri();
$sutunId = $_POST['sutunId'];
$resimId = $_POST['resimId'];
$resimYolu = 'images/4-2.jpg'; //uploaddan sonra gelecek bu
switch ($_POST['islem']) {
    case 'ekle':
        $fotogaleri->resimyukle($sutunId, $resimId, $_FILES['resim']);
        break;
    case 'degistir':
        //echo '<pre>';print_r($_FILES);echo '</pre>';
        $fotogaleri->resimDegistir($sutunId, $resimId, $_FILES['resim']);
        break;
    case 'sil':
        $fotogaleri->resimSil($sutunId, $resimId);
        break;
}
header("Location:settings.php");
?>
