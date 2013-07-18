<?php session_start();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php
error_reporting(E_ERROR);
if (!ini_get('display_errors')) {
ini_set('display_errors', 1);}
include_once '../fotoGaleri.php';
$galeri = new fotoGaleri();
if ($galeri->sifreKontrol($_POST['sifre'])):
    $_SESSION['oturum'] = true;
    header('Location:../settings.php'); else:
    ?>
    <head>
        <!-- General meta information -->
        <title>Login Form by Oussama Afellad</title>
        <meta name="keywords" content=""/>
        <meta name="description" content=""/>
        <meta name="robots" content="index, follow"/>
        <meta charset="utf-8"/>
        <!-- // General meta information -->
        <!-- Load Javascript -->
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/jquery.query-2.1.7.js"></script>
        <script type="text/javascript" src="js/rainbows.js"></script>
        <!-- // Load Javascipt -->

        <!-- Load stylesheets -->
        <link type="text/css" rel="stylesheet" href="css/style.css" media="screen"/>
        <!-- // Load stylesheets -->

        <script>


            $(document).ready(function () {

                $("#submit1").hover(
                    function () {
                        $(this).animate({"opacity": "0"}, "slow");
                    },
                    function () {
                        $(this).animate({"opacity": "1"}, "slow");
                    });
            });


        </script>

    </head>
    <body>

    <div id="wrapper">
        <div id="wrappertop"></div>

        <div id="wrappermiddle">

            <h2>Giriş</h2>

            <div id="password_input">

                <div id="password_inputleft"></div>

                <div id="password_inputmiddle">
                    <form action="" method="post" id="sifreForm">
                        <input type="password" name="sifre" id="url" value="Password" onclick="this.value = ''">
                        <img id="url_password" src="images/passicon.png" alt="">
                    </form>
                </div>

                <div id="password_inputright"></div>

            </div>

            <div id="submit">
                <form>
                    <input type="image" src="images/submit_hover.png" id="submit1" value="Sign In"
                           onclick="$('#url').submit();">
                    <input type="image" src="images/submit.png" id="submit2" value="Sign In">
                </form>
            </div>

        </div>

        <div id="wrapperbottom"></div>

        <div id="powered">
            <p>Powered by <a href="http://www.premiumfreebies.eu">Premiumfreebies Control Panel</a></p>
        </div>
    </div>

    </body>
<?php

endif;
?>
</html>