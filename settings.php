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
            $('.fancybox').fancybox({margin: 0, autoCenter: true, autoResize: true, closeBtn: false});
            $('#ayarlarAc').fancybox({margin: 0, autoCenter: true, autoResize: true, maxWidth: 400, minWidth: 400, closeBtn: false <?php if ($fotoGaleri->sifreKontrol('123456'))echo ',modal: true';?> });
        });
        function gonderajax(formId) {
            $.ajax({
                type: "POST",
                data: $("#" + formId + "").serializeArray(),
                url: "resimisle.php",
                cache: true,
                success: function (data) {
                    if (data == 1 || data == true) {
                        $.fancybox({
                            content: "İşleminiz Başarıyla Gerçekleştirldi",
                            openEffect: "fade",
                            closeEffect: "fade",
                            autoSize: true
                        });
                    } else {
                        $.fancybox({
                            content: data,
                            openEffect: "fade",
                            closeEffect: "fade",
                            autoSize: true
                        });
                    }
                },
                error: function (x, hata) {
                    $.fancybox({
                        content: hata,
                        openEffect: "fade",
                        closeEffect: "fade",
                        autoSize: true
                    });
                }
            });
        }
    </script>
    <!-- CaptionHoverEffect-->

    <link rel="stylesheet" type="text/css" href="css/component.css"/>
    <script src="js/modernizr.custom.js"></script>
    <!-- /CaptionHoverEffect-->
    <!-- Responsive için-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--bootstrap-->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <!--/bootstrap-->
    <style>
        .sutun {
            width: 10%;
            float: left;
        }

        .sutun ul {
            padding: 0;;
        }

        .sutunlar {
            min-width: 960px;
            max-width: 95%;
            margin: 0 auto;
        }

        .fancy {
            display: none;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <?php
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
    <a href="#ayarlar" id="ayarlarAc" class="pull-right"><i class="icon-wrench icon-align-right"></i>Ayarlar</a>
    <?php
    else:
        header('Location:LoginForm/index.php');
    endif;
    ?>
</div>
<div id="ayarlar" class="fancy">
    <?php if ($fotoGaleri->sifreKontrol('123456')) { ?>
        <div class="alert alert-danger">
            <h4>Dikkat!</h4>
            Bu foto galeriyi ilk kullanışınız ilk kullanımda şifreyi değiştirmeniz gerekmektedir.
        </div>
    <?php } ?>
    <form method="post" action="postisle.php" class="form-horizontal">
        <h4>Başlığı Ayarla</h4>

        <div class="input-append text-center" style="margin: 0px auto; width: 100%;">
            <input class="span2" id="appendedInputButton" type="text" id="galeriBasligi"
                   value="<?php echo $fotoGaleri->title ?>">
            <button class="btn" type="button">Kaydet</button>
        </div>
        <input type="hidden" name="islem" value="baslikDedis">
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