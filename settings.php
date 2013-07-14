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
/*
 * site başlığı
 */
$title = '';
?>
<?php
include_once "fotoGaleri.php";
$fotoGaleri= new fotoGaleri();
?>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foto Galeri</title>
    <link rel="shortcut icon" href="../favicon.ico">
    <meta name="description" content="Açıklama"/>
    <meta name="keywords" content=""/>
    <meta name="author" content="Samet ATABAŞ"/>
    <style>
        .imageBox {
            clear: both;
            float: left;
            width: 100px;
            height: 100px;
            border: 1px solid;
            margin: 2px;
        }

        .imageBox img {
            width: 100%;
            height: 100%;
        }

        .satir {

            float: left;
        }
    </style>
</head>
<body>
<?php
$fotoGaleri->resimler[] = array('0'); //boş bir sutun eklemek için
foreach ($fotoGaleri->resimler as $item) {
    if (is_array($item)) {
        echo '<div class="satir">' . "\n";
        $a = 1;
        foreach ($item as $resim) {
            echo '<div id="' . $a . '" class="imageBox"><img src="images/' . $resim . '"/></div>' . "\n";
            $a++;
        }
        /*
         * if = son sütun hariç her sütüne yeni resim için boş alan ekle
         */
        if (current($item) != '0') {
            echo '<div id="' . $a . '" class="imageBox"><img src=""/></div>' . "\n";
        }
        echo '</div>' . "\n";
    }
}
?>
</body>
</html>