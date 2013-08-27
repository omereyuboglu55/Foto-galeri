<?php session_start(); ?>
<!doctype html>
<?php error_reporting(E_ERROR);
if (!ini_get('display_errors')) ini_set('display_errors', 1);
include_once "fotoGaleri.php";
$fotoGaleri = new fotoGaleri();
?>
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
    <script type="text/javascript">
        $(document).ready(function () {
            $('.fancybox').fancybox({margin: 0, autoCenter: true, autoResize: true, closeBtn: false, minHeight: 0});//ekle sil değiştir pencereleri için
            $('#ayarlarAc').fancybox({margin: 0, autoCenter: true, autoResize: true, maxWidth: 400, minWidth: 400, closeBtn: false <?php if ($fotoGaleri->sifreKontrol('123456'))echo ',modal: true';?> });
        });
    </script>
    <!-- CaptionHoverEffect-->
    <link rel="stylesheet" type="text/css" href="css/component.css"/>
    <script src="js/modernizr.custom.js"></script>
    <!-- /CaptionHoverEffect-->
    <!--bootstrap-->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="css/style.css" rel="stylesheet" media="screen">
</head>
<body>
<div class="container-fluid ">
    <?php
    $fotoGaleri->oturumKontrol();
    if ($fotoGaleri->sifreKontrol('123456')) {
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $('#ayarlarAc').trigger('click');
            });
            $('#sifreDegistir_form').bind('submit', function () {
                var yeniSifre = $('#sifreDegistir_form #yeniSifre').val();
                var yeniSifreTekrar = $('#sifreDegistir_form #yeniSifreTekrar').val();
                if (yeniSifre === yeniSifreTekrar) {
                    return true;
                } else {
                    alert('Şifreler uyuşmuyor');
                }
                return false;
            });
        </script>
    <?php
    }
    $fotoGaleri->resimleriListele();
    ?>
    <hr>
    <a href="#ayarlar" id="ayarlarAc" class="pull-right">Ayarlar</a>
    <a href="index.php" class="pull-left">Galeriye Dön</a>
</div>
<div id="ayarlar" class="fancy">
    <?php if ($fotoGaleri->sifreKontrol('123456')) { ?>
        <div class="alert alert-danger">
            <h4>Dikkat!</h4>
            Bu foto galeriyi ilk kullanışınız ilk kullanımda şifreyi değiştirmeniz gerekmektedir.
        </div>
    <?php  }
    //TODO ajax ile yapılsın ?>
    <form method="post" action="postisle.php" class="form-horizontal">
        <h4>Başlığı Ayarla</h4>

        <div class="input-append text-center" style="margin: 0px auto; width: 100%;">
            <input class="span2" type="text" name="galeriBasligi" value="<?php echo $fotoGaleri->title ?>">
            <input type="hidden" name="islem" value="baslikDedis">
            <button class="btn" type="submit">Kaydet</button>
        </div>
    </form>
    <form id="sifreDegistir_form" action="postisle.php" method="post" class="form-horizontal">
        <h4>Şifreyi Değiştir</h4>

        <div class="control-group">
            <label class="control-label" for="inputEmail">Yeni Şifre</label>

            <div class="controls">
                <input type="password" name="yeniSifre" id="yeniSifre" class="input-medium"></br>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputEmail">Yeni Şifre Tekrar</label>

            <div class="controls">
                <input type="password" name="yeniSifreTekrar" id="yeniSifreTekrar" class="input-medium"></br></br>
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <input type="submit" value="Kaydet" class="btn btn-primary">
                <input type="hidden" name="islem" value="sifeDegistir"/>
            </div>
        </div>
    </form>
</div>
<!--CaptionHoverEffect-->
<script src="js/toucheffects.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<!--/CaptionHoverEffect-->
</body>
</html>