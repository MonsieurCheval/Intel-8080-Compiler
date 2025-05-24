<?php

class AltairTranslator {

//from assemble to binary
public static function hexProgramToBinary($hexProgram) {
    $hexProgram = strtoupper(trim($hexProgram));
    $bytes = preg_split('/\s+/', $hexProgram);
    $result = [];
    foreach ($bytes as $hex) {
        if ($hex === '') continue;
        $bin = self::hexToBinary($hex);
        if (strpos($bin, 'Invalid') === 0) return $bin; // error
        $result[] = $bin;
    }
    return implode(' ', $result);
}




















    public static function octalToHex($octalInput) {
        if (!preg_match('/^[0-7]+$/', $octalInput)) {
            return "Invalid octal input. Only digits 0–7 are allowed.";
        }
        return strtoupper(dechex(octdec($octalInput)));
    }

    public static function octalToBinary($octalInput) {
        if (!preg_match('/^[0-7]+$/', $octalInput)) {
            return "Invalid octal input. Only digits 0–7 are allowed.";
        }
        return str_pad(decbin(octdec($octalInput)), 8, '0', STR_PAD_LEFT);
    }

    public static function hexToOctal($hexInput) {
        if (!preg_match('/^[0-9A-Fa-f]+$/', $hexInput)) {
            return "Invalid hex input. Only digits 0–9 and A–F are allowed.";
        }
        return decoct(hexdec($hexInput));
    }

    public static function hexToBinary($hexInput) {
        if (!preg_match('/^[0-9A-Fa-f]+$/', $hexInput)) {
            return "Invalid hex input. Only digits 0–9 and A–F are allowed.";
        }
        return str_pad(decbin(hexdec($hexInput)), 8, '0', STR_PAD_LEFT);
    }

    public static function binaryToOctal($binaryInput) {
        if (!preg_match('/^[01]{8}$/', $binaryInput)) {
            return "Invalid binary input. Must be 8 bits (0 or 1).";
        }
        return decoct(bindec($binaryInput));
    }

    public static function binaryToHex($binaryInput) {
        if (!preg_match('/^[01]{8}$/', $binaryInput)) {
            return "Invalid binary input. Must be 8 bits (0 or 1).";
        }
        return strtoupper(str_pad(dechex(bindec($binaryInput)), 2, '0', STR_PAD_LEFT));
    }
}

// Example usage:
// echo AltairTranslator::hexToBinary("CD"); // Output: 11001101

?>
