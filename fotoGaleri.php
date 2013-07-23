<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sametatabasch
 * Date: 14.07.2013
 * Time: 03:16
 * To change this template use File | Settings | File Templates.
 */

class fotoGaleri
{
    /*
    * Resimlerin tutulduğu dizi
    *
    * Array
    */
    public $resimler = array();
    /*
     * şifre bilgisini tutan değişken
     *
     * String
     */
    private $sifre = '123456'; /*
     * Belirlenen resmin uzantısı
     *
     * String
     */
    private $uzanti;
    /*
     * İzin verilen uzuntıların saklandığı dizi
     *
     * Array
     */
    private $gecerliUzantilar = array('jpg', 'png', 'jpeg', 'JPG');
    /*
     * Site başlığı
     *
     * String
     */
    public $title;
    /*
     * Xml dosyasının yolunu tutar
     *
     * String
     */
    private $xmlDosya = 'galeri.xml';
    /*
     * xml nesnesini tutan değişken
     *
     * Object
     */
    public $xmlObj;

    /*
     *
     */
    function __construct()
    {
        $this->xmlDosya = __DIR__ . '/' . $this->xmlDosya;
        $this->xmlObj = new SimpleXMLElement($this->xmlDosya, null, true);
        $this->setGaleri();
    }

    /*
     * Galerş ile ilgili bilgileri değişkenlere aktarır
     *
     * @return Array
     */
    private function setGaleri()
    {
        $this->title = $this->xmlObj->title;
        $j = 0;
        foreach ($this->xmlObj->sutun as $sutun) {
            foreach ($sutun->resim as $resim) {
                $this->resimler[$j][] = (string)$resim;
            }
            $j++;
        }
    }

    /*
     * Verileri  xml dosyasına kaydeder
     *
     */
    public function kaydet()
    {
        $this->xmlObj->asXML($this->xmlDosya);
    }

    /*
     * Şifre kontrolünü yapan fonksiyon
     *
     * @param String
     * @return Boolean
     */
    public function sifreKontrol($sifre)
    {
        if ($this->sifre === $sifre) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Settings.php sayfası aracılığıyla yüklenen resimleri xml e kaydeder
     * @param String $sutunId Resmin bulunduğu sutun numarası
     * @param String $resimId Resmin numarası
     * @param String yüklenen resimin bulunduğu konum
     */
    public function resimyukle($sutunId, $resimId, $resimYolu)
    {
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

    /*
     * Belirtilen resmi xml ve dizinden siler
     *
     * @param string $sutunId <p>Silinecek resimin bulunduğu sutun numarası</p>
     * @param string $resimId <p>Silinecek resimin numarası</p>
     */
    public function resimSil($sutunId, $resimId)
    {
        $read = file_get_contents($this->xmlDosya);
        $sutunKonum = strpos($read, '<sutun id="' . $sutunId . '">') + strlen('<sutun id="' . $sutunId . '">');
        $sutunKonumSon = strpos($read, '</sutun>', $sutunKonum);
        $array = array();
        foreach ($this->xmlObj->sutun as $sutun) {
            if ((string)$sutun[id] == $sutunId) {
                foreach ($sutun->resim as $resim) {
                    $array[(integer)$resim[id] - 1] = (string)$resim;
                }
                array_splice($array, $resimId - 1, 1);
                $a = '';
                foreach ($array as $id => $deger) {
                    $a .= '<resim id="' . ($id + 1) . '">' . $deger . "</resim>\n";
                }
                $read = substr_replace($read, $a, $sutunKonum, ($sutunKonumSon - $sutunKonum));
                file_put_contents($this->xmlDosya, $read);
                break;
            }

        }
    }

    /**
     * Belirtilen resmi yenisi ile değiştirir
     *
     * @param string $sutunId <p>Değiştirilecek resimin bulunduğu sutun numarası</p>
     * @param string $resimId <p>Değiştirilecek resimin numarası</p>
     * @param String $resimYolu <p>yüklenen resimin bulunduğu konum</p>
     */
    public function resimDegistir($sutunId, $resimId, $resimYolu)
    {
        $read = file_get_contents($this->xmlDosya);
        $sutunKonum = strpos($read, '<sutun id="' . $sutunId . '">');
        $resimkonum = strpos($read, '<resim id="' . $resimId . '">', $sutunKonum);
        $resimkonumson = strpos($read, '<resim id="' . ($resimId + 1) . '">', $resimkonum);
        if (!$resimkonumson) $resimkonumson = strpos($read, '</sutun>', $resimkonum);
        $read = substr_replace($read, '<resim id="' . $resimId . '">' . $resimYolu . "</resim>\n\t", $resimkonum, ($resimkonumson - $resimkonum));
        file_put_contents($this->xmlDosya, $read);
    }

    /**
     *
     *
     */
    public function resimleriListele()
    {
        $liste = '';
        $this->resimler[] = array('images/ekle.png'); //boş bir sutun eklemek için
        $sutunId = 1;
        foreach ($this->resimler as $sutun) {
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
                                            <form enctype="multipart/form-data" id="resimDegistir_' . $sutunId . '-' . $resimId . '" method="post" action="resimisle.php">
                                                <input type="hidden" name="sutunId" value="' . $sutunId . '">
                                                <input type="hidden" name="resimId" value="' . $resimId . '">
                                                <input type="hidden" name="islem" value="degistir">
                                                <input type="file" name="resim">
                                                <input type="submit" value="Tamam">
                                            </form>
                                        </div>
                                        <div id="fancysil_' . $sutunId . '-' . $resimId . '" style="display:none;">
                                            <form id="resimSil_' . $sutunId . '-' . $resimId . '" method="post" action="resimisle.php">
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
                                        <form id="resimEkle_' . $sutunId . '-' . $resimId . '" method="post" action="resimisle.php">
                                            <input type="hidden" name="sutunId" value="' . $sutunId . '">
                                            <input type="hidden" name="resimId" value="' . $resimId . '">
                                            <input type="hidden" name="islem" value="ekle">
                                            <input type="submit" value="Ekle">
                                        </form>
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
                                        <form id="resimEkle_' . $sutunId . '-' . $resimId . '" method="post" action="resimisle.php">
                                            <input type="hidden" name="sutunId" value="' . $sutunId . '">
                                            <input type="hidden" name="resimId" value="' . $resimId . '">
                                            <input type="hidden" name="islem" value="ekle">
                                            <input type="submit" value="Ekle">
                                        </form>
                                    </figcaption>
                                </figure>
                            </li>';
                }
                $liste .= '</ul>' . "\n" . '</div>' . "\n";
            }
            $sutunId++;
        }
        echo $liste;
    }
}