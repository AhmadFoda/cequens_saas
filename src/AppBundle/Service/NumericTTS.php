<?php

namespace AppBundle\Service;
//
// +-----------------------------------+
// |         Numeric TTS v 1.0         |
// |      http://www.SysTurn.com       |
// +-----------------------------------+
//
//
//   This program is free software; you can redistribute it and/or modify
//   it under the terms of the ISLAMIC RULES and GNU Lesser General Public
//   License either version 2, or (at your option) any later version.
//
//   ISLAMIC RULES should be followed and respected if they differ
//   than terms of the GNU LESSER GENERAL PUBLIC LICENSE
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of the license with this software;
//   If not, please contact support @ S y s T u r n .com to receive a copy.
//

use Symfony\Component\DependencyInjection\ContainerInterface;

class NumericTTS
{
    var $voice = 'male';
    var $len = 0;
    protected $serviceContainer;
    protected  $rootDir;

    /**
     * NumericTTS constructor.
     */
    public function __construct(ContainerInterface $serviceContainer, $rootDirectory)
    {
        $this->serviceContainer = $serviceContainer;
        $this->rootDir = $rootDirectory;
    }


    function setVoice($voice)
    {
        if (is_dir($this->rootDir . '/voices/' . $voice . '/')) {
            $this->voice = $voice;
            return true;
        } else {
            return false;
        }
    }

    function header()
    {
        $len = pack('S*', $this->len);
        $len .= pack('@1');

        $header = pack('N*', 0x52494646);
        $header .= pack('C*', 0x8C);
        $header .= $len;
        $header .= pack('N*', 0x57415645);
        $header .= pack('N*', 0x666D7420);
        $header .= pack('N*', 0x10000000);
        $header .= pack('N*', 0x01000100);
        $header .= pack('N*', 0x401F0000);
        $header .= pack('N*', 0x401F0000);
        $header .= pack('N*', 0x01000800);
        $header .= pack('N*', 0x64617461);
        $header .= pack('C*', 0x68);
        $header .= $len;

        return $header;
    }

    function generate($number)
    {
        //$number .= '-';

        $this->len = 0;

        $output = '';

        for ($i = 0, $len = strlen($number); $i < $len; $i++) {
            $num = strtolower(substr($number, $i, 1));
            if ('-' == $num || ' ' == $num)
                $file = $this->rootDir . '/voices/-.wav';
            else
                $file = $this->rootDir . '/voices/' . $this->voice . '/' . $num . '.wav';
            //echo $file;
            if (!is_file($file)) continue;
            $fp = @fopen($file, 'rb');
            if ($fp) {
                $output .= fread($fp, filesize($file));
                fclose($fp);
            }
        }

        $this->len = (int)(strlen($output) / 260);

        return $this->header() . $output;
    }

    function output($number)
    {
        $output = $this->generate($number);
        header('Expires: 0');
        header('Pragma: no-cache');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Type: audio/x-wav');
        header('Content-Length: ' . strlen($output));
        echo $output;
        exit;
    }

    function write($number, $file)
    {
        $fp = fopen($file, 'wb');
        fwrite($fp, $this->generate($number));
        fclose($fp);
    }

    function play($file)
    {
        $this->len = 0;

        $output = '';
        $fp = @fopen($file, 'rb');
        if ($fp) {
            $output .= fread($fp, filesize($file));
            fclose($fp);
        }

        $this->len = (int)(strlen($output) / 260);

        $output =  $this->header() . $output;

        header('Expires: 0');
        header('Pragma: no-cache');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Type: audio/x-wav');
        header('Content-Length: ' . strlen($output));
        echo $output;
        exit;

    }
}

?>