<?php session_start();?>
<!doctype html>
<?php error_reporting(E_ERROR);
if (!ini_get('display_errors')) {
    ini_set('display_errors', 1);
}
/*
 * Galeriye resim ekleme
 * galeri başlığı gibi  birçok işlem bu sayfa ile yapılacak
 *
 */

?>
<?php
include_once "fotoGaleri.php";
$fotoGaleri= new fotoGaleri();
if($_SESSION['oturum']):
?>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $fotoGaleri->title ?></title>
    <link rel="shortcut icon" href="">
    <meta name="description" content="Açıklama"/>
    <meta name="keywords" content=""/>
    <meta name="author" content="Samet ATABAŞ"/>
    <!-- CaptionHoverEffect-->
    <link rel="stylesheet" type="text/css" href="css/default.css" />
    <link rel="stylesheet" type="text/css" href="css/component.css" />
    <script src="js/modernizr.custom.js"></script>
    <!-- /CaptionHoverEffect-->
    <style>
       .sutun {
           width: 10%;
            float: left;
        }
        .sutun ul{
            padding: 0;;
        }
    </style>
</head>
<body>
<div class="content">
<?php
$fotoGaleri->resimler[] = array('ekle.png'); //boş bir sutun eklemek için
foreach ($fotoGaleri->resimler as $item) {
    if (is_array($item)) {
        echo '<div class="sutun">' . "\n";
        echo '<ul class="grid cs-style-3">'."\n";
        $a = 1;
        foreach ($item as $resim) {
            //echo '<div id="' . $a . '" class="imageBox"><img src="images/' . $resim . '"/></div>' . "\n";
            echo '
            <li id="' . $a . '">
                <figure>
                    <img src="images/' . $resim . '" alt="'.$resim.'">
                    <figcaption>
                        <a href="#">Değiştir</a>
                        <a href="#">Sil</a>
                    </figcaption>
                </figure>
            </li>';
            $a++;
        }
        /*
         * if = son sütun hariç her sütüne yeni resim için boş alan ekle
         */
        if (current($item) != '0') {
            echo '
            <li id="' . $a . '">
                <figure>
                    <img src="images/ekle.png" alt="'.$resim.'">
                    <figcaption>
                        <a href="#">Ekle</a>
                    </figcaption>
                </figure>
            </li>';
        }
        echo '</ul>'."\n".'</div>' . "\n";
    }
}
else:
header('Location:LoginForm/index.php');
endif;
?>
</div>
<!--CaptionHoverEffect-->
<script src="js/toucheffects.js"></script>
<!--/CaptionHoverEffect-->
</body>
</html>