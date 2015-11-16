<?php

/**
 * The Validation class for Turkish Identification (tcKimlikNo) Number using Web Service
 *
 * This class first checks if the tcKimlikNo passes the validation algorithm:
 * tcKimlikNo is a number with 11 digits.
 * First digit can't be 0.
 * Sum of 1, 3, 5, 7 and 9th digits are multiplied by 7. And sum of 2, 4, 6, and 8th digits are subtracted
 * from this value. The modulus 10 of this value is the tenth digit.
 * Sum of 1, 2, 3, 4, 5, 6, 7, 8, 9, and 10th digit's modulus 10 is the eleventh digit.
 *
 * After the validation, class connects to the Turkish State's Identification Authority and checks using a soap client.
 *
 * Usage:
 *
 * $kimlik = new Midori\TCKimlik("12345678910");
 *
 *  Validate: $kimlik->validate();
 *  Ask to state: $kimlik->askToState($name,$familyName,$birthYear);
 *
 *
 * @author     Midori Kocak <mtkocak@mtkocak.net>
 */

namespace Midori;

class TCKimlik{

    /**
     * Turkish Identity Number
     *
     * @param int
     */
    private $tcIdentificationNo;

    /**
     * Constructor function checks if it has eleven digits or not
     *
     * @param int $tcIdentificationNo
     */
    public function __construct($tcIdentificationNo){

        if((int)log($tcIdentificationNo,10)!=10){
            return false;
        }

        $this->tcIdentificationNo = $tcIdentificationNo;
    }

    /**
     * Turkish uppercase function due to "İ" and "i" clash.
     * 
     * @see http://php.net/manual/en/ref.mbstring.php
     * 
     * @param string $str
     * @param string $encoding 
     * @return string
     */
    function strtoupperTR($str)
    {
        return mb_strtoupper($str,"UTF-8");
    }

    /**
     * Validation function based on algorithm.
     *
     * @return bool
     */
    public function validate()
    {
        $oddSum = 0;
        $evenSum = 0;
        if(substr($this->tcIdentificationNo,0,1)==0)
        {
            return false;
        }
        for($i=0;$i<=8;$i++)
        {
            if($i%2==0)
            {
                $oddSum += $this->tcIdentificationNo[$i];
            }
            else{
                $evenSum += $this->tcIdentificationNo[$i];
            }
        }
        $tenthDigit = ((($oddSum*7) - $evenSum)+10) % 10;
        if($tenthDigit!=$this->tcIdentificationNo[9])
        {
            return false;
        }

        $eleventhDigit = ($oddSum + $evenSum + $tenthDigit) % 10;

        if($eleventhDigit!=$this->tcIdentificationNo[10])
        {
            return false;
        }
        return true;
    }

    /**
     * State confirmation of the Turkish Identity Number
     *
     * Soap Client for checking the Turkish Identity Number communicating with the Turkish Identification identity.
     * All of string parameters has to include exact turkish characters, otherwise it returns false.
     *
     * @param string $name Name on Identity card, has to include the middle name
     * @param string $surname Name on Identity card.
     * @param int $birthYear Birth year on Identity Card
     * @return bool
     */
    public function askToState($name,$surname,$birthYear)
    {
        if(!$this->validate())
        {
            return false;
        }
        $client = new SoapClient('https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx?WSDL');
        $result = $client->TcKimlikNoDogrula(array('TCKimlikNo'=>$this->tcIdentificationNo, 'Ad'=>$this->strtoupperTR($name), 'Soyad'=>$this->strtoupperTR($surname), 'DogumYili'=>$birthYear));
        return $result->TCKimlikNoDogrulaResult;
    }
}
