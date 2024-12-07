<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proxy Request Debugger</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        pre { background-color: #f4f4f4; padding: 10px; overflow-x: auto; }
        textarea { width: 100%; height: 300px; }
    </style>
</head>
<body>
    <h1>Proxy Request Debugger</h1>
    <form method="POST" action="">
        <label for="url">URL:</label><br>
        <input type="text" id="url" name="url" placeholder="Enter the URL" required style="width: 100%; padding: 8px;" value="https://www.bing.com/maps/"><br><br>
        
        <label for="proxy">Proxy (IP:Port):</label><br>
        <input type="text" id="proxy" name="proxy" placeholder="Enter proxy (optional)" style="width: 100%; padding: 8px;"><br><br>
        
        <label for="method">Request Method:</label><br>
        <select id="method" name="method" style="width: 100%; padding: 8px;">
            <option value="GET">GET</option>
            <option value="POST">POST</option>
        </select><br><br>
        
        <label>Debug Options:</label><br>
        <input type="checkbox" id="show_headers" name="show_headers" value="1" checked>
        <label for="show_headers">Show Request Headers</label><br>
        <input type="checkbox" id="show_info" name="show_info" value="1" checked>
        <label for="show_info">Show cURL Transfer Info</label><br><br>
        
        <button type="submit" style="padding: 10px 20px; font-size: 16px;">Send Request</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $url = $_POST['url'];
        $proxy = $_POST['proxy'];
        $method = $_POST['method'];
        $show_headers = isset($_POST['show_headers']);
        $show_info = isset($_POST['show_info']);

        $ch = curl_init();
        
        // Set basic cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_ENCODING, '');  // Handle compressed responses
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0); // Force HTTP/2
        
        // SSL Options - Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, 0);
        
        // Set proxy and timeouts
        if (!empty($proxy)) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); // Changed back to HTTP
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 180);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_TCP_KEEPALIVE, 1);
            
            // Additional proxy SSL options
            curl_setopt($ch, CURLOPT_PROXY_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_PROXY_SSL_VERIFYHOST, 0);
        } else {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        }
        
        // Set request method
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
        }
        

        // Enhanced headers to exactly match the browser request
        $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Encoding: gzip, deflate, br, zstd',
            'Accept-Language: en-US,en;q=0.9',
            'Cache-Control: no-cache',
            'Sec-Ch-Ua: "Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
            'Sec-Ch-Ua-Arch: "arm"',
            'Sec-Ch-Ua-Bitness: "64"',
            'Sec-Ch-Ua-Full-Version: "131.0.6778.108"',
            'Sec-Ch-Ua-Full-Version-List: "Google Chrome";v="131.0.6778.108", "Chromium";v="131.0.6778.108", "Not_A Brand";v="24.0.0.0"',
            'Sec-Ch-Ua-Mobile: ?0',
            'Sec-Ch-Ua-Model: ""',
            'Sec-Ch-Ua-Platform: "macOS"',
            'Sec-Ch-Ua-Platform-Version: "14.6.1"',
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: none',
            'Sec-Fetch-User: ?1',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            'Priority: u=0, i',
            'Ect: 4g'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        // Capture response headers if requested
        if ($show_headers) {
            $response_headers = [];
            curl_setopt($ch, CURLOPT_HEADERFUNCTION, 
                function($curl, $header) use (&$response_headers) {
                    $len = strlen($header);
                    $header = trim($header);
                    if (!empty($header)) {
                        $response_headers[] = $header;
                    }
                    return $len;
                }
            );
        }
        
        // Execute request
        $response = curl_exec($ch);
        
        // Capture cURL info if requested
        $curl_info = $show_info ? curl_getinfo($ch) : null;
        
        // Check for errors
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            echo "<p style='color: red;'>cURL Error: $error</p>";
        } else {
            echo "<h2>Response:</h2>";
            
            // Show headers if requested
            if ($show_headers && !empty($response_headers)) {
                echo "<h3>Response Headers:</h3>";
                echo "<pre>" . htmlspecialchars(implode("\n", $response_headers)) . "</pre>";
            }
            
            // Show cURL info if requested
            if ($show_info && $curl_info) {
                echo "<h3>cURL Transfer Info:</h3>";
                echo "<pre>" . htmlspecialchars(print_r($curl_info, true)) . "</pre>";
            }
            
            // Display response with additional debug info
            echo "<h3>Response Body:</h3>";
            echo "<p>Response Length: " . strlen($response) . " bytes</p>";
            echo "<textarea readonly>" . htmlspecialchars($response) . "</textarea>";
            
            // Add raw response output for debugging
            echo "<h3>Raw Response (first 1000 bytes):</h3>";
            echo "<pre>" . htmlspecialchars(substr($response, 0, 1000)) . "</pre>";
        }
        
        // Close cURL session
        curl_close($ch);
    }
    ?>
</body>
</html>