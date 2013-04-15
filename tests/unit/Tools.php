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
            case "à": return "a";case "á": return "b";case "â": return "v";
            case "ã": return "g";case "ä": return "d";case "å": return "e";
            case "¸": return "yo";case "æ": return "zh";case "ç": return "z";
            case "è": return "i";case "é": return "i";case "ê": return "k";
            case "ë": return "l";case "ì": return "m";case "í": return "n";
            case "î": return "o";case "ï": return "p";case "ð": return "r";
            case "ñ": return "s";case "ò": return "t";case "ó": return "u";
            case "ô": return "f";case "õ": return "h";case "ö": return "ts";
            case "÷": return "ch";case "ø": return "sh";case "ù": return "tsh";
            case "ú": return "";case "û": return "y";case "ü": return "";
            case "ý": return "e";case "þ": return "yu";case "ÿ": return "ya";
            case "0": return "0";case "1": return "1";case "2": return "2";
            case "3": return "3";case "4": return "4";case "5": return "5";
            case "6": return "6";case "7": return "7";case "8": return "8";
            case "9": return "9";case "-": return "-";case "—": return "-";
            case "–": return "-";case "«": return "";case "»": return "";
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
