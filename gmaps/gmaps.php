<?php
// Enable output buffering with chunk size 0
ob_implicit_flush(true);
ob_end_flush();
ini_set('memory_limit', '256M');

// Set headers for real-time streaming
header('Content-Type: application/json');
header('X-Accel-Buffering: no');
header('Cache-Control: no-cache');

if (function_exists('ignore_user_abort')) {
    ignore_user_abort(false);
}

function streamOutput($data) {
    if (connection_aborted()) {
        exit;
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
    echo str_pad('', 4096);
    flush();
}

function extractSocialHandle($url, $platform) {
    $parts = explode($platform, $url);
    if (count($parts) > 1) {
        $handle = explode('/', $parts[1])[0];
        return trim($handle, '/');
    }
    return '';
}
class ProxyManager {
    private $workingProxies = [];
    private $currentIndex = 0;
    private $proxyTestUrl = 'https://www.bing.com';
    private $proxyTimeout = 5;
    private $useCustomProxies = false;
    private $customProxyList = '';

    public function __construct($useCustomProxies = false, $customProxyList = '') {
        $this->useCustomProxies = $useCustomProxies;
        $this->customProxyList = $customProxyList;
        $this->fetchAndTestProxies();
    }

    private function fetchProxies() {
        if ($this->useCustomProxies) {
            // Handle custom proxy list
            $proxies = array_filter(explode("\n", $this->customProxyList));
            return array_map('trim', $proxies);
        }

        // Default GitHub proxy sources
        $sources = [
            'https://raw.githubusercontent.com/TheSpeedX/PROXY-List/master/http.txt',
            'https://raw.githubusercontent.com/ShiftyTR/Proxy-List/master/http.txt',
            'https://raw.githubusercontent.com/clarketm/proxy-list/master/proxy-list-raw.txt'
        ];

        $proxies = [];
        foreach ($sources as $source) {
            try {
                $content = @file_get_contents($source);
                if ($content) {
                    $lines = explode("\n", trim($content));
                    $proxies = array_merge($proxies, $lines);
                }
            } catch (Exception $e) {
                streamOutput([
                    'status' => 'proxy_fetch_error',
                    'message' => "Error fetching proxies from $source: " . $e->getMessage()
                ]);
            }
        }
        return array_unique(array_filter($proxies));
    }

    private function testProxy($proxy) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->proxyTestUrl);
        curl_setopt($ch, CURLOPT_PROXY, trim($proxy));
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->proxyTimeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $startTime = microtime(true);
        $response = curl_exec($ch);
        $endTime = microtime(true);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $speed = round($endTime - $startTime, 2);
        
        // New checks for response content
        $isWorking = false;
        if ($response !== false && $httpCode == 200) {
            // Check if response is empty
            if (!empty($response)) {
                // Check for invalid response indicators
                if (strpos($response, 'HTTP_UPGRADE-INSECURE-REQUESTS') === false) {
                    // Check for valid HTML response
                    if (strpos($response, '<!DOCTYPE html>') !== false) {
                        $isWorking = true;
                    }
                }
            }
        }

        curl_close($ch);

        return [
            'working' => $isWorking,
            'speed' => $speed,
            'error' => $error
        ];
    }

    public function fetchAndTestProxies() {
        streamOutput([
            'status' => 'proxy_testing',
            'message' => $this->useCustomProxies ? 'Testing custom proxy list...' : 'Fetching proxy list from sources...'
        ]);

        $proxies = $this->fetchProxies();
        $testedCount = 0;
        
        foreach ($proxies as $proxy) {
            // For custom proxies, test all of them
            // For GitHub proxies, stop after finding 3 working ones
            if (!$this->useCustomProxies && count($this->workingProxies) >= 3) {
                break;
            }
            
            $testedCount++;
            streamOutput([
                'status' => 'proxy_testing',
                'message' => "Testing proxy $testedCount/" . count($proxies) . ": $proxy"
            ]);

            $result = $this->testProxy($proxy);
            
            if ($result['working']) {
                $this->workingProxies[] = $proxy;
                streamOutput([
                    'status' => 'proxy_success',
                    'message' => "Found working proxy: $proxy (Response time: {$result['speed']}s)"
                ]);
            }
        }

        if (empty($this->workingProxies)) {
            streamOutput([
                'status' => 'proxy_error',
                'message' => $this->useCustomProxies ? 
                    "No working proxies found in your custom list. Please check the format and try again." :
                    "No working proxies found. Proceeding without proxy."
            ]);
        } else {
            streamOutput([
                'status' => 'proxy_complete',
                'message' => "Found " . count($this->workingProxies) . " working " . 
                    ($this->useCustomProxies ? "custom " : "") . "proxies"
            ]);
        }
    }
    public function getNextProxy() {
        if (empty($this->workingProxies)) return null;
        
        $proxy = $this->workingProxies[$this->currentIndex];
        $this->currentIndex = ($this->currentIndex + 1) % count($this->workingProxies);
        return $proxy;
    }

    public function hasWorkingProxies() {
        return !empty($this->workingProxies);
    }
}

