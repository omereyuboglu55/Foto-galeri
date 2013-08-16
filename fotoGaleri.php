<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sametatabasch
 * Date: 14.07.2013
 * Time: 03:16
 * To change this template use File | Settings | File Templates.
 */
include_once 'SimpleImage.php';
class fotoGaleri extends SimpleImage
{
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
    private $gecerliUzantilar = array('jpg', 'png', 'jpeg', 'JPG');
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
    function __construct()
    {
        $this->xmlDosya = __DIR__ . '/' . $this->xmlDosya;
        if (!file_exists($this->xmlDosya)) {
            $icerik = '<?xml version="1.0"?>
<galeri>
    <title>Galeri Başlığı</title>
    <sifre>e10adc3949ba59abbe56e057f20f883e</sifre>
</galeri>';
            if (!file_put_contents($this->xmlDosya, $icerik)) $this->Hata(5);
            chmod($this->xmlDosya, 0644);
        }
        //TODO buradaki taha kontrolü işe yaramıyor araştırmak lazım
        if (!($this->xmlObj = new SimpleXMLElement($this->xmlDosya, null, true))) $this->Hata(9);
        $this->setGaleri();
    }

    /**
     * Galerş ile ilgili bilgileri değişkenlere aktarır
     *
     * @return Array
     */
    private function setGaleri()
    {
        $this->title = $this->xmlObj->title;
        $this->sifre = $this->xmlObj->sifre;
        $j = 0;
        foreach ($this->xmlObj->sutun as $sutun) {
            foreach ($sutun->resim as $resim) {
                $this->resimler[$j][] = (string)$resim;
            }
            $j++;
        }
    }

    /**
     * Şifreyi  tanımlar
     * @param $title
     */
    public function setTitle($title){
        $xml=file_get_contents($this->xmlDosya);
        $xml=str_replace($this->title,$title,$xml);
        file_put_contents($this->xmlDosya,$xml);
    }

    /**
     * Verileri  xml dosyasına kaydeder
     *
     */
    public function kaydet()
    {
        if ($this->xmlObj->asXML($this->xmlDosya)) return true; else $this->Hata(10);
    }

