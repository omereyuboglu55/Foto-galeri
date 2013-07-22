<?php session_start(); ?>
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
$fotoGaleri = new fotoGaleri();
if ($_SESSION['oturum']):?>
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
    <!-- Add jQuery library -->
    <script type="text/javascript" src="fancybox/lib/jquery-1.10.1.min.js"></script>
    <!-- Add fancyBox main JS and CSS files -->
    <script type="text/javascript" src="fancybox/source/jquery.fancybox.js?v=2.1.5"></script>
    <link rel="stylesheet" type="text/css" href="fancybox/source/jquery.fancybox.css?v=2.1.5" media="screen"/>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.fancybox').fancybox();
        });
    </script>
    <!-- CaptionHoverEffect-->
    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css" href="css/component.css"/>
    <script src="js/modernizr.custom.js"></script>
    <!-- /CaptionHoverEffect-->
    <style>
        .sutun {
            width: 10%;
            float: left;
        }

        .sutun ul {
            padding: 0;;
        }
    </style>
</head>
<body>
<div class="content">
    <?php
    $fotoGaleri->resimleriListele();
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