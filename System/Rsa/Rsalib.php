<?php
/*
*-------------------------------------RSALib-----------------------------------*
*----    An implementation of the RSA (Rivest/Shamir/Adelman) algorithm    ----*
*----------                http://www.rsasecurity.com                ----------*
*------------------------------------------------------------------------------*
*------------------------------------------------------------------------------*
*----     This library is free software; you can redistribute it and/or    ----*
*----      modify it under the terms of the GNU Lesser General Public      ----*
*----     License as published by the Free Software Foundation; either     ----*
*----  version 2.1 of the License, or (at your option) any later version.  ----*
*----                                                                      ----*
*----   This library is distributed in the hope that it will be useful,    ----*
*----    but WITHOUT ANY WARRANTY; without even the implied warranty of    ----*
*----  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU   ----*
*----            Lesser General Public License for more details.           ----*
*----                                                                      ----*
*----   You should have received a copy of the GNU Lesser General Public   ----*
*----  License along with this library; if not, write to the Free Software ----*
*----        Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,        ----*
*----                          MA  02110-1301  USA                         ----*
*------------------------------------------------------------------------------*
*------------------------------------------------------------------------------*
*------                Copyright © 2005 Antonio Anzivino                 ------*
*------------------------------------------------------------------------------*
*/

/**
 * @example

    $keys = System_Rsa_Keygen::Generate();
    $rsa = new System_Rsa_Rsalib($keys['module'], $keys['public'], $keys['private']);
    $output = $rsa->encrypt("Строка с переносами\r\nи прочими гадостями!!!\r\n\";№№\"%№;?;*");
    $input = $rsa->decrypt($output);

 *
 */
class System_Rsa_Rsalib
{
    protected $n; //modulo
        protected $e; //public
        protected $d; //private

        /*
        CONSTRUCTOR
        Initializes the RSA Engine with given RSA Key Pair. You must have run
        keygen.php and obtained a valid RSA Key Pair
        */
    public function __construct($n = 0, $e = 0, $d = 0)
    {
        if ($n == 0 or $e == 0 or $d == 0) {
            list($n, $e, $d) = $this->generate_keys();
        }
        $this->n = $n;
        $this->e = $e;
        $this->d = $d;

        $test = "Test string to test correct key pairing";
        $enc = $this->encrypt($test, $e, $n);
        $dec = $this->decrypt($enc, $d, $n);
        if ($test === $dec) {
            return true;
        } else {
            return false;
        }
    }

    /*
    CONVERSIONS STRING-BINARY
    */
    public function bin2asc($temp)
    {
        $data = "";
        for ($i = 0; $i < strlen($temp) - 1 ; $i += 8) {
            $data .= chr(bindec(substr($temp, $i, 8)));
        }

        return $data;
    }

    public function asc2bin($temp)
    {
        $data = "";
        for ($i = 0; $i < $strlen($temp) - 1; $i++) {
            $data .= sprintf("%08b", ord($temp[$i]));
        }

        return $data;
    }


    /*
    MODULUS FUNCTION
    */
    public function mo($g, $l)
    {
        return $g - ($l * floor($g / $l));
    }
    /*
    RUSSIAN PAESANT method for exponentiation
    */
    public function powmod($base, $exp, $modulus)
    {
        $accum = 1;
        $i = 0;
        $basepow2 = $base;
        while (($exp >> $i) > 0) {
            if ((($exp >> $i) & 1) == 1) {
                $accum = $this->mo(($accum * $basepow2), $modulus);
            }
            $basepow2 = $this->mo(($basepow2 * $basepow2), $modulus);
            $i++;
        }

        return $accum;
    }

    /*
    ENCRYPT FUNCTION
    Returns X = M^E (mod N)

    Each letter in the message is represented as its ASCII code number - 30
    3 letters in each block with 1 in the beginning and end.
    For example string
    AAA
    will become
    13535351 (A = ASCII 65-30 = 35)
    we can build these blocks because the smalest prime available is 4507
    4507^2 = 20313049
    This means that
    1. Modulo N will always be < 19999991
    2. Letters > ASCII 128 must not occur in plain text message
    */
    public function encrypt($m)
    {
        //Checking against incompatible stream
        $m = base64_encode($m);
        for ($i = 0; $i < strlen($m) - 1; $i++) {
            if (ord($m[$i]) > 128) {
                return false;
            }
        }
        $coded = "";
        $asci = array();
        for ($i = 0; $i < strlen($m); $i += 3) {
            $tmpasci = "1";
            for ($h = 0; $h < 3; $h++) {
                if ($i + $h < strlen($m)) {
                    $tmpstr = ord(substr($m, $i + $h, 1)) - 30;
                    if (strlen($tmpstr) < 2) {
                        $tmpstr = "0".$tmpstr;
                    }
                } else {
                    break;
                }
                $tmpasci .= $tmpstr;
            }
            array_push($asci, $tmpasci."1");
        }

        //Each number is then encrypted using the RSA formula: block ^E mod N
        for ($k = 0; $k < count($asci); $k++) {
            $resultmod = $this->powmod($asci[$k], $this->e, $this->n);
            //$coded .= base_convert($resultmod,10,35)." ";
            $coded .= str_pad(dechex($resultmod), 7, '0', STR_PAD_LEFT);
        }

        return trim($coded);
    }

    /*
    DECRYPT FUNCTION
    M = X^D (mod N)
    */
    public function decrypt($c)
    {
        $deencrypt = '';
        $resultd = '';

            
        $decryptarray = explode('!', chunk_split($c, 7, '!'));
        for ($u = 0; $u < count($decryptarray); $u++) {
            if ($decryptarray[$u] == "") {
                array_splice($decryptarray, $u, 1);
            }
        }
        for ($u = 0; $u < count($decryptarray); $u++) {
            $resultmod = $this->powmod(hexdec($decryptarray[$u]), $this->d, $this->n);
            @$deencrypt .= substr($resultmod, 1, strlen($resultmod) - 2);
        }
        for ($u = 0; $u < strlen($deencrypt); $u += 2) {
            @$resultd .= chr(substr($deencrypt, $u, 2) + 30);
        }

        return base64_decode($resultd);
    }
}
