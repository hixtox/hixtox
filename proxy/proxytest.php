<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0);

class ProxyTester {
    private $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36'
    ];

    private $testUrls = [
        'https://bing.com/',
        'http://google.com/'
    ];

    private $options = [
        'timeout' => 5,
        'verify_ssl' => false
    ];

    private $stats = [
        'tested' => 0,
        'working' => 0,
        'failed' => 0,
        'total_time' => 0
    ];

    public function __construct($options = []) {
        $this->options = array_merge($this->options, $options);
    }

    public function testProxies($proxyList) {
        $totalProxies = count($proxyList);
        $batchSize = 50; // Adjust batch size for optimal performance
        $mh = curl_multi_init();

        for ($i = 0; $i < $totalProxies; $i += $batchSize) {
            $batch = array_slice($proxyList, $i, $batchSize);
            $handles = [];

            foreach ($batch as $proxy) {
                $ch = $this->createCurlHandle($proxy);
                curl_multi_add_handle($mh, $ch);
                $handles[(int)$ch] = $proxy;
            }

            $this->executeMultiHandle($mh, $handles);
        }

        curl_multi_close($mh);
    }

    private function createCurlHandle($proxy) {
        $ch = curl_init();
        $testUrl = $this->testUrls[array_rand($this->testUrls)];
        $userAgent = $this->userAgents[array_rand($this->userAgents)];

        curl_setopt_array($ch, [
            CURLOPT_URL => $testUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_PROXY => trim($proxy),
            CURLOPT_PROXYTYPE => CURLPROXY_HTTP,
            CURLOPT_TIMEOUT => $this->options['timeout'],
            CURLOPT_CONNECTTIMEOUT => $this->options['timeout'],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT => $userAgent,
            CURLOPT_HEADER => false,
            CURLOPT_NOBODY => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3
        ]);

        return $ch;
    }

    private function executeMultiHandle($mh, $handles) {
        $running = null;
        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh);

            while ($info = curl_multi_info_read($mh)) {
                $ch = $info['handle'];
                $proxy = $handles[(int)$ch];
                
                $response = curl_multi_getcontent($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                $responseTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME) * 1000;

                $this->stats['tested']++;
                $this->stats['total_time'] += $responseTime;

                $result = [
                    'proxy' => $proxy,
                    'success' => false,
                    'response_time' => round($responseTime),
                    'http_code' => $httpCode,
                    'error' => $error,
                    'stats' => $this->stats
                ];

                if ($httpCode === 200 && $response && !$error) {
                    $result['success'] = true;
                    $this->stats['working']++;
                } else {
                    $this->stats['failed']++;
                }

                echo json_encode($result) . "\n";
                ob_flush();
                flush();

                curl_multi_remove_handle($mh, $ch);
                curl_close($ch);
            }
        } while ($running > 0);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!empty($input['proxies'])) {
        $options = [
            'timeout' => isset($input['timeout']) ? (int)$input['timeout'] : 5,
            'verify_ssl' => isset($input['verify_ssl']) ? (bool)$input['verify_ssl'] : false
        ];

        $tester = new ProxyTester($options);
        $tester->testProxies($input['proxies']);
    } else {
        echo json_encode(['error' => 'No proxies provided']);
    }
}
?>
