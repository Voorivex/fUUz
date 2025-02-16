<?php

function fuzzUnicode() {
    for ($codePoint = 1; $codePoint <= 0x10FFFF; $codePoint++) {
        // Skip invalid UTF-16 surrogate ranges
        if ($codePoint >= 0xD800 && $codePoint <= 0xDFFF) {
            continue;
        }

        // Convert Unicode code point to character
        try {
            $fuzzChar = mb_chr($codePoint, 'UTF-8');

            // Use Unicode character in a GET parameter instead of IP
            $url = "http://{$fuzzChar}27.0.0.1/";

            // Initialize cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 2); // 2 seconds timeout

            $response = curl_exec($ch);
            if ($response === false) {
                throw new Exception("cURL request failed: " . curl_error($ch));
            }

            curl_close($ch);

            // Check response
            if (strpos($response, "ok") !== false) {
                printf("Unicode Character: %s (U+%04X)\n", $fuzzChar, $codePoint);
            }
        } catch (Exception $e) {
            // Handle errors (e.g., network failures, encoding issues)
            // error_log("Error processing Unicode (U+{$codePoint}): " . $e->getMessage());
        }
    }
}

// Run the fuzzing function
fuzzUnicode();