<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Proxy Tester</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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

        h1, h2, h3 {
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

        textarea {
            width: 95%;
            height: 150px;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #555;
            border-radius: 4px;
            background-color: #3a3a3a;
            color: #f8f9fa;
            font-family: monospace;
        }

        .settings-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #ffc300;
        }

        input[type="number"] {
            width: 95%;
            padding: 8px;
            margin-bottom: 10px;
            background: #3a3a3a;
            border: 1px solid #555;
            border-radius: 4px;
            color: #f8f9fa;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #ffc300;
            color: #000;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin-bottom: 10px;
        }

        button:hover {
            background: #ffd60a;
        }

        .results-panel {
            background: #2c2c2c;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
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
            background: #333;
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
        }

        .proxy-lists-container {
            display: flex;
            gap: 20px;
            margin-top: 15px;
        }

        .proxy-list-column {
            flex: 1;
        }

        .proxy-list-column h3 {
            color: #ffc300;
            margin-bottom: 10px;
            text-align: center;
        }

        .proxy-list {
            max-height: 400px;
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
        }

        #workingProxyResults .proxy-item {
            border-left: 3px solid #4CAF50;
            padding-left: 10px;
        }

        #failedProxyResults .proxy-item {
            border-left: 3px solid #f44336;
            padding-left: 10px;
            opacity: 0.7;
        }

        .command-line {
            background: #2c2c2c;
            padding: 15px;
            border-radius: 8px;
            height: 200px;
            overflow-y: auto;
            font-family: monospace;
            margin-bottom: 15px;
        }

        .log-entry {
            margin-bottom: 5px;
            padding: 5px;
            border-radius: 3px;
        }

        .log-success { color: #4CAF50; }
        .log-error { color: #f44336; }
        .log-info { color: #2196F3; }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .action-button {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        #exportButton {
            background: #2196F3;
            color: white;
        }

        #clearButton {
            background: #f44336;
            color: white;
        }

        .progress-bar {
            height: 4px;
            background: #333;
            margin-bottom: 15px;
            border-radius: 2px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: #ffc300;
            width: 0;
            transition: width 0.3s ease;
        }

        .proxy-item {
            animation: fadeIn 0.2s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .performance-indicator {
            position: fixed;
            top: 10px;
            right: 10px;
            background: #2c2c2c;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            color: #ffc300;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <div class="options-panel">
                <h2>Proxy Tester</h2>
                <form id="proxyForm">
                    <div class="settings-group">
                        <label>Proxy List (one per line)</label>
                        <textarea id="proxyList" placeholder="Enter proxies here...&#10;Example:&#10;127.0.0.1:8080&#10;proxy.example.com:3128" required></textarea>
                    </div>
                    <div class="settings-group">
                        <label>Timeout (seconds)</label>
                        <input type="number" id="timeout" value="5" min="1" max="30">
                    </div>
                    <div class="settings-group">
                        <label>Concurrent Tests</label>
                        <input type="number" id="concurrent" value="10" min="1" max="50">
                    </div>
                    <button type="submit">Start Testing</button>
                    <button type="button" id="stopButton" style="display: none;">Stop Testing</button>
                </form>
            </div>
        </div>

        <div class="right-panel">
            <div class="results-panel">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value" id="totalProxies">0</div>
                        <div class="stat-label">Total Proxies</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" id="testedProxies">0</div>
                        <div class="stat-label">Tested</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" id="workingProxies">0</div>
                        <div class="stat-label">Working</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" id="avgResponse">0ms</div>
                        <div class="stat-label">Avg Response</div>
                    </div>
                </div>

                <div class="action-buttons">
                    <button id="exportButton" class="action-button">
                        <i class="fas fa-download"></i> Export Working
                    </button>
                    <button id="clearButton" class="action-button">
                        <i class="fas fa-trash"></i> Clear Results
                    </button>
                </div>

                <div class="proxy-lists-container">
                    <div class="proxy-list-column">
                        <h3>Working Proxies</h3>
                        <div class="proxy-list" id="workingProxyResults"></div>
                    </div>
                    <div class="proxy-list-column">
                        <h3>Failed Proxies</h3>
                        <div class="proxy-list" id="failedProxyResults"></div>
                    </div>
                </div>
            </div>

            <div class="command-line" id="commandLine"></div>
        </div>
    </div>

    <div class="performance-indicator" id="performanceIndicator">
        Processing Speed: 0/s
    </div>

    <script>
        let testing = false;
        let abortController = null;
        let startTime = null;
        let processedCount = 0;
        let lastUpdateTime = 0;
        let processingSpeed = 0;

        function updatePerformanceStats() {
            const now = Date.now();
            const timeDiff = (now - lastUpdateTime) / 1000;
            if (timeDiff >= 1) {
                const speed = Math.round(processedCount / timeDiff);
                document.getElementById('performanceIndicator').textContent = 
                    `Processing Speed: ${speed}/s`;
                processedCount = 0;
                lastUpdateTime = now;
            }
        }

        function processResults(text) {
            text.split('\n').forEach(line => {
                if (!line.trim()) return;
                
                try {
                    const result = JSON.parse(line);
                    processedCount++;
                    
                    if (result.proxy) {
                        const proxyList = result.success ? 
                            document.getElementById('workingProxyResults') : 
                            document.getElementById('failedProxyResults');
                        
                        const item = document.createElement('div');
                        item.className = 'proxy-item';
                        item.innerHTML = `
                            <span>${result.proxy}</span>
                            <span>${Math.round(result.response_time)}ms | HTTP ${result.http_code}</span>
                        `;
                        proxyList.appendChild(item);
                        proxyList.scrollTop = proxyList.scrollHeight;

                        if (result.stats) {
                            document.getElementById('testedProxies').textContent = result.stats.tested;
                            document.getElementById('workingProxies').textContent = result.stats.working;
                            if (result.stats.total_time > 0 && result.stats.tested > 0) {
                                const avgResponse = Math.round(result.stats.total_time / result.stats.tested);
                                document.getElementById('avgResponse').textContent = avgResponse + 'ms';
                            }
                        }

                        const message = `${result.proxy} - ${result.success ? 'Working' : 'Failed'} (${result.response_time}ms)`;
                        logMessage(message, result.success ? 'success' : 'error');
                    }

                    updatePerformanceStats();
                    
                } catch (e) {
                    console.error('Error parsing result:', e, line);
                }
            });
        }

        async function startTest(proxyList) {
            testing = true;
            startTime = Date.now();
            lastUpdateTime = startTime;
            processedCount = 0;
            abortController = new AbortController();

            document.getElementById('totalProxies').textContent = proxyList.length;
            document.getElementById('stopButton').style.display = 'block';
            document.getElementById('workingProxyResults').innerHTML = '';
            document.getElementById('failedProxyResults').innerHTML = '';
            document.getElementById('commandLine').innerHTML = '';

            try {
                const response = await fetch('proxytest.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        proxies: proxyList,
                        timeout: parseInt(document.getElementById('timeout').value),
                        concurrent: parseInt(document.getElementById('concurrent').value)
                    }),
                    signal: abortController.signal
                });

                const reader = response.body.getReader();
                const decoder = new TextDecoder();

                while (testing) {
                    const {value, done} = await reader.read();
                    if (done) break;
                    processResults(decoder.decode(value));
                }
            } catch (error) {
                if (error.name === 'AbortError') {
                    logMessage('Testing stopped by user', 'info');
                } else {
                    logMessage('Error: ' + error.message, 'error');
                }
            } finally {
                endTest();
            }
        }

        function endTest() {
            testing = false;
            abortController = null;
            document.getElementById('stopButton').style.display = 'none';
            const duration = ((Date.now() - startTime) / 1000).toFixed(1);
            logMessage(`Test completed in ${duration}s`, 'info');
        }

        document.getElementById('proxyForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            if (testing) return;

            const proxyList = document.getElementById('proxyList').value
                .split('\n')
                .map(proxy => proxy.trim())
                .filter(proxy => proxy);

            if (proxyList.length === 0) {
                logMessage('No valid proxies provided', 'error');
                return;
            }

            startTest(proxyList);
        });

        document.getElementById('stopButton').addEventListener('click', () => {
            if (testing && abortController) {
                abortController.abort();
            }
        });

        document.getElementById('exportButton').addEventListener('click', () => {
            const workingProxies = Array.from(document.querySelectorAll('#workingProxyResults .proxy-item'))
                .map(item => item.querySelector('span').textContent)
                .join('\n');

            if (workingProxies) {
                const blob = new Blob([workingProxies], {type: 'text/plain'});
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'working_proxies.txt';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            } else {
                logMessage('No working proxies to export', 'error');
            }
        });

        document.getElementById('clearButton').addEventListener('click', () => {
            document.getElementById('workingProxyResults').innerHTML = '';
            document.getElementById('failedProxyResults').innerHTML = '';
            document.getElementById('commandLine').innerHTML = '';
            document.getElementById('totalProxies').textContent = '0';
            document.getElementById('testedProxies').textContent = '0';
            document.getElementById('workingProxies').textContent = '0';
            document.getElementById('avgResponse').textContent = '0ms';
            document.getElementById('performanceIndicator').textContent = 'Processing Speed: 0/s';
            logMessage('Results cleared', 'info');
        });

        function logMessage(message, type = 'info') {
            const commandLine = document.getElementById('commandLine');
            const entry = document.createElement('div');
            entry.className = `log-entry log-${type}`;
            entry.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
            commandLine.appendChild(entry);
            commandLine.scrollTop = commandLine.scrollHeight;
        }
    </script>
</body>
</html>