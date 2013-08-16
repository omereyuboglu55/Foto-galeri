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
switch ($_POST['islem']) {
    case 'ekle':
        $fotogaleri->resimyukle($sutunId, $resimId, $_FILES['resim']);
        break;
    case 'degistir':
        $fotogaleri->resimDegistir($sutunId, $resimId, $_FILES['resim']);
        break;
    case 'sil':
        $fotogaleri->resimSil($sutunId, $resimId);
        break;
    case 'sifeDegistir':
            $yeniSifre=$_POST['yeniSifre'];
            $fotogaleri->setSifre($yeniSifre);
        break;
    case 'baslikDedis':
        $fotogaleri->setTitle($_POST['galeriBasligi']);
        break;
}
header("Location:settings.php");
?>
