<?php
// Enable output buffering and error reporting
ob_start();
ob_implicit_flush(true);
error_reporting(E_ALL);
ini_set('display_errors', 1);

class EmailScraper {
    private $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.82 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Edge/88.0.705.56 Safari/537.36'
    ];

    private $dorks = [
        // Plain text searches
        '@{domain}',
        'email {domain}',
        'contact {domain}',
        'mail {domain}',
        'email address {domain}',
        'contact information {domain}',
        
        // Basic Google dorks
        'site:{domain} "@{domain}"',
        'site:{domain} "email"',
        'site:{domain} "contact us"',
        'site:{domain} "get in touch"',
        'site:{domain} "send us an email"',
        'site:{domain} "email us"',
        
        // Google-specific advanced operators
        'allintext: email @{domain}',
        'allintext: contact @{domain}',
        'allinurl:contact site:{domain}',
        'allinurl:email site:{domain}',
        'allintitle: contact {domain}',
        'related:{domain} "email"',
        
        // Existing dorks...
        'inbody:"@{domain}"',
        'instreamset:(url title):"@{domain}"',
        'contains:"@{domain}"',
        
        // Advanced Bing-specific dorks...
        'inanchor:"@{domain}"',
        'inbody:"email" AND "@{domain}"',
        'inbody:"mail" AND "@{domain}"',
        'inbody:"contact" AND "@{domain}"',
        'inbody:"contact us" AND "@{domain}"',
        'inbody:"contact-us" AND "@{domain}"',
        
        // Common email patterns
        '"@{domain}"',
        '"mailto:*@{domain}"',
        'email:*@{domain}',
        'mail:*@{domain}',
        
        // Obfuscated patterns
        'inbody:"[at] {domain}"',
        'inbody:"(at) {domain}"',
        'inbody:"[dot] {domain}"',
        'inbody:"{domain} [dot]"',
        '"[at] {domain}"',
        '"(at) {domain}"',
        
        // Department combinations
        'inbody:("email" OR "contact" OR "mail") AND ("hr" OR "human resources") AND "@{domain}"',
        'inbody:("email" OR "contact" OR "mail") AND ("sales" OR "marketing") AND "@{domain}"',
        'inbody:("email" OR "contact" OR "mail") AND ("support" OR "help") AND "@{domain}"',
        'inbody:("email" OR "contact" OR "mail") AND ("press" OR "media") AND "@{domain}"',
        'inbody:("email" OR "contact" OR "mail") AND ("info" OR "contact") AND "@{domain}"',
        
        // File type searches
        'filetype:pdf AND "@{domain}"',
        'filetype:doc OR filetype:docx AND "@{domain}"',
        'filetype:xls OR filetype:xlsx AND "@{domain}"',
        'filetype:txt AND "@{domain}"',
        'filetype:csv AND "@{domain}"',
        
        // Common pages
        'site:{domain} AND (inbody:"contact" OR inbody:"email" OR inbody:"get in touch")',
        'site:{domain}/contact',
        'site:{domain}/about',
        'site:{domain}/team',
        'site:{domain}/staff',
        
        // Social and professional networks
        'site:linkedin.com AND "@{domain}"',
        'site:github.com AND "@{domain}"',
        'site:gitlab.com AND "@{domain}"',
        'site:twitter.com AND "@{domain}"',
        
        // Business directories
        'site:crunchbase.com AND "@{domain}"',
        'site:zoominfo.com AND "@{domain}"',
        'site:apollo.io AND "@{domain}"',
        'site:rocketreach.co AND "@{domain}"',
        
        // Contact patterns
        'inbody:"mailto:" AND "@{domain}"',
        'inbody:"email us" AND "@{domain}"',
        'inbody:"send us an email" AND "@{domain}"',
        'inbody:"drop us a line" AND "@{domain}"',
        
        // Developer resources
        'site:npm.js AND "@{domain}"',
        'site:stackoverflow.com AND "@{domain}"',
        'site:packagist.org AND "@{domain}"',
        
        // Events and conferences
        'inbody:("conference" OR "event" OR "webinar") AND "@{domain}"',
        'site:eventbrite.com AND "@{domain}"',
        'site:meetup.com AND "@{domain}"',
        
        // Job sites
        'site:indeed.com AND "@{domain}"',
        'site:glassdoor.com AND "@{domain}"',
        'site:careers.* AND "@{domain}"',
        
        // Academic
        'site:academia.edu AND "@{domain}"',
        'site:researchgate.net AND "@{domain}"',
        'inbody:("faculty" OR "professor" OR "researcher") AND "@{domain}"',
        
        // Document specific
        'filetype:pdf AND "mailto:" AND "@{domain}"',
        'filetype:pdf AND "contact" AND "@{domain}"',
        'filetype:doc AND "mailto:" AND "@{domain}"',
        
        // Advanced combinations
        'inbody:("contact" OR "email" OR "mail") AND ("department" OR "division") AND "@{domain}"',
        'inbody:("write to" OR "reach out" OR "contact") AND "@{domain}"',
        'inbody:("directory" OR "staff directory" OR "employee directory") AND "@{domain}"',
        
        // Cache and archives
        'cache:*.{domain} AND "@{domain}"',
        'site:web.archive.org AND "@{domain}"',
        
        // Google Groups and Forums
        'site:groups.google.com "@{domain}"',
        'site:forum.* "@{domain}"',
        
        // Press releases and news
        'inurl:press OR inurl:news "@{domain}"',
        'inurl:media OR inurl:press-releases "@{domain}"',
        
        // Government and educational institutions
        'site:gov "@{domain}"',
        'site:edu "@{domain}"',
        
        // Technical documentation
        'site:docs.* "@{domain}"',
        'inurl:documentation "@{domain}"',
        
        // Support and help pages
        'inurl:support "@{domain}"',
        'inurl:help "@{domain}"',
        
        // Mailing lists and newsletters
        'inurl:list OR inurl:newsletter "@{domain}"',
        'subscribe OR newsletter "@{domain}"'
    ];

    private $searchEngines = [
        'google' => [
            'url' => 'https://www.google.com/search?q={query}&start={page}',
            'step' => 10
        ],
        'bing' => [
            'url' => 'https://www.bing.com/search?q={query}&first={page}',
            'step' => 10
        ],
        'yahoo' => [
            'url' => 'https://search.yahoo.com/search?p={query}&b={page}',
            'step' => 10
        ],
        'brave' => [
            'url' => 'https://search.brave.com/search?q={query}&offset={page}',
            'step' => 10
        ],
        'aol' => [
            'url' => 'https://search.aol.com/aol/search?q={query}&pz=100&b={page}',
            'step' => 10
        ],
        'ecosia' => [
            'url' => 'https://www.ecosia.org/search?method=index&q={query}&p={page}',
            'step' => 10
        ]
    ];

    private $options = [];
    private $retryCount = 0;
    private $workingProxies = [];
    private $proxyTestUrl = 'http://httpbin.org/ip';
    private $minWorkingProxies = 5;
    private $proxyTimeout = 5;
    private $useProxies = true;

    public function __construct($options = []) {
        $this->options = array_merge([
            'engines' => ['google'],
            'check_leaks' => false,
            'deep_search' => false,
            'delay' => 2,
            'pages' => 1,
            'use_proxies' => true,
            'custom_proxies' => null
        ], $options);
    }

    private function log($message, $type = 'info', $details = []) {
        $timestamp = date('Y-m-d H:i:s');
        $response = array_merge([
            'type' => $type,
            'timestamp' => $timestamp,
            'message' => $message
        ], $details);
        
        echo json_encode($response) . "\n";
        ob_flush();
        flush();
    }

    private function initializeProxies() {
        if ($this->options['use_proxies'] && !empty($this->options['custom_proxies'])) {
            // Use custom proxies
            $this->log("Testing custom proxy list...", 'proxy');
            $proxies = array_filter(explode("\n", $this->options['custom_proxies']));
        } else {
            // Use GitHub proxies
            $this->log("Fetching proxy list from GitHub...", 'proxy');
            $proxyList = @file_get_contents('https://raw.githubusercontent.com/TheSpeedX/SOCKS-List/master/http.txt');
            if (!$proxyList) {
                $this->log("Failed to fetch proxy list", 'error');
                return false;
            }
            $proxies = array_filter(explode("\n", $proxyList));
        }

        $this->log("Found " . count($proxies) . " proxies to test", 'proxy');
        
        // Test proxies in parallel using curl_multi
        $mh = curl_multi_init();
        $channels = [];
        $testing = [];
        
        foreach ($proxies as $proxy) {
            if (count($testing) >= 20) { // Test 20 proxies at a time
                $this->processProxyTests($mh, $channels, $testing);
                $channels = [];
                $testing = [];
            }
            
            $ch = $this->createProxyTestCurl($proxy);
            if ($ch) {
                curl_multi_add_handle($mh, $ch);
                $channels[(int)$ch] = $ch;
                $testing[(int)$ch] = $proxy;
            }
            
            // Break if we found enough working proxies
            if (count($this->workingProxies) >= $this->minWorkingProxies) {
                $this->log("Found enough working proxies, stopping tests", 'proxy');
                break;
            }
        }
        
        // Process any remaining proxy tests
        if (!empty($channels)) {
            $this->processProxyTests($mh, $channels, $testing);
        }
        
        curl_multi_close($mh);
        
        $this->log("Found " . count($this->workingProxies) . " working proxies", 'proxy', [
            'working_proxies' => $this->workingProxies
        ]);
        
        return count($this->workingProxies) >= $this->minWorkingProxies;
    }
    
    private function createProxyTestCurl($proxy) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->proxyTestUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_PROXY => trim($proxy),
            CURLOPT_PROXYTYPE => CURLPROXY_HTTP,
            CURLOPT_TIMEOUT => $this->proxyTimeout,
            CURLOPT_CONNECTTIMEOUT => $this->proxyTimeout,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => false
        ]);
        return $ch;
    }
    
    private function processProxyTests($mh, &$channels, &$testing) {
        $running = null;
        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh);
            
            while ($info = curl_multi_info_read($mh)) {
                if ($info['msg'] == CURLMSG_DONE) {
                    $ch = $info['handle'];
                    $ch_id = (int)$ch;
                    
                    if (isset($testing[$ch_id])) {
                        $proxy = $testing[$ch_id];
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
                        $error = curl_error($ch);
                        
                        if ($httpCode == 200 && $totalTime < $this->proxyTimeout) {
                            $this->workingProxies[] = $proxy;
                            $this->log("Found working proxy: $proxy", 'proxy_success', [
                                'response_time' => $totalTime,
                                'proxy' => $proxy
                            ]);
                        } else {
                            $this->log("Failed proxy: $proxy", 'proxy_fail', [
                                'http_code' => $httpCode,
                                'error' => $error,
                                'proxy' => $proxy
                            ]);
                        }
                        
                        unset($testing[$ch_id]);
                        curl_multi_remove_handle($mh, $ch);
                    }
                }
            }
        } while ($running > 0);
    }
    
    private function isValidProxyResponse($response) {
        $data = @json_decode($response, true);
        return $data && isset($data['origin']) && filter_var($data['origin'], FILTER_VALIDATE_IP);
    }
    
    private function getProxy() {
        if (empty($this->workingProxies)) {
            return null;
        }
        return $this->workingProxies[array_rand($this->workingProxies)];
    }

    private function fetch_content($url, $engine, $dork, $page) {
        $maxRetries = 2;
        $retryCount = 0;
        $triedProxies = [];
        
        while ($retryCount <= $maxRetries) {
            // Get available proxies that haven't been tried
            $availableProxies = array_diff($this->workingProxies, $triedProxies);
            
            // If no more available proxies, break
            if (empty($availableProxies)) {
                $this->log("No more available proxies to try", 'error', [
                    'engine' => $engine,
                    'dork' => $dork,
                    'page' => $page
                ]);
                break;
            }
            
            // Select random proxy from available ones
            $proxyKeys = array_keys($availableProxies);
            $randomKey = $proxyKeys[array_rand($proxyKeys)];
            $proxy = $availableProxies[$randomKey];
            $triedProxies[] = $proxy;

            $ch = curl_init();
            $curlOptions = [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => $this->userAgents[array_rand($this->userAgents)],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_HTTPHEADER => [
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language: en-US,en;q=0.5',
                    'Cache-Control: no-cache',
                    'Connection: keep-alive'
                ],
                CURLOPT_COOKIEJAR => "cookies_{$engine}.txt",
                CURLOPT_COOKIEFILE => "cookies_{$engine}.txt",
                CURLOPT_ENCODING => 'gzip, deflate',
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
            ];

            if ($proxy) {
                $curlOptions[CURLOPT_PROXY] = trim($proxy);
                $curlOptions[CURLOPT_PROXYTYPE] = CURLPROXY_HTTP;
                
                $this->log("Attempt " . ($retryCount + 1) . " using proxy: " . $proxy, 'connection', [
                    'engine' => $engine,
                    'dork' => $dork,
                    'page' => $page,
                    'url' => $url,
                    'proxy' => $proxy
                ]);
            }

            curl_setopt_array($ch, $curlOptions);

            $startTime = microtime(true);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
            
            if (curl_errno($ch)) {
                $this->log("Connection error (Attempt " . ($retryCount + 1) . "): " . curl_error($ch), 'error', [
                    'engine' => $engine,
                    'dork' => $dork,
                    'page' => $page,
                    'error' => curl_error($ch),
                    'proxy' => $proxy
                ]);
            } else {
                $this->log("Response received from $engine (Attempt " . ($retryCount + 1) . ")", 'response_details', [
                    'engine' => $engine,
                    'dork' => $dork,
                    'page' => $page,
                    'url' => $finalUrl,
                    'http_code' => $httpCode,
                    'total_time' => round($totalTime, 2),
                    'response_size' => strlen($response)
                ]);
            }

            curl_close($ch);

            // If successful (HTTP 200), return the response
            if ($httpCode === 200) {
                $delay = rand($this->options['delay'] * 1000000, ($this->options['delay'] + 1) * 1000000);
                usleep($delay);
                return $response;
            }

            // Handle rate limiting
            if ($httpCode === 429) {
                $this->log("Rate limited by $engine (Attempt " . ($retryCount + 1) . ")", 'warning', [
                    'engine' => $engine,
                    'dork' => $dork,
                    'page' => $page,
                    'proxy' => $proxy
                ]);
                
                // Add exponential backoff
                $backoff = min(300, pow(2, $retryCount) * $this->options['delay']);
                sleep($backoff);
            } else {
                $this->log("HTTP error " . $httpCode . " (Attempt " . ($retryCount + 1) . ")", 'error', [
                    'engine' => $engine,
                    'dork' => $dork,
                    'page' => $page,
                    'proxy' => $proxy
                ]);
            }

            // Remove failed proxy from working proxies list
            if ($proxy) {
                $this->workingProxies = array_diff($this->workingProxies, [$proxy]);
            }

            $retryCount++;
            
            // If we've exhausted all retries, log and continue to next
            if ($retryCount > $maxRetries) {
                $this->log("Max retries reached for $engine, moving to next", 'warning', [
                    'engine' => $engine,
                    'dork' => $dork,
                    'page' => $page
                ]);
            }
        }

        return false;
    }

    private function extract_emails($content, $domain, $source_url) {
        // Improved email extraction patterns
        $patterns = [
            // Standard email pattern
            '/[a-zA-Z0-9._%+-]+@' . preg_quote($domain, '/') . '/i',
            
            // Common obfuscation patterns
            '/[a-zA-Z0-9._%+-]+\s*[\[\(]\s*at\s*[\]\)]\s*' . preg_quote($domain, '/') . '/i',
            '/[a-zA-Z0-9._%+-]+\s*@\s*' . preg_quote($domain, '/') . '/i',
            '/[a-zA-Z0-9._%+-]+\[at\]' . preg_quote($domain, '/') . '/i',
            '/[a-zA-Z0-9._%+-]+&#64;' . preg_quote($domain, '/') . '/i',
            '/[a-zA-Z0-9._%+-]+\(at\)' . preg_quote($domain, '/') . '/i',
            '/[a-zA-Z0-9._%+-]+\s+at\s+' . preg_quote($domain, '/') . '/i',
            
            // HTML encoded variations
            '/[a-zA-Z0-9._%+-]+&#0?64;' . preg_quote($domain, '/') . '/i',
            '/[a-zA-Z0-9._%+-]+%40' . preg_quote($domain, '/') . '/i'
        ];

        $results = [];
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $content = preg_replace('/\s+/', ' ', $content); // Normalize whitespace

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches)) {
                foreach ($matches[0] as $email) {
                    $email = $this->clean_email($email);
                    if ($this->validate_email($email, $domain)) {
                        $results[] = [
                            'email' => $email,
                            'source' => $source_url
                        ];
                    }
                }
            }
        }

        return array_unique($results, SORT_REGULAR);
    }

    private function clean_email($email) {
        // If email is already an array with 'email' key, extract just the email
        if (is_array($email) && isset($email['email'])) {
            $email = $email['email'];
        }
        
        // Remove HTML entities
        $email = html_entity_decode($email);
        
        // Remove common obfuscation
        $replacements = [
            '[at]' => '@',
            '(at)' => '@',
            ' at ' => '@',
            '[dot]' => '.',
            '(dot)' => '.',
            ' dot ' => '.',
            '&#64;' => '@',
            '&#40;at&#41;' => '@',
            '\x40' => '@'
        ];
        
        $email = str_replace(array_keys($replacements), array_values($replacements), $email);
        
        // Remove spaces and convert to lowercase
        $email = strtolower(trim($email));
        
        // Remove any remaining special characters
        $email = preg_replace('/[^\w\-\@\.]/', '', $email);
        
        return $email;
    }

    private function validate_email($email, $domain) {
        // Basic validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Check if email matches domain
        $emailDomain = substr(strrchr($email, "@"), 1);
        return strtolower($emailDomain) === strtolower($domain);
    }

    private function log_raw_response($content, $engine, $httpCode) {
        $errorMessage = $httpCode !== 200 ? 
            "<div class='error-message'>Request failed (HTTP $httpCode)</div>" : '';
        
        $dom = new DOMDocument();
        @$dom->loadHTML($content);
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $formatted_html = $dom->saveHTML();

        $this->log("", 'raw_response', [
            'content' => $errorMessage . $formatted_html,
            'engine' => $engine,
            'timestamp' => date('Y-m-d H:i:s'),
            'http_code' => $httpCode
        ]);
    }

    public function scrape($domain) {
        // Initialize proxies before starting the scrape
        if (!$this->initializeProxies()) {
            $this->log("Failed to initialize required number of proxies", 'error');
            return [];
        }
        
        $startTime = microtime(true);
        
        $this->log("Starting email scraping for domain: $domain", 'start', [
            'domain' => $domain,
            'pages' => $this->options['pages'],
            'engines' => $this->options['engines'],
            'deep_search' => $this->options['deep_search'],
            'check_leaks' => $this->options['check_leaks'],
            'delay' => $this->options['delay'],
            'total_dorks' => count($this->dorks),
            'start_time' => date('Y-m-d H:i:s')
        ]);
        
        // Only use selected search engines
        $this->searchEngines = array_intersect_key(
            $this->searchEngines, 
            array_flip($this->options['engines'])
        );

        $allEmails = [];
        $totalProcessed = 0;
        $totalDorks = count($this->dorks);
        
        foreach ($this->dorks as $index => $dorkTemplate) {
            $dork = str_replace('{domain}', $domain, $dorkTemplate);
            $this->log("Processing dork: $dork", 'dork_start', [
                'dork' => $dork,
                'progress' => [
                    'current' => $index + 1,
                    'total' => $totalDorks,
                    'percentage' => round((($index + 1) / $totalDorks) * 100)
                ]
            ]);
            
            foreach ($this->searchEngines as $engineName => $engineData) {
                for ($page = 0; $page < $this->options['pages']; $page++) {
                    $pagination = $page * $engineData['step'];
                    $url = str_replace(
                        ['{query}', '{page}'],
                        [urlencode($dork), $pagination],
                        $engineData['url']
                    );

                    $response = $this->fetch_content($url, $engineName, $dork, $page + 1);
                    if (!$response) {
                        continue;
                    }

                    $emails = $this->extract_emails($response, $domain, $url);
                    $newEmails = array_diff($emails, $allEmails);
                    
                    if (!empty($newEmails)) {
                        $allEmails = array_merge($allEmails, $newEmails);
                        $this->log("Found new emails", 'emails_found', [
                            'engine' => $engineName,
                            'dork' => $dork,
                            'page' => $page + 1,
                            'new_emails' => array_values($newEmails),
                            'total_emails' => count($allEmails),
                            'progress' => [
                                'current' => $index + 1,
                                'total' => $totalDorks,
                                'percentage' => round((($index + 1) / $totalDorks) * 100)
                            ]
                        ]);
                    } else {
                        $this->log("No new emails found", 'no_emails', [
                            'engine' => $engineName,
                            'dork' => $dork,
                            'page' => $page + 1
                        ]);
                    }

                    usleep(rand(500000, 1500000));
                }
            }
            $totalProcessed++;
        }

        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);
        
        $this->log("Scraping completed", 'complete', [
            'total_unique_emails' => count($allEmails),
            'total_dorks_processed' => $totalProcessed,
            'duration_seconds' => $duration,
            'emails_per_second' => round(count($allEmails) / $duration, 2),
            'total_requests' => $totalProcessed * count($this->options['engines']) * $this->options['pages'],
            'end_time' => date('Y-m-d H:i:s'),
            'statistics' => [
                'successful_requests' => $successfulRequests,
                'failed_requests' => $failedRequests,
                'total_emails_found' => count($allEmails),
                'unique_domains' => array_unique(array_map(function($email) {
                    return substr(strrchr($email['email'], "@"), 1);
                }, $allEmails))
            ]
        ]);
        
        return $allEmails;
    }
}

// Handle incoming requests
if (isset($_GET['email_query'])) {
    $options = [
        'engines' => isset($_GET['engines']) ? explode(',', $_GET['engines']) : ['google'],
        'check_leaks' => isset($_GET['check_leaks']),
        'deep_search' => isset($_GET['deep_search']),
        'delay' => isset($_GET['delay']) ? (int)$_GET['delay'] : 2,
        'pages' => isset($_GET['pages']) ? (int)$_GET['pages'] : 1,
        'use_proxies' => isset($_GET['use_proxies']) ? (bool)$_GET['use_proxies'] : true,
        'custom_proxies' => isset($_GET['custom_proxies']) ? $_GET['custom_proxies'] : null
    ];
    
    $scraper = new EmailScraper($options);
    $scraper->scrape($_GET['email_query']);
    exit;
}
?>
