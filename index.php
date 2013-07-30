<!doctype html>
<html>
<?php
include_once "fotoGaleri.php";
$fotoGaleri = new fotoGaleri();
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $fotoGaleri->title?></title>
    <link rel="shortcut icon" href="../favicon.ico">
    <meta name="description" content="Açıklama"/>
    <meta name="keywords" content=""/>
    <meta name="author" content="Samet ATABAŞ"/>
    <link href='css/JimNightshade-Regular.ttf' rel='stylesheet' type='text/css'/>
    <link href='http://fonts.googleapis.com/css?family=Jim+Nightshade&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <link href="css/style.css" rel="stylesheet" type="text/css"/>
    <script src="js/jquery-1.6.min.js" type="text/javascript"></script>
    <script src="js/portfolio.js" type="text/javascript"></script>
    <script src="js/init.js" type="text/javascript"></script>
</head>
<body>

<h1><?php echo $fotoGaleri->title?></h1>

<div id="portfolio">
    <div id="background"></div>
    <div class="arrows">
        <a href="#" class="up">Up</a>
        <a href="#" class="down">Down</a>
        <a href="#" class="prev">Previous</a>
        <a href="#" class="next">Next</a>
    </div>
    <div class="gallery">
        <div class="inside">
            <?php
            foreach ($fotoGaleri->resimler as $item) {
                if (is_array($item)) {
                    echo '<div class="item">' . "\n";
                    foreach ($item as $resim) {
                        echo '<div><img src="' . $resim . '"/></div>' . "\n";
                    }
                    echo '</div>' . "\n";
                }
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>