    /**
     * Şifre kontrolünü yapan fonksiyon
     *
     * @param String
     * @return Boolean
     */
    public function sifreKontrol($sifre)
    {
        if ($this->sifre == md5($sifre)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $sifre
     */
    public function setSifre($sifre){
        $xml=file_get_contents($this->xmlDosya);
        $xml=str_replace($this->sifre,md5($sifre),$xml);
        file_put_contents($this->xmlDosya,$xml);
    }

    /**
     * Settings.php sayfası aracılığıyla yüklenen resimleri xml e kaydeder
     * @param String $sutunId Resmin bulunduğu sutun numarası
     * @param String $resimId Resmin numarası
     * @param Array yüklenen resimin bilgileri($_FILES)
     *
     * @throws ErrorException
     * @return Bool
     */
    public function resimyukle($sutunId, $resimId, $resimBilgileri)
    {
        if ($resimBilgileri['error'] === UPLOAD_ERR_OK) {
            $uzanti = end(explode('.', $resimBilgileri['name']));
            if (in_array($uzanti, $this->gecerliUzantilar)) {
                $resimYolu = 'images/' . time() . '.' . $uzanti;
                if (!($this->load($resimBilgileri['tmp_name']))) $this->Hata(13);
                $this->resize(600, 400);
                $this->save($resimYolu);
            } else $this->Hata(14);
        } else $this->Hata($resimBilgileri['error']);
        if (empty($this->xmlObj->sutun)) { //eğer hiç sutun yoksa birinci sutun ve birinci resimi ekliyor
            $this->xmlObj->addChild('sutun'); //birinci sutun eklendi
            $this->xmlObj->sutun->addAttribute('id', $sutunId); //birinci sutuna id verildi
            $this->xmlObj->sutun->addChild('resim', $resimYolu); //birinci resim eklendi
            $this->xmlObj->sutun->resim->addAttribute('id', $resimId); //birinci resime id verildi
        } else { //eğer sutun var ise
            foreach ($this->xmlObj->sutun as $sutun) { // sutun id leri $idler dizisine yazılıyor
                $idler[] = (string)$sutun[id];
            }
            if (in_array($sutunId, $idler)) { //Eğer gelen sutun id $idler dizisinde varsa yani var olan bir sutunsa
                foreach ($this->xmlObj->sutun as $sutun) { // idleri kontrol  etmek için sutunlar döndürülüyor
                    if ((string)$sutun[id] == $sutunId) { // gelen sutunid ye sahip olan sutun bulunuyor
                        $sutun->addChild('resim', $resimYolu); //sutuna resim ekleniyor
                        foreach ($sutun->resim as $resim) { //sutun içindeki resimlerin idleri döndürülüyor
                            if (empty($resim[id])) { //id si olmayan resme id ekleniyor yani yeni resme
                                $resim->addAttribute('id', $resimId);
                            }
                        }
                        break;
                    }
                }
            } else { //eğer var olmayan bir sutunsa
                $this->xmlObj->addChild('sutun', ''); //yeni sutun ekleniyor
                foreach ($this->xmlObj->sutun as $sutun) { // idleri kontrol  etmek için sutunlar döndürülüyor
                    if (empty($sutun[id])) { //id si olmayan yani  yeni sutun bulunuyor
                        $sutun->addAttribute('id', $sutunId); // yeni sutuna id veriliyor
                        $sutun->addChild('resim', $resimYolu); //resim elkeniyor
                        $sutun->resim->addAttribute('id', $resimId); //resme id veriliyor
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
    public function resimSil($sutunId, $resimId)
    {
        if (!($read = file_get_contents($this->xmlDosya))) $this->Hata(11);
        $sutunKonum = strpos($read, '<sutun id="' . $sutunId . '">') + strlen('<sutun id="' . $sutunId . '">');
        $sutunKonumSon = strpos($read, '</sutun>', $sutunKonum);
        $array = array();
        foreach ($this->xmlObj->sutun as $sutun) {
            if ((string)$sutun[id] == $sutunId) {
                foreach ($sutun->resim as $resim) {
                    $array[(integer)$resim[id] - 1] = (string)$resim;
                }
                if (!unlink($array[$resimId - 1])) {
                    $this->Hata(12);
                }
                array_splice($array, $resimId - 1, 1);
                $a = '';
                foreach ($array as $id => $deger) {
                    $a .= '<resim id="' . ($id + 1) . '">' . $deger . "</resim>\n";
                }
                if (empty($a)) { //eğer resim kalmadıysa sütunu sil
                    $sutunKonum = $sutunKonum - strlen('<sutun id="' . $sutunId . '">');
                    $sutunKonumSon = $sutunKonumSon + strlen('</sutun>');
                }
                $read = substr_replace($read, $a, $sutunKonum, ($sutunKonumSon - $sutunKonum));
                if (!(file_put_contents($this->xmlDosya, $read))) $this->Hata(10);
                break;
            }

        }
    }

    /**
     * Belirtilen resmi yenisi ile değiştirir
     *
     * @param string $sutunId <p>Değiştirilecek resimin bulunduğu sutun numarası</p>
     * @param string $resimId <p>Değiştirilecek resimin numarası</p>
     * @param Array $resimBilgileri <p>yüklenen resimin bulunduğu konum</p>
     */
    public function resimDegistir($sutunId, $resimId, $resimBilgileri)
    {
        //resmi yükle ve sunucuda yeniden oluştur
        if ($resimBilgileri['error'] === UPLOAD_ERR_OK) {
            $uzanti = end(explode('.', $resimBilgileri['name']));
            if (in_array($uzanti, $this->gecerliUzantilar)) {
                $resimYolu = 'images/' . time() . '.' . $uzanti;
                if (!($this->load($resimBilgileri['tmp_name']))) $this->Hata(13);
                $this->resize(600, 400);
                $this->save($resimYolu);
            } else $this->Hata(14);
        } else $this->Hata($resimBilgileri['error']);
        if (!($read = file_get_contents($this->xmlDosya))) $this->Hata(11); //xml verilerini oku
        $sutunKonum = strpos($read, '<sutun id="' . $sutunId . '">'); //aranan sutun tag ının başlangıç konumu belirlenir
        $resimkonum = strpos($read, '<resim id="' . $resimId . '">', $sutunKonum); //aranan resim tagının başlanğıç konumu belirlenir
        $resimkonumson = strpos($read, '<resim id="' . ($resimId + 1) . '">', $resimkonum); //aranan resim tagının son konumu belirlenir
        if (!$resimkonumson) $resimkonumson = strpos($read, '</sutun>', $resimkonum); //eğer resim sonu belirlenemediyse sutun sonu aranır yani resim sutundaki son resimse
        $read = substr_replace($read, '<resim id="' . $resimId . '">' . $resimYolu . "</resim>\n\t", $resimkonum, ($resimkonumson - $resimkonum));
        foreach ($this->xmlObj->sutun as $sutun) {
            if ((string)$sutun[id] == $sutunId) {
                foreach ($sutun->resim as $resim) {
                    if ((string)$resim[id] == $resimId) {
                        if (!unlink((string)$resim)) $this->Hata(12);
                    }
                }

            }
        }
        if (!(file_put_contents($this->xmlDosya, $read))) $this->Hata(10);
    }

    /**
     *
     *
     */
    public function resimleriListele()
    {
        $liste = '<div class="sutunlar">';
        $this->resimler[] = array('images/ekle.png'); //boş bir sutun eklemek için
        $sutunId = 1;
        foreach ($this->resimler as $sutun) {
            if($sutunId<11):
            if (is_array($sutun)) {
                $liste .= '<div class="sutun" id="' . $sutunId . '">' . "\n";
                $liste .= '<ul class="grid cs-style-3">' . "\n";
                $resimId = 1;
                foreach ($sutun as $resim) {
                    $liste .= '     <li id="' . $resimId . '">
                                 <figure>
                                    <img src="' . $resim . '" alt="' . $resim . '">';
                    if (current($sutun) != 'images/ekle.png') {
                        $liste .= '             <figcaption>
                                        <div id="fancydegistir_' . $sutunId . '-' . $resimId . '" style="display:none;">
                                            <form enctype="multipart/form-data" id="resimDegistir_' . $sutunId . '-' . $resimId . '" method="post" action="postisle.php">
                                                <input type="hidden" name="sutunId" value="' . $sutunId . '">
                                                <input type="hidden" name="resimId" value="' . $resimId . '">
                                                <input type="hidden" name="islem" value="degistir">
                                                <input type="hidden" name="MAX_FILE_SIZE" value="' . $this->getUploadMaxFilesize() . '" />
                                                <input type="file" name="resim">
                                                <input type="submit" value="Tamam">
                                            </form>
                                        </div>
                                        <div id="fancysil_' . $sutunId . '-' . $resimId . '" style="display:none;">
                                            <form id="resimSil_' . $sutunId . '-' . $resimId . '" method="post" action="postisle.php">
                                                Resmi silmek istiyor musunuz?
                                                <input type="hidden" name="sutunId" value="' . $sutunId . '">
                                                <input type="hidden" name="resimId" value="' . $resimId . '">
                                                <input type="hidden" name="islem" value="sil">
                                                <input type="submit" value="Evet">
                                                <input type="button" value="Hayır" onclick="$.fancybox.close()">
                                            </form>
                                        </div>
                                        <a class="fancybox" href="#fancydegistir_' . $sutunId . '-' . $resimId . '">Değiştir</a>
                                        <a class="fancybox" href="#fancysil_' . $sutunId . '-' . $resimId . '">Sil</a>
                                    </figcaption>';

                    } else {
                        $liste .= '
                                    <figcaption>
                                        <div id="fancyekle_' . $sutunId . '-' . $resimId . '" style="display:none;">
                                            <form enctype="multipart/form-data" id="resimEkle_' . $sutunId . '-' . $resimId . '" method="post" action="postisle.php">
                                                <input type="hidden" name="sutunId" value="' . $sutunId . '">
                                                <input type="hidden" name="resimId" value="' . $resimId . '">
                                                <input type="hidden" name="islem" value="ekle">
                                                <input type="hidden" name="MAX_FILE_SIZE" value="' . $this->getUploadMaxFilesize() . '" />
                                                <input type="file" name="resim">
                                                <input type="submit" value="Tamam">
                                            </form>
                                        </div>
                                        <a class="fancybox" href="#fancyekle_' . $sutunId . '-' . $resimId . '">Ekle</a>
                                    </figcaption>
                        ';
                    }
                    $liste .= '
                                </figure>
                            </li>';
                    $resimId++;
                }
                /*
                 * if = son sütun hariç her sütüne yeni resim için boş alan ekle
                 */
                if (current($sutun) != 'images/ekle.png') {
                    $liste .= '
                            <li id="' . $resimId . '">
                                <figure>
                                    <img src="images/ekle.png" alt="' . $resim . '">
                                    <figcaption>
                                        <div id="fancyekle_' . $sutunId . '-' . $resimId . '" style="display:none;">
                                            <form enctype="multipart/form-data" id="resimEkle_' . $sutunId . '-' . $resimId . '" method="post" action="postisle.php">
                                                <input type="hidden" name="sutunId" value="' . $sutunId . '">
                                                <input type="hidden" name="resimId" value="' . $resimId . '">
                                                <input type="hidden" name="islem" value="ekle">
                                                <input type="hidden" name="MAX_FILE_SIZE" value="' . $this->getUploadMaxFilesize() . '" />
                                                <input type="file" name="resim">
                                                <input type="submit" value="Tamam">
                                            </form>
                                        </div>
                                        <a class="fancybox" href="#fancyekle_' . $sutunId . '-' . $resimId . '">Ekle</a>
                                    </figcaption>
                                </figure>
                            </li>';
                }
                $liste .= '</ul>' . "\n" . '</div>' . "\n";
            }
            $sutunId++;
            endif;
        }
        $liste.='<div style="clear:both;"></div>'."\n".'</div>'."\n";
        echo $liste;
    }

    /**
     * upload_max_filesize değerinin byte cinsinden verir
     * @return int|string
     */
    public function getUploadMaxFilesize()
    {
        $val = ini_get('upload_max_filesize');
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        switch ($last) {
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

    /**
     * Bir hata oluştuğunda bu fonksiyon o hatayı denetleyecek yada try kullanınca gerek kalmayacak
     * @param $kod
     */
    public function Hata($kod)
    {
        header("Content-type:text/html; charset=utf-8");
        switch ($kod) {
            case 1:
                die("HATA 1: Yüklenen Dosya PHP'ninizin verilen dosya boyutunu  aşmaktadır.");
                break;
            case 2:
                die("HATA 2: Yüklenen Dosya maximum dosya boyutunu  aşmaktadır.");
                break;
            case 3:
                die("HATA 3: Dosya tam olarak yüklemenemedi");
                break;
            case 4:
                die("HATA 4: Hiçbir dosya seçilmedi");
                break;
            case 5:
                die("HATA 5: galeri.xml oluşturulamadı.Lütfen dosya izinlerini kontrol edin.");
                break;
            case 6:
                die("HATA 6: Geçici dizin yok");
                break;
            case 7:
                die("HATA 7: Dosya diske yazılamadı");
                break;
            case 8:
                die("HATA 8: Yükleme bir eklentiden dolayı durdu.");
                break;
            case 9:
                die("HATA 9: galeri.xml dosyasında hata var.Lütfen Kontrol edin.");
                break;
            case 10:
                die("HATA 10: Değişiklik galeri.xml dosyasına yazılamadı");
                break;
            case 11:
                die("HATA 11: galeri.xml dosyası okunamadı.Lütfen dosya izinlerini kontrol edin.");
                break;
            case 12:
                die("HATA 12: Resim silinemedi.Lütfen dosya izinlerini kontrol edin.");
                break;
            case 13:
                die("HATA 13: Resim oluşturulamadı.Lütfen dosya izinlerini kontrol edin.");
                break;
            case 14:
                die("HATA 14: Yüklenen resim uzantısı desteklenmiyor.");
                break;
            case 15:
                die("HATA 15: ");
                break;
        }
    }
}

?>