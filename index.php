<!doctype html>
<?php
	include('dizinislemleri.class.php');
 	$Directory = new DirectoryReader();
 	$dizin=substr(__FILE__,0,-9);
   $Directory->setDir($dizin.'images');
   foreach ($Directory->getFileList('a') as $dosya){
		$uzanti=substr($dosya,-3,3);
		$isim=substr($dosya,0,-4);
		if($uzanti=='jpg' ||$uzanti=='png' ||$uzanti=='jpeg' ||$uzanti=='JPG') {
			$grup=explode('-',$isim);
			if(!is_array($resimler[$grup[0]])) $resimler[$grup[0]]=array();
			array_push($resimler[$grup[0]],$isim.'.'.$uzanti);
		}
	}
?>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Foto Galeri</title>
		<link rel="shortcut icon" href="../favicon.ico">
		<meta name="description" content="Açıklama" />
        <meta name="keywords" content="" />
		<meta name="author" content="Samet ATABAŞ" />
		<link href='css/JimNightshade-Regular.ttf' rel='stylesheet' type='text/css' />
		<link href='http://fonts.googleapis.com/css?family=Jim+Nightshade&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
		
		<link href="css/style.css" rel="stylesheet" type="text/css" />
		<script src="js/jquery.js" type="text/javascript"></script>
		<script src="js/portfolio.js" type="text/javascript"></script>
		<script src="js/init.js" type="text/javascript"></script>
	</head>
	<body>
		
		<h1>Şeyma ÖZDEMİR</h1>
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
				$x=count($resimler);
				for($i=1;$i<=$x;$i++) {
					if(is_array($resimler[$i])) {
						echo '<div class="item">'."\n";
						foreach ($resimler[$i] as $resim){
							echo '<div><img src="images/'.$resim.'"/></div>'."\n";
						}
						echo '</div>'."\n";
					}else {$x++;}
				}
				?>
				</div>
			</div>
		</div>
	</body>
</html>

