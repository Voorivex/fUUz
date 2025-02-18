import requests
from urllib.parse import quote
import idna
import unicodedata

def fuzz_unicode():
    for code_point in range(1, 0x10FFFF + 1):
        # Skip invalid UTF-16 surrogate ranges
        if 0xD800 <= code_point <= 0xDFFF:
            continue

        try:
            # Convert Unicode code point to character
            fuzz_char = chr(code_point)

            # Skip control characters and other invalid characters
            if unicodedata.category(fuzz_char).startswith('C'):
                continue

            # Encode the Unicode character for use in URL
            encoded_char = quote(fuzz_char)

            # Use Unicode character in a GET parameter instead of IP
            domain = f"{encoded_char}27.0.0.1"
            try:
                # Encode domain using IDNA
                encoded_domain = idna.encode(domain).decode('ascii')
            except idna.IDNAError:
                # Skip if domain cannot be encoded
                continue

            url = f"http://{encoded_domain}/"

            # Send HTTP GET request
            response = requests.get(url, timeout=2)

            # Check response
            if "ok" in response.text:
                print(f"Unicode Character: {fuzz_char} (U+{code_point:04X}) | status code: {response.status_code}")

        except requests.exceptions.RequestException as e:
            # Handle errors (e.g., network failures, encoding issues)
            # print(f"Error processing Unicode (U+{code_point:04X}): {e}")
            pass

# Run the fuzzing function
fuzz_unicode()