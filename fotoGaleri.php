<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sametatabasch
 * Date: 14.07.2013
 * Time: 03:16
 * version: 1.0
 */
include_once 'SimpleImage.php';
class fotoGaleri extends SimpleImage {
	/**
	 * Resimlerin tutulduğu dizi
	 *
	 * Array
	 */
	public $resimler = array();
	/**
	 * şifre bilgisini tutan değişken
	 *
	 * String
	 */
	private $sifre;
	/**
	 * Belirlenen resmin uzantısı
	 *
	 * String
	 */
	private $uzanti;
	/**
	 * İzin verilen uzuntıların saklandığı dizi
	 *
	 * Array
	 */
	private $gecerliUzantilar = array( 'jpg', 'png', 'jpeg', 'JPG' );
	/**
	 * Site başlığı
	 *
	 * String
	 */
	public $title = '';
	/**
	 * Xml dosyasının yolunu tutar
	 *
	 * String
	 */
	private $xmlDosya = 'galeri.xml';
	/**
	 * xml nesnesini tutan değişken
	 *
	 * Object
	 */
	public $xmlObj;

	/**
	 *
	 */
	function __construct() {
		$this->xmlDosya = __DIR__ . '/' . $this->xmlDosya;
		if ( ! file_exists( $this->xmlDosya ) ) {
			$icerik = '<?xml version="1.0"?>
<galeri>
    <title>Galeri Başlığı</title>
    <sifre>' . $this->sifrele( '123456' ) . '</sifre>
</galeri>';
			if ( ! file_put_contents( $this->xmlDosya, $icerik ) ) $this->Hata( 5 );
			chmod( $this->xmlDosya, 0600 );
		}
		//TODO buradaki taha kontrolü işe yaramıyor araştırmak lazım
		if ( ! ( $this->xmlObj = new SimpleXMLElement( $this->xmlDosya, null, true ) ) ) $this->Hata( 9 );
		$this->setGaleri();
	}

	/**
	 * Galerş ile ilgili bilgileri değişkenlere aktarır
	 *
	 * @return Array
	 */
	private function setGaleri() {
		$this->title = $this->xmlObj->title;
		$this->sifre = $this->xmlObj->sifre;
		$j           = 0;
		foreach ( $this->xmlObj->sutun as $sutun ) {
			foreach ( $sutun->resim as $resim ) {
				$this->resimler[$j][] = (string) $resim;
			}
			$j ++;
		}
	}

	/**
	 * Başlığı  tanımlar
	 *
	 * @param $title
	 */
	public function setTitle( $title ) {
		$xml = file_get_contents( $this->xmlDosya );
		$xml = str_replace( $this->title, $title, $xml );
		file_put_contents( $this->xmlDosya, $xml );
	}

	/**
	 * Verileri  xml dosyasına kaydeder
	 *
	 */
	public function kaydet() {
		if ( $this->xmlObj->asXML( $this->xmlDosya ) ) return true;
		else $this->Hata( 10 );
	}