function getMedicalCenters($query, $page = 1, $count = 100, $useProxy = false, $proxyManager = null) {
    $maxRetries = 3;
    $attempt = 0;
    $lastError = '';

    while ($attempt < $maxRetries) {
        $ch = curl_init();
        
        // Construct URL with proper encoding
        $params = http_build_query([
            'q' => $query,
            'p1' => '',
            'count' => $count,
            'first' => $page
        ]);
        $url = "https://www.bing.com/maps/overlaybfpr?" . $params;
        
        // Basic curl options
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => 'gzip, deflate, br',
            CURLOPT_HEADER => false,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            CURLOPT_HTTPHEADER => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language: en-US,en;q=0.5',
                'Accept-Encoding: gzip, deflate, br',
                'Connection: keep-alive',
                'Referer: https://www.bing.com/maps',
                'Origin: https://www.bing.com',
                'Host: www.bing.com',
                'Sec-Fetch-Dest: empty',
                'Sec-Fetch-Mode: cors',
                'Sec-Fetch-Site: same-origin',
                'TE: trailers'
            ]
        ]);
        
        if ($useProxy && $proxyManager && $proxyManager->hasWorkingProxies()) {
            $proxy = $proxyManager->getNextProxy();
            if ($proxy) {
                curl_setopt($ch, CURLOPT_PROXY, trim($proxy));
                curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
                curl_setopt($ch, CURLOPT_PROXYHEADER, [
                    'Connection: Keep-Alive',
                    'Proxy-Connection: Keep-Alive'
                ]);
                
                streamOutput([
                    'status' => 'proxy_info',
                    'message' => "Attempt " . ($attempt + 1) . "/$maxRetries: Using proxy: $proxy"
                ]);
            }
        }

        streamOutput([
            'status' => 'searching',
            'message' => "Finding businesses on page {$page}...",
            'page' => $page
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);

        // Debug info
        streamOutput([
            'status' => 'connection_info',
            'message' => "Response Code: $httpCode - Content Type: {$info['content_type']} - Size: {$info['size_download']} bytes - Error: " . ($error ?: 'None') . " - Attempt: " . ($attempt + 1)
        ]);

        curl_close($ch);

        if ($response !== false && $httpCode === 200) {
            $doc = new DOMDocument();
            @$doc->loadHTML(mb_convert_encoding($response, 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new DOMXPath($doc);
            
            $names = [];
            $listings = $xpath->query("//div[contains(@class, 'b_vPanel')]/div[1]");
            
            $totalResults = $xpath->query("//span[@class='listCount']");
            $totalCount = 0;
            if ($totalResults->length > 0) {
                $totalText = $totalResults->item(0)->textContent;
                preg_match('/\d+/', $totalText, $matches);
                $totalCount = isset($matches[0]) ? (int)$matches[0] : 0;
            }
            
            foreach ($listings as $listing) {
                $name = trim($listing->textContent);
                if (!empty($name)) {
                    $names[] = $name;
                }
            }
            
            $totalPages = ceil($totalCount / $count);
            if ($totalPages == 0 && count($names) > 0) {
                $totalPages = 1;
            }
            
            streamOutput([
                'status' => 'found',
                'count' => count($names),
                'total_count' => $totalCount,
                'current_page' => $page,
                'total_pages' => $totalPages,
                'message' => sprintf(
                    'Found %d businesses on page %d of %d (Total: %d businesses)',
                    count($names),
                    $page,
                    $totalPages,
                    $totalCount
                )
            ]);
            
            return $names;
        } else {
            $lastError = $error;
            $attempt++;
            usleep(2 ** $attempt * 1000000); // Exponential backoff
        }
    }

    streamOutput([
        'status' => 'error',
        'message' => "Request failed after $maxRetries attempts. Last error: " . ($lastError ?: 'None')
    ]);
    return [];
}
function getDetailedInfo($name, $useProxy = false, $proxyManager = null) {
    streamOutput([
        'status' => 'processing',
        'message' => 'Getting details for: ' . $name
    ]);

    $ch = curl_init();
    $url = "https://www.bing.com/maps/overlaybfpr?q=" . urlencode($name) . "&count=1";
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    if ($useProxy && $proxyManager && $proxyManager->hasWorkingProxies()) {
        $proxy = $proxyManager->getNextProxy();
        if ($proxy) {
            curl_setopt($ch, CURLOPT_PROXY, trim($proxy));
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            
            streamOutput([
                'status' => 'proxy_info',
                'message' => "Using proxy: $proxy for details fetch"
            ]);
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false || $httpCode !== 200) {
        streamOutput([
            'status' => 'error',
            'message' => "Failed to get details: " . ($error ? $error : "HTTP Code: $httpCode")
        ]);
        return null;
    }

    $doc = new DOMDocument();
    @$doc->loadHTML(mb_convert_encoding($response, 'HTML-ENTITIES', 'UTF-8'));
    $xpath = new DOMXPath($doc);

    $info = [
        'status' => 'result',
        'data' => [
            'name' => $name,
            'address' => '',
            'phone' => '',
            'website' => '',
            'social' => [],
            'hours' => ''
        ]
    ];

    // Get address from data attributes
    $addressNode = $xpath->query("//div[contains(@data-facts, 'addressFields')]");
    if ($addressNode->length > 0) {
        $factsData = $addressNode->item(0)->getAttribute('data-facts');
        $facts = json_decode($factsData, true);
        if ($facts && isset($facts['addressFields'])) {
            $addressParts = array_filter([
                $facts['addressFields']['addressLine'] ?? '',
                $facts['addressFields']['city'] ?? '',
                $facts['addressFields']['stateMunicipality'] ?? ''
            ]);
            $info['data']['address'] = implode(', ', $addressParts);
        }
    }

    // Get phone number from data attributes
    $phoneNode = $xpath->query("//div[contains(@data-itineraryfacts, 'PhoneNumber')]");
    if ($phoneNode->length > 0) {
        $factsData = $phoneNode->item(0)->getAttribute('data-itineraryfacts');
        $facts = json_decode($factsData, true);
        if ($facts && isset($facts['PhoneNumber'])) {
            $info['data']['phone'] = $facts['PhoneNumber'];
        }
    }

    // Get website from data attributes
    $websiteNode = $xpath->query("//a[contains(@href, 'alink/link')]");
    if ($websiteNode->length > 0) {
        $href = $websiteNode->item(0)->getAttribute('href');
        parse_str(parse_url($href, PHP_URL_QUERY), $params);
        if (isset($params['url'])) {
            $info['data']['website'] = urldecode($params['url']);
        }
    }

    // Get social media links from data attributes
    $socialLinks = $xpath->query("//div[contains(@data-facts, 'WebResources')]");
    if ($socialLinks->length > 0) {
        $factsData = $socialLinks->item(0)->getAttribute('data-facts');
        $facts = json_decode($factsData, true);
        if ($facts && isset($facts['WebResources'])) {
            $resources = base64_decode($facts['WebResources']);
            if (strpos($resources, 'facebook.com') !== false) {
                $info['data']['social']['facebook'] = 'https://facebook.com/' . extractSocialHandle($resources, 'facebook.com');
            }
            if (strpos($resources, 'instagram.com') !== false) {
                $info['data']['social']['instagram'] = 'https://instagram.com/' . extractSocialHandle($resources, 'instagram.com');
            }
        }
    }

    // Get opening hours from data attributes
    $hoursNode = $xpath->query("//div[contains(@data-itineraryfacts, 'OpenHours')]");
    if ($hoursNode->length > 0) {
        $factsData = $hoursNode->item(0)->getAttribute('data-itineraryfacts');
        $facts = json_decode($factsData, true);
        if ($facts && isset($facts['OpenHours'])) {
            $info['data']['hours'] = $facts['OpenHours'];
        }
    }

    return $info;
}

// Get query parameters
$query = isset($_GET['query']) ? $_GET['query'] : 'guess';
$page = isset($_GET['first']) ? (int)$_GET['first'] : 1;
$count = isset($_GET['count']) ? (int)$_GET['count'] : 100;
$useProxy = isset($_GET['proxy']) && $_GET['proxy'] === '1';
$useCustomProxy = isset($_GET['customProxy']) && $_GET['customProxy'] === '1';
$customProxyList = isset($_GET['proxyList']) ? $_GET['proxyList'] : '';

// Main execution
try {
    $proxyManager = null;
    if ($useProxy || $useCustomProxy) {
        $proxyManager = new ProxyManager($useCustomProxy, $customProxyList);
    }

    $centers = getMedicalCenters($query, $page, $count, ($useProxy || $useCustomProxy), $proxyManager);
    
    if (empty($centers)) {
        streamOutput([
            'status' => 'error',
            'message' => 'No results found for: ' . $query . ' on page ' . $page
        ]);
        exit;
    }

    $total = count($centers);
    $current = 0;

    foreach ($centers as $center) {
        if (connection_aborted()) {
            exit;
        }
        
        $current++;
        $detailedInfo = getDetailedInfo($center, ($useProxy || $useCustomProxy), $proxyManager);
        
        if ($detailedInfo) {
            $detailedInfo['progress'] = [
                'current' => $current,
                'total' => $total,
                'current_page' => $page,
                'percentage' => round(($current / $total) * 100)
            ];
            streamOutput($detailedInfo);
        }
        
        usleep(500000); // 0.5 second delay between requests
    }

    streamOutput([
        'status' => 'complete',
        'message' => sprintf(
            'Completed page %d results for: %s',
            $page,
            $query
        ),
        'current_page' => $page
    ]);

} catch (Exception $e) {
    streamOutput([
        'status' => 'error',
        'message' => $e->getMessage(),
        'page' => $page
    ]);
}
?>