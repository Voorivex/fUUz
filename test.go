package main

import (
        "bytes"
        "fmt"
        "io/ioutil"
        "net/http"
        "unicode/utf8"
)

func fuzzUnicode() {
        for codePoint := 1; codePoint <= 0x10FFFF; codePoint++ {
                // Skip invalid UTF-16 surrogate ranges
                if codePoint >= 0xD800 && codePoint <= 0xDFFF {
                        continue
                }

                // Convert Unicode code point to character
                fuzzChar := string(rune(codePoint))

                // Use Unicode character in a GET parameter instead of IP
        //  Note: It's HIGHLY unusual to put unicode characters directly into an IP address.  
        //        This code assumes you have a local server setup to handle this.
                url := fmt.Sprintf("http://%s27.0.0.1/", fuzzChar)

                // Make HTTP GET request
                response, err := http.Get(url)
                if err != nil {
                        // Handle errors properly, but don't print every error if you get lots
            //      It's likely many unicode points will cause errors.
                        // fmt.Printf("Error processing Unicode (U+%04X): %v\n", codePoint, err)  // Commented out to reduce output
                        continue
                }
                defer response.Body.Close()

                // Read response body
                body, err := ioutil.ReadAll(response.Body)
                if err != nil {
            //      Same as above, likely many errors here.
                        // fmt.Printf("Error reading response body for Unicode (U+%04X): %v\n", codePoint, err) // Commented out
                        continue
                }

                // Check response.  Only print if BOTH are true.
                if utf8.Valid(body) && bytes.Contains(body, []byte("ok")) {
                        fmt.Printf("Valid Unicode Character: %s (U+%04X) - Response: %s\n", fuzzChar, codePoint, body)
                }
        }
}

func main() {
        // Run the fuzzing function
        fuzzUnicode()
}
