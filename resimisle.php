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
echo '<pre>';
print_r($_POST);
echo '</pre>';
$resimYolu = 'images/4-2.jpg'; //uploaddan sonra gelecek bu
switch ($_POST['islem']) {
    case 'ekle':
        $fotogaleri->resimyukle($sutunId, $resimId, $resimYolu);
        break;
    case 'degistir':
        echo '<pre>';
        print_r($_FILES);
        echo '</pre>';
        //boyut kontrolü  uzantı kontrolü  yapılacak rsim yeniden boyutlandırılacak 600x400
        $uzanti = end(explode('.', $_FILES[resim]['name']));
        $resimYolu = 'images/' . time() . '.' . $uzanti;
        if (!move_uploaded_file($_FILES['resim']['tmp_name'], $resimYolu)) {
            echo 'Resim Yüklenemedi';
        }
        $fotogaleri->resimDegistir($sutunId, $resimId, $resimYolu);
        break;
    case 'sil':
        $fotogaleri->resimSil($sutunId, $resimId);
        break;
}
//header("Location:settings.php");
?>
