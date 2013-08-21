<?php session_start(); ?>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $fotoGaleri->title ?></title>
    <meta name="author" content="Samet ATABAŞ"/>
    <!-- Add jQuery library -->
    <script type="text/javascript" src="fancybox/lib/jquery-1.10.1.min.js"></script>
    <!-- Add fancyBox main JS and CSS files -->
    <script type="text/javascript" src="fancybox/source/jquery.fancybox.js?v=2.1.5"></script>
    <link rel="stylesheet" type="text/css" href="fancybox/source/jquery.fancybox.css?v=2.1.5" media="screen"/>
    <!--bootstrap-->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
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
        $fotogaleri->setSifre($_POST['yeniSifre']);
        break;
    case 'baslikDedis':
        $fotogaleri->setTitle($_POST['galeriBasligi']);
        break;
    case 'giris':
        if ($fotogaleri->sifreKontrol($_POST['sifre'])) $_SESSION['oturum'] = true;
        break;
}
header("Location:settings.php");
?>
</body>
</html>