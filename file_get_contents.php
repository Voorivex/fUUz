<?php

function fuzzUnicode() {
    for ($codePoint = 1; $codePoint <= 0x10FFFF; $codePoint++) {
        // Skip invalid UTF-16 surrogate ranges
        if ($codePoint >= 0xD800 && $codePoint <= 0xDFFF) {
            continue;
        }

        try {
            // Convert Unicode code point to character
            $fuzzChar = mb_chr($codePoint, 'UTF-8');

            // Use Unicode character in a GET parameter instead of IP
            $url = "http://127{$fuzzChar}0.0.1/";

            // Suppress warning & manually handle errors
            $response = @file_get_contents($url);

            // Check response
            if (strpos($response, "ok") !== false) {
                printf("Unicode Character: %s (U+%04X)\n", $fuzzChar, $codePoint);
            }
        } catch (Exception $e) {
            // Handle errors properly
            error_log("Error processing Unicode (U+{$codePoint}): " . $e->getMessage());
        }
    }
}

// Run the fuzzing function
fuzzUnicode();