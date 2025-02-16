
const axios = require('axios');

async function fuzzUnicode() {
    for (let codePoint = 0; codePoint <= 0x10FFFF; codePoint++) {
        // Skip invalid ranges
        if (codePoint >= 0xD800 && codePoint <= 0xDFFF) continue;

        const fuzzChar = String.fromCodePoint(codePoint);
        const targetIP = `${fuzzChar}27.0.0.1`;

        try {
            const response = await axios.get(`http://${targetIP}`, {
                timeout: 2000 // Set a timeout for the request
            });

            if (response.data.includes("ok")) {
                console.log(`Unicode Character: ${fuzzChar} (U+${codePoint.toString(16).toUpperCase()})`);
            }
        } catch (error) {
            // Handle expected errors like connection failure
            if (error.response) {
                // console.log(`Response Error for ${targetIP}: ${error.response.status}`);
            } else if (error.request) {
                // console.log(`No response received for ${targetIP}`);
            } else {
                // console.error(`Error with ${targetIP}: ${error.message}`);
            }
        }
    }
}

// Run the fuzzing function
fuzzUnicode();