<?php
trait Tools
{
    protected function showTest($name)
    {
        echo "\r\n" . '--------------------' . $name . '--------------------' . "\r\n";
    }
    
    protected function showTestCase($name)
    {
        echo "\r\n\r\n" . '====================' . $name . '====================' . "\r\n";
    }
    
    protected function convertToLatin($charIn)
    {
        switch ($charIn) {
            case "a": return "a";case "b": return "b";case "c": return "c";
            case "d": return "d";case "e": return "e";case "f": return "f";
            case "g": return "g";case "h": return "h";case "i": return "i";
            case "j": return "j";case "k": return "k";case "l": return "l";
            case "m": return "m";case "n": return "n";case "o": return "o";
            case "p": return "p";case "q": return "q";case "r": return "r";
            case "s": return "s";case "t": return "t";case "u": return "u";
            case "v": return "v";case "w": return "w";case "x": return "x";
            case "y": return "y";case "z": return "z";case "~": return "~";
            case "�": return "a";case "�": return "b";case "�": return "v";
            case "�": return "g";case "�": return "d";case "�": return "e";
            case "�": return "yo";case "�": return "zh";case "�": return "z";
            case "�": return "i";case "�": return "i";case "�": return "k";
            case "�": return "l";case "�": return "m";case "�": return "n";
            case "�": return "o";case "�": return "p";case "�": return "r";
            case "�": return "s";case "�": return "t";case "�": return "u";
            case "�": return "f";case "�": return "h";case "�": return "ts";
            case "�": return "ch";case "�": return "sh";case "�": return "tsh";
            case "�": return "";case "�": return "y";case "�": return "";
            case "�": return "e";case "�": return "yu";case "�": return "ya";
            case "0": return "0";case "1": return "1";case "2": return "2";
            case "3": return "3";case "4": return "4";case "5": return "5";
            case "6": return "6";case "7": return "7";case "8": return "8";
            case "9": return "9";case "-": return "-";case "�": return "-";
            case "�": return "-";case "�": return "";case "�": return "";
            case "'": return "";case '"': return "";case "`": return "";
            case ".": return "";case ",": return "";case ";": return "";
            case ":": return "";case "!": return "";case "?": return "";
            case "(": return "";case ")": return "";default: return " ";
        }
    }
    
    protected function transliterate($strIn)
    {
        //$tr = Transliterator::create("Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();");
        //return $tr->transliterate($str);
        $strOut = "";
        for ($i = 0, $strInLen = strlen($strIn); $i < $strInLen; $i++) {
            $strOut .= $this->convertToLatin(strtolower(substr($strIn, $i, 1)));
        }
        return $strOut;
    }
}