	/**
	 * Şifre kontrolünü yapan fonksiyon
	 *
	 * @param String
	 *
	 * @return Boolean
	 */
	public function sifreKontrol( $sifre ) {
		if ( $this->sifre == $this->sifrele( $sifre ) ) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * xml dosyasına yeni şifre tanımlar
	 *
	 * @param $sifre
	 */
	public function setSifre( $sifre ) {
		$xml = file_get_contents( $this->xmlDosya );
		$xml = str_replace( $this->sifre, $this->sifrele( $sifre ), $xml );
		file_put_contents( $this->xmlDosya, $xml );
	}

	/**
	 * Kırılımaz bir  şifre oluşturur
	 * @param $sifre
	 *
	 * @return string
	 */
	public function sifrele( $sifre ) {
		return substr( sha1( base64_encode( md5( base64_encode( $sifre ) ) ) ), 5, 32 );
	}

	/**
	 * Settings.php sayfası aracılığıyla yüklenen resimleri xml e kaydeder
	 *
	 * @param String $sutunId Resmin bulunduğu sutun numarası
	 * @param String $resimId Resmin numarası
	 * @param        Array    yüklenen resimin bilgileri($_FILES)
	 *
	 * @throws ErrorException
	 * @return Bool
	 */
	public function resimyukle( $sutunId, $resimId, $resimBilgileri ) {
		if ( $resimBilgileri['error'] === UPLOAD_ERR_OK ) {
			$uzanti = end( explode( '.', $resimBilgileri['name'] ) );
			if ( in_array( $uzanti, $this->gecerliUzantilar ) ) {
				$resimYolu = 'images/' . time() . '.' . $uzanti;
				if ( ! ( $this->load( $resimBilgileri['tmp_name'] ) ) ) $this->Hata( 13 );
				$this->resize( 600, 400 );
				$this->save( $resimYolu );
			}
			else $this->Hata( 14 );
		}
		else $this->Hata( $resimBilgileri['error'] );
		if ( empty( $this->xmlObj->sutun ) ) { //eğer hiç sutun yoksa birinci sutun ve birinci resimi ekliyor
			$this->xmlObj->addChild( 'sutun' ); //birinci sutun eklendi
			$this->xmlObj->sutun->addAttribute( 'id', $sutunId ); //birinci sutuna id verildi
			$this->xmlObj->sutun->addChild( 'resim', $resimYolu ); //birinci resim eklendi
			$this->xmlObj->sutun->resim->addAttribute( 'id', $resimId ); //birinci resime id verildi
		}
		else { //eğer sutun var ise
			foreach ( $this->xmlObj->sutun as $sutun ) { // sutun id leri $idler dizisine yazılıyor
				$idler[] = (string) $sutun[id];
			}
			if ( in_array( $sutunId, $idler ) ) { //Eğer gelen sutun id $idler dizisinde varsa yani var olan bir sutunsa
				foreach ( $this->xmlObj->sutun as $sutun ) { // idleri kontrol  etmek için sutunlar döndürülüyor
					if ( (string) $sutun[id] == $sutunId ) { // gelen sutunid ye sahip olan sutun bulunuyor
						$sutun->addChild( 'resim', $resimYolu ); //sutuna resim ekleniyor
						foreach ( $sutun->resim as $resim ) { //sutun içindeki resimlerin idleri döndürülüyor
							if ( empty( $resim[id] ) ) { //id si olmayan resme id ekleniyor yani yeni resme
								$resim->addAttribute( 'id', $resimId );
							}
						}
						break;
					}
				}
			}
			else { //eğer var olmayan bir sutunsa
				$this->xmlObj->addChild( 'sutun', '' ); //yeni sutun ekleniyor
				foreach ( $this->xmlObj->sutun as $sutun ) { // idleri kontrol  etmek için sutunlar döndürülüyor
					if ( empty( $sutun[id] ) ) { //id si olmayan yani  yeni sutun bulunuyor
						$sutun->addAttribute( 'id', $sutunId ); // yeni sutuna id veriliyor
						$sutun->addChild( 'resim', $resimYolu ); //resim elkeniyor
						$sutun->resim->addAttribute( 'id', $resimId ); //resme id veriliyor
					}
				}
			}
		}
		$this->kaydet(); //işlemler sononda değişiklikler dosyaya kaydediliyor
	}

	/**
	 * Belirtilen resmi xml ve dizinden siler
	 *
	 * @param string $sutunId <p>Silinecek resimin bulunduğu sutun numarası</p>
	 * @param string $resimId <p>Silinecek resimin numarası</p>
	 */
	public function resimSil( $sutunId, $resimId ) {
		if ( ! ( $read = file_get_contents( $this->xmlDosya ) ) ) $this->Hata( 11 );
		$sutunKonum    = strpos( $read, '<sutun id="' . $sutunId . '">' ) + strlen( '<sutun id="' . $sutunId . '">' );
		$sutunKonumSon = strpos( $read, '</sutun>', $sutunKonum );
		$array         = array();
		foreach ( $this->xmlObj->sutun as $sutun ) {
			if ( (string) $sutun[id] == $sutunId ) {
				foreach ( $sutun->resim as $resim ) {
					$array[(integer) $resim[id] - 1] = (string) $resim;
				}
				if ( ! unlink( $array[$resimId - 1] ) ) {
					$this->Hata( 12 );
				}
				array_splice( $array, $resimId - 1, 1 );
				$a = '';
				foreach ( $array as $id => $deger ) {
					$a .= '<resim id="' . ( $id + 1 ) . '">' . $deger . "</resim>\n";
				}
				if ( empty( $a ) ) { //eğer resim kalmadıysa sütunu sil
					$sutunKonum    = $sutunKonum - strlen( '<sutun id="' . $sutunId . '">' );
					$sutunKonumSon = $sutunKonumSon + strlen( '</sutun>' );
				}
				$read = substr_replace( $read, $a, $sutunKonum, ( $sutunKonumSon - $sutunKonum ) );
				if ( ! ( file_put_contents( $this->xmlDosya, $read ) ) ) $this->Hata( 10 );
				break;
			}

		}
	}

	/**
	 * Belirtilen resmi yenisi ile değiştirir
	 *
	 * @param string $sutunId        <p>Değiştirilecek resimin bulunduğu sutun numarası</p>
	 * @param string $resimId        <p>Değiştirilecek resimin numarası</p>
	 * @param Array  $resimBilgileri <p>yüklenen resimin bulunduğu konum</p>
	 */
	public function resimDegistir( $sutunId, $resimId, $resimBilgileri ) {
		//resmi yükle ve sunucuda yeniden oluştur
		if ( $resimBilgileri['error'] === UPLOAD_ERR_OK ) {
			$uzanti = end( explode( '.', $resimBilgileri['name'] ) );
			if ( in_array( $uzanti, $this->gecerliUzantilar ) ) {
				$resimYolu = 'images/' . time() . '.' . $uzanti;
				if ( ! ( $this->load( $resimBilgileri['tmp_name'] ) ) ) $this->Hata( 13 );
				$this->resize( 600, 400 );
				$this->save( $resimYolu );
			}
			else $this->Hata( 14 );
		}
		else $this->Hata( $resimBilgileri['error'] );
		if ( ! ( $read = file_get_contents( $this->xmlDosya ) ) ) $this->Hata( 11 ); //xml verilerini oku
		$sutunKonum    = strpos( $read, '<sutun id="' . $sutunId . '">' ); //aranan sutun tag ının başlangıç konumu belirlenir
		$resimkonum    = strpos( $read, '<resim id="' . $resimId . '">', $sutunKonum ); //aranan resim tagının başlanğıç konumu belirlenir
		$resimkonumson = strpos( $read, '<resim id="' . ( $resimId + 1 ) . '">', $resimkonum ); //aranan resim tagının son konumu belirlenir
		if ( ! $resimkonumson ) $resimkonumson = strpos( $read, '</sutun>', $resimkonum ); //eğer resim sonu belirlenemediyse sutun sonu aranır yani resim sutundaki son resimse
		$read = substr_replace( $read, '<resim id="' . $resimId . '">' . $resimYolu . "</resim>\n\t", $resimkonum, ( $resimkonumson - $resimkonum ) );
		foreach ( $this->xmlObj->sutun as $sutun ) {
			if ( (string) $sutun[id] == $sutunId ) {
				foreach ( $sutun->resim as $resim ) {
					if ( (string) $resim[id] == $resimId ) {
						if ( ! unlink( (string) $resim ) ) $this->Hata( 12 );
					}
				}

			}
		}
		if ( ! ( file_put_contents( $this->xmlDosya, $read ) ) ) $this->Hata( 10 );
	}

	/**
	 *
	 *
	 */
	public function resimleriListele() {
		$liste            = '<div class="row-fluid">';
		$this->resimler[] = array( 'images/ekle.png' ); //boş bir sutun eklemek için
		$sutunId          = 1;
		foreach ( $this->resimler as $sutun ) {
			if ( $sutunId < 11 ):
				if ( is_array( $sutun ) ) {
					$liste .= '<div class="sutun" id="' . $sutunId . '">' . "\n";
					$liste .= '<ul class="grid cs-style-3">' . "\n";
					$resimId = 1;
					foreach ( $sutun as $resim ) {
						$liste .= '     <li id="' . $resimId . '">
                                 <figure>
                                    <img src="' . $resim . '" alt="' . $resim . '">';
						if ( current( $sutun ) != 'images/ekle.png' ) {
							$liste .= '             <figcaption>
                                        <div id="fancydegistir_' . $sutunId . '-' . $resimId . '" style="display:none;">
                                            <form class="form-inline" enctype="multipart/form-data" id="resimDegistir_' . $sutunId . '-' . $resimId . '" method="post" action="postisle.php">
                                                <div class="input-append text-center" style="margin: 0px auto; width: 100%;">
                                                    <input type="hidden" name="sutunId" value="' . $sutunId . '">
                                                    <input type="hidden" name="resimId" value="' . $resimId . '">
                                                    <input type="hidden" name="islem" value="degistir">
                                                    <input type="hidden" name="MAX_FILE_SIZE" value="' . $this->getUploadMaxFilesize() . '" />
                                                    <input type="file" name="resim" class="input-medium">
                                                    <button class="btn" type="submit">Değiştir</button>

                                                </div>
                                                <span class="help-block">Sadece jpg ve png uzantılı resimler</span>
                                            </form>
                                        </div>
                                        <div id="fancysil_' . $sutunId . '-' . $resimId . '" style="display:none;">
                                            <form id="resimSil_' . $sutunId . '-' . $resimId . '" method="post" action="postisle.php">
                                                <div class="alert">
                                                    <h4>Resmi silmek istiyor musunuz?</h4>
                                                </div>
                                                <input type="hidden" name="sutunId" value="' . $sutunId . '">
                                                <input type="hidden" name="resimId" value="' . $resimId . '">
                                                <input type="hidden" name="islem" value="sil">
                                                <div class="form-action text-center">
                                                    <input type="submit" class="btn btn-success btn-large" value="Evet">
                                                    <input type="button" class="btn btn-danger btn-large offset1" value="Hayır" onclick="$.fancybox.close()">
                                                </div>
                                            </form>
                                        </div>
                                        <a class="fancybox" href="#fancydegistir_' . $sutunId . '-' . $resimId . '">Değiştir</a>
                                        <a class="fancybox" href="#fancysil_' . $sutunId . '-' . $resimId . '">Sil</a>
                                    </figcaption>';

						}
						else {
							$liste .= '
                                    <figcaption>
                                        <div id="fancyekle_' . $sutunId . '-' . $resimId . '" style="display:none;">
                                            <form class="form-inline" enctype="multipart/form-data" id="resimEkle_' . $sutunId . '-' . $resimId . '" method="post" action="postisle.php">
                                                <div class="input-append text-center" style="margin: 0px auto; width: 100%;">
                                                    <input type="hidden" name="sutunId" value="' . $sutunId . '">
                                                    <input type="hidden" name="resimId" value="' . $resimId . '">
                                                    <input type="hidden" name="islem" value="ekle">
                                                    <input type="hidden" name="MAX_FILE_SIZE" value="' . $this->getUploadMaxFilesize() . '" />
                                                    <input type="file" name="resim" class="input-medium">
                                                    <button class="btn" type="submit">Ekle</button>
                                                </div>
                                                <span class="help-block">Sadece jpg ve png uzantılı resimler</span>
                                            </form>
                                        </div>
                                        <a class="fancybox" href="#fancyekle_' . $sutunId . '-' . $resimId . '">Ekle</a>
                                    </figcaption>
                        ';
						}
						$liste .= '
                                </figure>
                            </li>';
						$resimId ++;
					}
					/*
					 * if = son sütun hariç her sütüne yeni resim için boş alan ekle
					 */
					if ( current( $sutun ) != 'images/ekle.png' ) {
						$liste .= '
                            <li id="' . $resimId . '">
                                <figure>
                                    <img src="images/ekle.png" alt="' . $resim . '">
                                    <figcaption>
                                        <div id="fancyekle_' . $sutunId . '-' . $resimId . '" style="display:none;">
                                            <form class="form-inline" enctype="multipart/form-data" id="resimEkle_' . $sutunId . '-' . $resimId . '" method="post" action="postisle.php">
                                                <div class="input-append text-center" style="margin: 0px auto; width: 100%;">
                                                    <input type="hidden" name="sutunId" value="' . $sutunId . '">
                                                    <input type="hidden" name="resimId" value="' . $resimId . '">
                                                    <input type="hidden" name="islem" value="ekle">
                                                    <input type="hidden" name="MAX_FILE_SIZE" value="' . $this->getUploadMaxFilesize() . '" />
                                                    <input type="file" name="resim" class="input-medium">
                                                    <button class="btn" type="submit">Ekle</button>
                                                </div>
                                                <span class="help-block">Sadece jpg ve png uzantılı resimler</span>
                                            </form>
                                        </div>
                                        <a class="fancybox" href="#fancyekle_' . $sutunId . '-' . $resimId . '">Ekle</a>
                                    </figcaption>
                                </figure>
                            </li>';
					}
					$liste .= '</ul>' . "\n" . '</div>' . "\n";
				}
				$sutunId ++;
			endif;
		}
		$liste .= '<div style="clear:both;"></div>' . "\n" . '</div>' . "\n";
		echo $liste;
	}

	/**
	 * upload_max_filesize değerinin byte cinsinden verir
	 * @return int|string
	 */
	public function getUploadMaxFilesize() {
		$val  = ini_get( 'upload_max_filesize' );
		$val  = trim( $val );
		$last = strtolower( $val[strlen( $val ) - 1] );
		// todo buradaki  switch gereksiz gibi test etmek lazım
		switch ( $last ) {
			// 'G' birimi PHP 5.1.0 sürümünden beri var.
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}

		return $val;
	}

	public function oturumKontrol() {
		$sifreForm = '<script type="text/javascript">
        $(document).ready(function () {
            $("#girisAc").fancybox({margin: 0,autoCenter: true,autoResize: true,minHeight: 0,
                afterClose : function() {window.history.back()}});});//todo index.php ye yönlendirsin
        </script>
            <div id="girisForm" class="">
                <form class="form" id="sifreForm" action="postisle.php" method="post">';
		if ( $this->sifreKontrol( '123456' ) ):
			$sifreForm .= '
                    <div class="alert alert-danger" style="margin:0;">
                        İlk Giriş Şifreniz "123456"
                    </div>';
		endif;
		$sifreForm .= '
                    <legend>Şifre Girin</legend>
                    <div class="control-group">
                       <div class="controls">
                           <input type="password" id="sifre" name="sifre"/>
                           <input type="hidden" name="islem" value="giris"/>
                       </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-success btn-block">Doğrula</button>
                    </div>
                </form>
            </div>
            <a id="girisAc" href="#girisForm" class="fancy fancybox"></a>
            <script type="text/javascript">
                $(document).ready(function () {
                    $(\'#girisAc\').trigger(\'click\');
                });
            </script>';
		if ( $_SESSION['oturum'] ) {
			return true;
		}
		else {
			die( $sifreForm );
		}
	}

	/**
	 * Bir hata oluştuğunda bu fonksiyon o hatayı denetleyecek yada try kullanınca gerek kalmayacak
	 *
	 * @param $kod
	 */
	public function Hata( $kod ) {
		$hataMesaji = '<script type="text/javascript">
        $(document).ready(function () {
            $("#hataAc").fancybox({margin: 0,autoCenter: true,autoResize: true,padding: 0,minHeight: 0,
                afterClose : function() {
                    window.history.back()
                }
            });
        });
        </script><div id="hataMesaji" class="alert alert-danger" style="margin-bottom: 0px;">';
		switch ( $kod ) {
			case 1:
				$hataMesaji .= '<h4>HATA 1</h4> Yüklenen Dosya PHP\'ninizin verilen dosya boyutunu  aşmaktadır.';
				break;
			case 2:
				$hataMesaji .= '<h4>HATA 2</h4> Yüklenen Dosya maximum dosya boyutunu  aşmaktadır.';
				break;
			case 3:
				$hataMesaji .= '<h4>HATA 3</h4> Dosya tam olarak yüklemenemedi';
				break;
			case 4:
				$hataMesaji .= '<h4>HATA 4</h4> Hiçbir dosya seçilmedi';
				break;
			case 5:
				$hataMesaji .= '<h4>HATA 5</h4> galeri.xml oluşturulamadı.Lütfen dosya izinlerini kontrol edin.';
				break;
			case 6:
				$hataMesaji .= '<h4>HATA 6</h4> Geçici dizin yok';
				break;
			case 7:
				$hataMesaji .= '<h4>HATA 7</h4> Dosya diske yazılamadı';
				break;
			case 8:
				$hataMesaji .= '<h4>HATA 8</h4> Yükleme bir eklentiden dolayı durdu.';
				break;
			case 9:
				$hataMesaji .= '<h4>HATA 9</h4> galeri.xml dosyasında hata var.Lütfen Kontrol edin.';
				break;
			case 10:
				$hataMesaji .= '<h4>HATA 10</h4> Değişiklik galeri.xml dosyasına yazılamadı';
				break;
			case 11:
				$hataMesaji .= '<h4>HATA 11</h4> galeri.xml dosyası okunamadı.Lütfen dosya izinlerini kontrol edin.';
				break;
			case 12:
				$hataMesaji .= '<h4>HATA 12</h4> Resim silinemedi.Lütfen dosya izinlerini kontrol edin.';
				break;
			case 13:
				$hataMesaji .= '<h4>HATA 13</h4> Resim oluşturulamadı.Lütfen dosya izinlerini kontrol edin.';
				break;
			case 14:
				$hataMesaji .= '<h4>HATA 14</h4> Yüklenen resim uzantısı desteklenmiyor.';
				break;
			case 15:
				$hataMesaji .= '<h4>HATA 15</h4> ';
				break;
		}
		$hataMesaji .= '</div>
            <a id="hataAc" href="#hataMesaji" class="fancy"></a>
            <script type="text/javascript">
                $(document).ready(function () {
                    $(\'#hataAc\').trigger(\'click\');
                });
            </script> ';
		die( $hataMesaji );

	}
}

?>