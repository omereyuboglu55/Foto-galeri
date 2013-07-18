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
$fotogaleri=new fotoGaleri();
$sutunId=$_POST['sutunId'];
$resimId=$_POST['resimId'];
$fotogaleri->resimyukle($sutunId,$resimId);
header("Location:settings.php");
?>