<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Scraper</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            margin: 0;
            background-color: #1e1e1e;
            color: #f8f9fa;
            padding: 20px;
        }

        .container {
            display: flex;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            gap: 20px;
        }

        .left-panel {
            flex: 1;
            max-width: 400px;
        }

        .right-panel {
            flex: 2;
        }

        h1, h2 {
            text-align: center;
            color: #ffd60a;
            margin-bottom: 20px;
        }

        .options-panel {
            background-color: #2c2c2c;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            color: #ffc300;
            margin-bottom: 8px;
            display: block;
        }

        input, select, button {
            width: 95%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #555;
            border-radius: 4px;
            font-size: 14px;
            background-color: #3a3a3a;
            color: #f8f9fa;
        }

        button {
            background-color: #ffc300;
            color: #000;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #ffd60a;
        }

        .search-engines-group {
            margin-bottom: 15px;
        }

        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .checkbox-item input[type="checkbox"] {
            width: auto;
            margin: 0;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .panel-title {
            margin: 0;
            color: #ffc300;
        }

        .clear-button {
            background: transparent;
            border: 1px solid #ffc300;
            color: #ffc300;
            padding: 5px 10px;
            font-size: 12px;
            width: auto;
            margin: 0;
        }

        .clear-button:hover {
            background: #ffc300;
            color: #000;
        }

        /* Scraping Panel Styles */
        .scraping-panel {
            background: #2c2c2c;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            height: 300px;
            display: flex;
            flex-direction: column;
        }

        .scraping-container {
            flex: 1;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .scraping-summary {
            background: #333;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .stat-item {
            text-align: center;
            padding: 10px;
            background: #2c2c2c;
            border-radius: 4px;
        }

        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #ffc300;
        }

        .stat-label {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }

        .scraping-progress {
            height: 4px;
            background: #444;
            border-radius: 2px;
            overflow: hidden;
        }

        .scraping-progress-bar {
            height: 100%;
            width: 0;
            background: #ffc300;
            transition: width 0.3s ease;
        }

        .scraping-list {
            flex: 1;
            overflow-y: auto;
            background: #333;
            border-radius: 4px;
            padding: 10px;
        }

        .scraping-list .email-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            border-bottom: 1px solid #444;
            font-family: monospace;
            font-size: 12px;
        }

        .email-item .email-address {
            flex: 1;
        }

        .email-item .source-link {
            background: #ffc300;
            color: #000;
            padding: 2px 8px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 10px;
            margin-left: 10px;
        }

        .source-link:hover {
            background: #ffd60a;
        }

        .action-buttons {
            
            top: 15px;
            right: 15px;
            display: flex;
            gap: 10px;
        }

        .action-button {
            background: transparent;
            border: 1px solid #ffc300;
            color: #ffc300;
            padding: 5px 10px;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }

        .action-button:hover {
            background: #ffc300;
            color: #000;
        }

        .action-button i {
            font-size: 14px;
        }

        /* Proxy Panel Styles */
        .proxy-panel {
            background: #2c2c2c;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            height: 300px;
            display: flex;
            flex-direction: column;
        }

        .proxy-testing-container {
            flex: 1;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .proxy-summary {
            background: #333;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
        }

        .proxy-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .proxy-stat-item {
            text-align: center;
            padding: 10px;
            background: #2c2c2c;
            border-radius: 4px;
        }

        .proxy-stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #2196F3;
        }

        .proxy-stat-label {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }

        .proxy-progress {
            height: 4px;
            background: #444;
            border-radius: 2px;
            overflow: hidden;
        }

        .proxy-progress-bar {
            height: 100%;
            width: 0;
            background: #2196F3;
            transition: width 0.3s ease;
        }

        .proxy-list {
            flex: 1;
            overflow-y: auto;
            background: #333;
            border-radius: 4px;
            padding: 10px;
        }

        .proxy-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            border-bottom: 1px solid #444;
            font-family: monospace;
            font-size: 12px;
        }

        .proxy-item:last-child {
            border-bottom: none;
        }

        .proxy-item.success {
            border-left: 3px solid #4CAF50;
            padding-left: 10px;
        }

        .proxy-item.fail {
            border-left: 3px solid #f44336;
            padding-left: 10px;
            opacity: 0.7;
        }

        /* Command Line Styles */
        .command-line {
    background: #2c2c2c;
    padding: 15px;
    border-radius: 8px;
    height: 150px;
    overflow-y: auto;
    font-family: 'Consolas', 'Monaco', monospace;
    font-size: 13px;
    line-height: 1.5;
    margin-bottom: 15px;
    color: #e0e0e0;
        }

        /* Log entry types */
        .log-proxy {
            color: #64B5F6;  /* Light Blue */
            padding: 4px 8px;
            border-left: 3px solid #64B5F6;
            margin: 4px 0;
        }

        .log-proxy_success {
            color: #81C784;  /* Light Green */
            padding: 4px 8px;
            border-left: 3px solid #81C784;
            margin: 4px 0;
        }

        .log-proxy_fail {
            color: #E57373;  /* Light Red */
            padding: 4px 8px;
            border-left: 3px solid #E57373;
            margin: 4px 0;
            opacity: 0.8;
        }

        .log-success {
            color: #AED581;  /* Light Green-Yellow */
            padding: 4px 8px;
            border-left: 3px solid #AED581;
            margin: 4px 0;
        }

        .log-error {
            color: #FF8A65;  /* Light Red-Orange */
            padding: 4px 8px;
            border-left: 3px solid #FF8A65;
            margin: 4px 0;
            font-weight: 500;
        }

        .log-default {
            color: #10683a;  /* Light Blue-Grey */
            padding: 4px 8px;
            border-left: 3px solid #10683a;
            margin: 4px 0;
        }

        /* Timestamp styling */
        .log-proxy [timestamp],
        .log-proxy_success [timestamp],
        .log-proxy_fail [timestamp],
        .log-success [timestamp],
        .log-error [timestamp],
        .log-default [timestamp] {
            color: #78909C;  /* Muted Blue-Grey */
            font-size: 12px;
            margin-right: 8px;
        }

        /* Scrollbar styling */
        .command-line::-webkit-scrollbar {
            width: 8px;
        }

        .command-line::-webkit-scrollbar-track {
            background: #1e1e1e;
            border-radius: 4px;
        }

        .command-line::-webkit-scrollbar-thumb {
            background: #4a4a4a;
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        .command-line::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Command line header styling */
        .command-line .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #3a3a3a;
        }

        .command-line .panel-title {
            font-size: 14px;
            font-weight: 600;
            color: #ffc300;
        }

        /* Clear button styling */
        .command-line .clear-button {
            background: transparent;
            border: 1px solid #ffc300;
            color: #ffc300;
            padding: 4px 8px;
            font-size: 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .command-line .clear-button:hover {
            background: #ffc300;
            color: #000;
        }

        /* Log entry hover effect */
        .log-proxy,
        .log-proxy_success,
        .log-proxy_fail,
        .log-success,
        .log-error,
        .log-default {
            transition: background-color 0.2s ease;
        }

        .log-proxy:hover,
        .log-proxy_success:hover,
        .log-proxy_fail:hover,
        .log-success:hover,
        .log-error:hover,
        .log-default:hover {
            background-color: rgba(255, 255, 255, 0.03);
        }

        /* Animation for new log entries */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-2px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .command-line div {
            animation: fadeIn 0.2s ease-out forwards;
        }

        /* Word wrapping for long messages */
        .command-line div {
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        /* Link styling within logs */
        .command-line a {
            color: #64B5F6;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .command-line a:hover {
            color: #90CAF9;
            text-decoration: underline;
        }

        /* Status Badges */
        .proxy-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
        }

        .proxy-badge.success {
            background: #4CAF50;
            color: white;
        }

        .proxy-badge.fail {
            background: #f44336;
            color: white;
        }

        /* Loading Animation */
        @keyframes loading {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #ffc300;
            border-radius: 50%;
            animation: loading 1s linear infinite;
            margin-right: 10px;
        }

        .control-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 15px;
        }

        .status-text {
            text-align: center;
           
            margin-bottom: 20px;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            background-color: #2c2c2c;
            border: 1px solid #ffc300;
            color: #ffc300;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        /* Status-specific styles */
        .status-text.running {
            background-color: rgba(255, 195, 0, 0.1);
            border-color: #ffc300;
            color: #ffc300;
        }

        .status-text.stopped {
            background-color: rgba(244, 67, 54, 0.1);
            border-color: #f44336;
            color: #f44336;
        }

        /* Add loading spinner for running state */
        .status-text.running::before {
            content: '';
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 2px solid #ffc300;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .action-button, .clear-button {
            background: transparent;
            border: 1px solid #ffc300;
            color: #ffc300;
            padding: 5px 10px;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .action-button:hover, .clear-button:hover {
            background: #ffc300;
            color: #000;
        }

        .action-button i, .clear-button i {
            font-size: 14px;
        }

        #pauseButton {
            background-color: #ffa500;
        }

        #pauseButton:hover {
            background-color: #ff8c00;
        }

        #resumeButton {
            background-color: #4CAF50;
            display: none;
        }

        #resumeButton:hover {
            background-color: #45a049;
        }

        #stopButton {
            background-color: #f44336;
        }

        #stopButton:hover {
            background-color: #da190b;
        }

        .advanced-options {
            background: #333;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .advanced-options h3 {
            color: #ffc300;
            margin-top: 0;
        }

        .proxy-section {
            margin: 15px 0;
        }

        #proxy_list {
            width: 100%;
            height: 100px;
            background: #2c2c2c;
            border: 1px solid #555;
            color: #f8f9fa;
            padding: 10px;
            margin-top: 10px;
            border-radius: 4px;
        }

        .response-preview {
            background: #2c2c2c;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            max-height: 300px;
            overflow: auto;
        }

        #responsePreview {
            background: #333;
            padding: 15px;
            border-radius: 4px;
            margin: 0;
            white-space: pre-wrap;
            font-family: monospace;
            font-size: 12px;
            color: #f8f9fa;
        }

        .log-status {
            color: #00ff00 !important; /* Bright green */
            font-weight: bold;
            padding: 4px 8px;
            border-left: 3px solid #00ff00;
            margin: 4px 0;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="left-panel">
           
            <div class="options-panel">
             <h1>Email Scraper</h1>
                <form id="scraperForm">
                    <label for="domain">Domain:</label>
                    <input type="text" id="domain" name="domain" placeholder="example.com" required>

                    <div class="advanced-options">
                        <h3>Advanced Options</h3>
                        
                        <div class="form-group">
                            <label>Search Engines:</label>
                            <div class="checkbox-group">
                                <div class="checkbox-item">
                                    <input type="checkbox" id="google" name="engines[]" value="google" checked>
                                    <label for="google">Google</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="bing" name="engines[]" value="bing" checked>
                                    <label for="bing">Bing</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="yahoo" name="engines[]" value="yahoo" checked>
                                    <label for="yahoo">Yahoo</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="brave" name="engines[]" value="brave" checked>
                                    <label for="brave">Brave</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="aol" name="engines[]" value="aol" checked>
                                    <label for="aol">AOL</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="ecosia" name="engines[]" value="ecosia" checked>
                                    <label for="ecosia">Ecosia</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="duckduckgo" name="engines[]" value="duckduckgo" checked>
                                    <label for="duckduckgo">DuckDuckGo</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="yandex" name="engines[]" value="yandex" checked>
                                    <label for="yandex">Yandex</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="baidu" name="engines[]" value="baidu" checked>
                                    <label for="baidu">Baidu</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="naver" name="engines[]" value="naver" checked>
                                    <label for="naver">Naver</label>
                                </div>
                            </div>
                        </div>

                        <div class="proxy-section">
                            <div class="checkbox-item">
                                <input type="checkbox" id="use_proxies" name="use_proxies">
                                <label for="use_proxies">Use Custom Proxies</label>
                            </div>
                            <textarea id="proxy_list" name="proxy_list" placeholder="Enter your proxies (one per line)" style="display: none;"></textarea>
                        </div>

                        <label for="pages">Pages per Search:</label>
                        <input type="number" id="pages" name="pages" value="1" min="1" max="10">

                        <label for="delay">Delay (seconds):</label>
                        <input type="number" id="delay" name="delay" value="2" min="1" max="10">

                        <div class="checkbox-item">
                            <input type="checkbox" id="deep_search" name="deep_search">
                            <label for="deep_search">Deep Search</label>
                        </div>
                    </div>

                    <div class="control-buttons">
                        <button type="submit" id="startButton">Start Scraping</button>
                        <button type="button" id="stopButton" style="display: none;">Stop</button>
                        
                    </div>
                </form>
            </div>
        </div>

        <div class="right-panel">
        <div class="command-line">
                        <div class="panel-header" style="display: none;">
                    <h3 class="panel-title">Command Line</h3>
                    <button class="clear-button" id="clearCommandLine">Clear</button>
                </div>
                <div id="command-line"></div>
            </div>
            <div class="scraping-panel">
            <div class="status-text" id="statusText"></div>
                <div class="panel-header">
                    <h3 class="panel-title">Email Scraping Progress</h3>
                    <div class="action-buttons">
                        <button class="action-button" id="copyEmails">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                        <button class="action-button" id="downloadEmails">
                            <i class="fas fa-download"></i> Download
                        </button>
                        <button class="clear-button" style="display: none;" id="clearScrapingPanel">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="scraping-container">
                    <div class="scraping-summary">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-value" id="totalEmails">0</div>
                                <div class="stat-label">Emails Found</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value" id="dorksChecked">0</div>
                                <div class="stat-label">Dorks Checked</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value" id="pagesScanned">0</div>
                                <div class="stat-label">Pages Scanned</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value" id="successRate">0%</div>
                                <div class="stat-label">Success Rate</div>
                            </div>
                        </div>
                        <div class="scraping-progress">
                            <div class="scraping-progress-bar" id="scrapingProgressBar"></div>
                        </div>
                    </div>
                    <div class="scraping-list" id="scrapingList"></div>
                </div>
            </div>

            <div class="proxy-panel">
                <div class="panel-header">
                    <h3 class="panel-title">Proxy Testing</h3>
                    <button class="clear-button" id="clearProxyPanel">Clear</button>
                </div>
                <div class="proxy-testing-container">
                    <div class="proxy-summary">
                        <div class="proxy-stats-grid">
                            <div class="proxy-stat-item">
                                <div class="proxy-stat-value" id="totalProxies">0</div>
                                <div class="proxy-stat-label">Total Proxies</div>
                            </div>
                            <div class="proxy-stat-item">
                                <div class="proxy-stat-value" id="testedProxies">0</div>
                                <div class="proxy-stat-label">Tested</div>
                            </div>
                            <div class="proxy-stat-item">
                                <div class="proxy-stat-value" id="workingProxies">0</div>
                                <div class="proxy-stat-label">Working</div>
                            </div>
                            <div class="proxy-stat-item">
                                <div class="proxy-stat-value" id="averageResponse">0ms</div>
                                <div class="proxy-stat-label">Avg Response</div>
                            </div>
                        </div>
                        <div class="proxy-progress">
                            <div class="proxy-progress-bar" id="proxyProgressBar"></div>
                        </div>
                    </div>
                    <div class="proxy-list" id="proxyList"></div>
                </div>
            </div>

            
        </div>
    </div>

    <script>
        // Initialize variables
        const commandLineDiv = document.getElementById('command-line');
        const form = document.getElementById('scraperForm');
        const startButton = document.getElementById('startButton');
        const stopButton = document.getElementById('stopButton');
        let currentXHR = null;
        let isRunning = false;
        let lastQueryString = '';
        let lastProcessedIndex = 0;
        const statusCheckInterval = 1000; // Check status every second

        // Initialize statistics
        let scrapingStats = {
            totalEmails: 0,
            dorksChecked: 0,
            pagesScanned: 0,
            totalRequests: 0,
            successfulRequests: 0
        };

        let proxyStats = {
            total: 0,
            tested: 0,
            working: 0,
            responseTimes: []
        };

        // Clear button handlers
        document.getElementById('clearCommandLine').addEventListener('click', () => {
            commandLineDiv.innerHTML = '';
        });

        document.getElementById('clearProxyPanel').addEventListener('click', () => {
            document.getElementById('proxyList').innerHTML = '';
            proxyStats = {
                total: 0,
                tested: 0,
                working: 0,
                responseTimes: []
            };
            updateProxyStats();
        });

        document.getElementById('clearScrapingPanel').addEventListener('click', () => {
            document.getElementById('scrapingList').innerHTML = '';
            scrapingStats = {
                totalEmails: 0,
                dorksChecked: 0,
                pagesScanned: 0,
                totalRequests: 0,
                successfulRequests: 0
            };
            updateScrapingStats();
        });

        // Form submission handler
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const domain = formData.get('domain');
            const engines = formData.getAll('engines[]');
            const pages = formData.get('pages');
            const delay = formData.get('delay');
            const deepSearch = formData.get('deep_search') ? 1 : 0;
            const useProxies = formData.get('use_proxies') ? 1 : 0;
            
            // Add custom proxies handling
            let queryString = `email_query=${encodeURIComponent(domain)}&engines=${engines.join(',')}&pages=${pages}&delay=${delay}&deep_search=${deepSearch}&use_proxies=${useProxies}`;
            
            // If using custom proxies, add them to the query string
            if (useProxies) {
                const proxyList = document.getElementById('proxy_list').value.trim();
                if (proxyList) {
                    queryString += `&custom_proxies=${encodeURIComponent(proxyList)}`;
                }
            }
            
            startScraping(queryString);
            updateButtons('running');
            logCommandLine('Good luck, Scraping started !!');
                  
            logCommandLine('Testing proxies...');
        });


        stopButton.addEventListener('click', function() {
            isRunning = false;
            if (currentXHR) {
                currentXHR.abort();
            }
            updateButtons('stopped');
            logCommandLine('This was hard but we got something, Scraping stopped !!');
            
            fetch('email_scraper.php?stop=true')
                .then(response => response.json())
                .catch(error => console.error('Failed to stop:', error));
        });

        function startStatusCheck() {
            isRunning = true;
            const statusChecker = setInterval(() => {
                if (!isRunning) {
                    clearInterval(statusChecker);
                    return;
                }

                fetch('email_scraper.php?check_status')
                    .then(response => response.json())
                    .then(data => {
                        updateButtons(data.running ? 'running' : 'stopped');
                    })
                    .catch(error => console.error('Status check failed:', error));
            }, statusCheckInterval);
        }

        function startScraping(queryString) {
            isRunning = true;
            updateButtons('running');
            

            
            currentXHR = new XMLHttpRequest();
            currentXHR.open('GET', `email_scraper.php?${queryString}`, true);
            
            let buffer = '';
            
            currentXHR.onprogress = function() {
                const newData = this.responseText.substr(buffer.length);
                buffer = this.responseText;
                
                newData.split("\n").forEach(line => {
                    if (line.trim()) {
                        try {
                            const data = JSON.parse(line);
                            updateCommandLine(data);
                            
                            // Check if proxy testing is complete
                            if (data.type === 'proxy' && data.message.includes('Found enough working proxies')) {
                                logCommandLine('Proxy testing complete, starting scraping...');
                                updateStatus('running');
                            }
                        } catch (error) {
                            console.error('Error parsing JSON:', error, 'Line:', line);
                        }
                    }
                });
            };
            
            currentXHR.onerror = function() {
                updateCommandLine({
                    type: 'error',
                    message: 'Connection error occurred',
                    timestamp: new Date().toISOString()
                });
                updateButtons('stopped');
                isRunning = false;
            };
            
            currentXHR.onload = function() {
                if (!isRunning) {
                    updateButtons('stopped');
                    isRunning = false;
                }
            };
            
            currentXHR.send();
        }

        function updateButtons(status) {
            const startButton = document.getElementById('startButton');
            const stopButton = document.getElementById('stopButton');
            const statusText = document.getElementById('statusText');

            statusText.classList.remove('running', 'stopped');

            switch(status) {
                case 'running':
                    startButton.style.display = 'none';
                    stopButton.style.display = 'block';
                    statusText.textContent = 'Scraping in progress...';
                    statusText.classList.add('running');
                    break;
                case 'stopped':
                    startButton.style.display = 'block';
                    stopButton.style.display = 'none';
                    statusText.textContent = 'Scraping stopped';
                    statusText.classList.add('stopped');
                    break;
            }
        }

        function updateCommandLine(data) {
            const timestamp = data.timestamp || new Date().toLocaleTimeString();
            let message = '';

            switch(data.type) {
                case 'proxy':
                    if (data.message.includes('Found')) {
                        proxyStats.total = parseInt(data.message.match(/\d+/)[0]);
                        updateProxyStats();
                    }
                    message = `<div class="log-proxy">[${timestamp}] ${data.message}</div>`;
                    break;

                case 'proxy_success':
                    proxyStats.tested++;
                    proxyStats.working++;
                    if (data.response_time) {
                        proxyStats.responseTimes.push(data.response_time);
                    }
                    addProxyToList(data);
                    updateProxyStats();
                    message = `<div class="log-proxy_success">[${timestamp}] ${data.message}</div>`;
                    break;

                case 'proxy_fail':
                    proxyStats.tested++;
                    addProxyToList(data);
                    updateProxyStats();
                    message = `<div class="log-proxy_fail">[${timestamp}] ${data.message}</div>`;
                    break;

                case 'emails_found':
                    scrapingStats.totalEmails += data.new_emails.length;
                    scrapingStats.pagesScanned++;
                    scrapingStats.successfulRequests++;
                    data.new_emails.forEach(email => {
                        addEmailToList({
                            email: email.email || email,
                            source: email.source || 'Direct Search',
                            dork: data.dork
                        });
                    });
                    updateScrapingStats();
                    if (data.progress) {
                        document.getElementById('scrapingProgressBar').style.width = 
                            `${data.progress.percentage}%`;
                    }
                    message = `<div class="log-success">[${timestamp}] ${data.message}</div>`;
                    break;

                case 'dork_start':
                    scrapingStats.dorksChecked++;
                    updateScrapingStats();
                    if (data.progress) {
                        document.getElementById('scrapingProgressBar').style.width = 
                            `${data.progress.percentage}%`;
                    }
                    message = `<div class="log-info">[${timestamp}] ${data.message}</div>`;
                    break;

                case 'error':
                    message = `<div class="log-error">[${timestamp}] ${data.message}</div>`;
                    break;

                case 'raw_response':
                    document.getElementById('responsePreview').innerHTML = data.content;
                    break;

                case 'progress':
                    // Update progress bar and status
                    const progressBar = document.getElementById('scrapingProgressBar');
                    if (progressBar) {
                        progressBar.style.width = `${data.percentage}%`;
                    }
                    
                    // Store current position
                    lastPosition = {
                        dorkIndex: data.current - 1,
                        engineIndex: 0,
                        pageIndex: 0
                    };
                    break;

                default:
                    message = `<div class="log-default">[${timestamp}] ${data.message}</div>`;
            }

            if (message) {
                commandLineDiv.innerHTML += message;
                commandLineDiv.scrollTop = commandLineDiv.scrollHeight;
            }
        }

        function updateProxyStats() {
            document.getElementById('totalProxies').textContent = proxyStats.total;
            document.getElementById('testedProxies').textContent = proxyStats.tested;
            document.getElementById('workingProxies').textContent = proxyStats.working;
            
            const avgResponse = proxyStats.responseTimes.length 
                ? Math.round(proxyStats.responseTimes.reduce((a, b) => a + b, 0) / proxyStats.responseTimes.length * 1000)
                : 0;
            document.getElementById('averageResponse').textContent = `${avgResponse}ms`;

            const progress = proxyStats.total ? (proxyStats.tested / proxyStats.total) * 100 : 0;
            document.getElementById('proxyProgressBar').style.width = `${progress}%`;
        }

        function updateScrapingStats() {
            document.getElementById('totalEmails').textContent = scrapingStats.totalEmails;
            document.getElementById('dorksChecked').textContent = scrapingStats.dorksChecked;
            document.getElementById('pagesScanned').textContent = scrapingStats.pagesScanned;
            
            const successRate = scrapingStats.totalRequests 
                ? Math.round((scrapingStats.successfulRequests / scrapingStats.totalRequests) * 100)
                : 0;
            document.getElementById('successRate').textContent = `${successRate}%`;
        }

        function addProxyToList(data) {
            const proxyItem = document.createElement('div');
            proxyItem.className = `proxy-item ${data.type === 'proxy_success' ? 'success' : 'fail'}`;
            
            const responseTime = data.response_time ? `${Math.round(data.response_time * 1000)}ms` : '';
            
            proxyItem.innerHTML = `
                <div class="proxy-item-details">
                    <span class="proxy-ip">${data.proxy}</span>
                    ${responseTime ? `<span class="proxy-response-time">${responseTime}</span>` : ''}
                </div>
                <span class="proxy-badge ${data.type === 'proxy_success' ? 'success' : 'fail'}">
                    ${data.type === 'proxy_success' ? 'WORKING' : 'FAILED'}
                </span>
            `;
            
            document.getElementById('proxyList').appendChild(proxyItem);
            document.getElementById('proxyList').scrollTop = document.getElementById('proxyList').scrollHeight;
        }

        function addEmailToList(data) {
            const emailItem = document.createElement('div');
            emailItem.className = 'email-item';
            
            emailItem.innerHTML = `
                <span class="email-address">${data.email}</span>
                <a href="${data.source}" target="_blank" class="source-link">
                    <i class="fas fa-external-link-alt"></i> Check on Web
                </a>
            `;
            
            document.getElementById('scrapingList').appendChild(emailItem);
            document.getElementById('scrapingList').scrollTop = document.getElementById('scrapingList').scrollHeight;
        }

        // Add copy and download functionality
        document.getElementById('copyEmails').addEventListener('click', function() {
            const emailItems = document.querySelectorAll('.email-item .email-address');
            if (emailItems.length === 0) {
                alert('No emails to copy!');
                return;
            }
            
            const emailList = Array.from(emailItems)
                .map(item => item.textContent.trim())
                .filter(email => email)
                .join('\n');
            
            // Create temporary textarea element
            const textarea = document.createElement('textarea');
            textarea.value = emailList;
            textarea.setAttribute('readonly', '');
            textarea.style.position = 'absolute';
            textarea.style.left = '-9999px';
            document.body.appendChild(textarea);
            
            try {
                // Select and copy the text
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                
                // Visual feedback
                const copyButton = this;
                const originalText = copyButton.innerHTML;
                copyButton.innerHTML = '<i class="fas fa-check"></i> Copied!';
                setTimeout(() => {
                    copyButton.innerHTML = originalText;
                }, 2000);
            } catch (err) {
                console.error('Failed to copy emails:', err);
                alert('Failed to copy emails to clipboard. Please try again.');
            } finally {
                document.body.removeChild(textarea);
            }
        });

        document.getElementById('downloadEmails').addEventListener('click', function() {
            const emailItems = document.querySelectorAll('.email-address');
            const emailList = Array.from(emailItems).map(item => item.textContent).join('\n');
            
            const blob = new Blob([emailList], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'email_list.txt';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        });

        function updateStatus(status) {
            currentStatus = status;
            const statusText = document.getElementById('statusText');
            
            switch(status) {
                case 'idle':
                    statusText.textContent = 'Ready to start';
                    break;
                case 'running':
                    statusText.textContent = 'Scraping in progress...';
                    statusText.classList.add('running');
                    break;
                case 'paused':
                    statusText.textContent = 'Scraping paused';
                    break;
                case 'stopped':
                    statusText.textContent = 'Scraping stopped';
                    statusText.classList.add('stopped');
                    break;
            }
        }

        // Initialize status
        updateStatus('idle');

        document.getElementById('use_proxies').addEventListener('change', function() {
            const proxyTextarea = document.getElementById('proxy_list');
            proxyTextarea.style.display = this.checked ? 'block' : 'none';
            if (!this.checked) {
                proxyTextarea.value = ''; // Clear proxies when unchecked
            }
        });

        // Add this function if it doesn't exist
        function logCommandLine(message) {
            const timestamp = new Date().toISOString().replace('T', ' ').split('.')[0];
            const logMessage = `<div class="log-status">[${timestamp}] ${message}</div>`;
            commandLineDiv.innerHTML += logMessage;
            commandLineDiv.scrollTop = commandLineDiv.scrollHeight;
        }
    </script>
</body>
</html>