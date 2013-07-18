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
    function __construct()
    {
        $this->xmlDosya = __DIR__ . '/' . $this->xmlDosya;
        $this->setGaleri();
    }

    /*
     * Resimlerin tutulduğu dizi
     *
     * Array
     */
    public $resimler = array();
    /*
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
     * site başlığı
     *
     * String
     */
    public $title = 'Foto galeri';
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
     * şifre bilgisini tutan değişken
     *
     * String
     */
    private $sifre = '123456';

    /*
     * Galerş ile ilgili bilgileri değişkenlere aktarır
     *
     * @return Array
     */
    private function setGaleri()
    {
        $this->xmlObj = new SimpleXMLElement($this->xmlDosya, null, true);
        $this->title = $this->xmlObj->title;
        //$this->xmlObj->;
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
     *
     * @param String $sutunId Resmin bulunduğu sutun numarası
     * @param String $resimId Resmin numarası
     */
    public function resimyukle($sutunId, $resimId)
    {
        $this->xmlObj = new SimpleXMLElement($this->xmlDosya, null, true);
        if (empty($this->xmlObj->sutun)) { //eğer hiç sutun yoksa birinci sutun ve birinci resimi ekliyor
            $this->xmlObj->addChild('sutun'); //birinci sutun eklendi
            $this->xmlObj->sutun->addAttribute('id', $sutunId); //birinci sutuna id verildi
            $this->xmlObj->sutun->addChild('resim', 'images/4-1.jpg'); //birinci resim eklendi
            $this->xmlObj->sutun->resim->addAttribute('id', $resimId); //birinci resime id verildi
        } else { //eğer sutun var ise
            foreach ($this->xmlObj->sutun as $sutun) {// sutun id leri $idler dizisine yazılıyor
                $idler[] = (string)$sutun[id];
            }
            if (in_array($sutunId, $idler)) {//Eğer gelen sutun id $idler dizisinde varsa yani var olan bir sutunsa
                foreach ($this->xmlObj->sutun as $sutun) {// idleri kontrol  etmek için sutunlar döndürülüyor
                    if ((string)$sutun[id] == $sutunId) {// gelen sutunid ye sahip olan sutun bulunuyor
                        $sutun->addChild('resim', 'images/4-1.jpg');//sutuna resim ekleniyor
                        foreach ($sutun->resim as $resim) {//sutun içindeki resimlerin idleri döndürülüyor
                            if (empty($resim[id])) {//id si olmayan resme id ekleniyor yani yeni resme
                                $resim->addAttribute('id', $resimId);
                            }
                        }
                    }
                }
            } else {//eğer var olmayan bir sutunsa
                $this->xmlObj->addChild('sutun', '');//yeni sutun ekleniyor
                foreach ($this->xmlObj->sutun as $sutun) {// idleri kontrol  etmek için sutunlar döndürülüyor
                    if (empty($sutun[id])) {//id si olmayan yani  yeni sutun bulunuyor
                        $sutun->addAttribute('id', $sutunId);// yeni sutuna id veriliyor
                        $sutun->addChild('resim', 'images/4-1.jpg');//resim elkeniyor
                        $sutun->resim->addAttribute('id', $resimId);//resme id veriliyor
                    }
                }
            }
        }
        $this->kaydet();//işlemler sononda değişiklikler dosyaya kaydediliyor
    }
}