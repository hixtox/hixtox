<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Scraper</title>
    <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
    font-family: 'Courier New', monospace;
    margin: 0;
    background-color: #1e1e1e;
    color: #f8f9fa;
    padding: 20px;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
}

.top-section {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
}

.left-panel {
    flex: 1;
    max-width: 400px;
}

.right-panel {
    flex: 2;
}

h1, h2 {
    color: #ffd60a;
    margin-bottom: 20px;
}

.options-panel {
    background-color: #2c2c2c;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.stats-section {
    margin: 30px 0;
    padding: 20px;
    background-color: #2c2c2c;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 20px;
}

.stat-card {
    background-color: #383838;
    padding: 20px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 15px;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-card i {
    font-size: 24px;
    color: #ffd60a;
}

.stat-content {
    flex-grow: 1;
}

.stat-content h3 {
    color: #f8f9fa;
    font-size: 14px;
    margin: 0 0 5px 0;
}

.stat-content span {
    font-size: 24px;
    font-weight: bold;
    color: #ffd60a;
}

.stat-content small {
    color: #adb5bd;
    margin-left: 5px;
}

.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
    color: #ffd60a;
}

input[type="text"], 
select.form-control,
textarea.form-control {
    width: 100%;
    padding: 8px;
    background-color: #3c3c3c;
    border: 1px solid #555;
    color: #fff;
    border-radius: 4px;
}

textarea.form-control {
    margin-top: 10px;
    resize: vertical;
    min-height: 100px;
    font-family: monospace;
}

#customProxyArea {
    margin-top: 15px;
    padding: 10px;
    border: 1px solid #555;
    border-radius: 4px;
    background-color: #383838;
}

.button-group {
    display: flex;
    gap: 10px;
}

button {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s ease;
}

button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

#submitButton {
    background-color: #ffd60a;
    color: #1e1e1e;
}

#stopButton {
    background-color: #dc3545;
    color: #fff;
    display: none;
}

.command-line {
    background-color: #2c2c2c;
    padding: 15px;
    border-radius: 8px;
    height: 280px;
    overflow-y: auto;
    font-family: monospace;
    font-size: 14px;
    line-height: 1.5;
}

.log-start { color: #ffd60a; }
.log-progress { color: #0dcaf0; }
.log-result { color: #20c997; }
.log-complete { color: #198754; }
.log-error { color: #dc3545; }

.progress-container {
    background-color: #3c3c3c;
    border-radius: 4px;
    overflow: hidden;
    margin-top: 15px;
    height: 20px;
}

.progress-bar {
    height: 100%;
    background-color: #ffd60a;
    width: 0%;
    transition: width 0.3s ease;
}

.table-section {
    margin-top: 30px;
}

.table-controls {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 20px;
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.table-search {
    display: flex;
    align-items: center;
    gap: 10px;
}

#tableSearch {
    width: 300px;
    padding: 8px;
    background-color: #3c3c3c;
    border: 1px solid #555;
    color: #fff;
    border-radius: 4px;
}

#searchCount {
    color: #ffd60a;
    font-size: 14px;
}

.export-buttons {
    display: flex;
    gap: 10px;
}

.export-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s ease;
}

.export-btn.excel {
    background-color: #207245;
    color: white;
}

.table-container {
    overflow-x: auto;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background-color: #2c2c2c;
    border-radius: 8px;
}

th {
    background-color: #333;
    color: #ffd60a;
    font-weight: bold;
    padding: 15px;
    text-align: left;
    cursor: pointer;
    transition: background-color 0.3s ease;
    position: sticky;
    top: 0;
}

th:hover {
    background-color: #444;
}
td a{
color: white;
    background: #fed709b8;
    padding: 5px;
    border-radius: 10px;
    text-decoration: none;
}
td a:hover{
    background: #ad9e4db8;
}
td {
    padding: 12px 15px;
    border-bottom: 1px solid #444;
    color: #f8f9fa;
}

tr:hover {
    background-color: #383838;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
}

.pagination button {
    padding: 8px 16px;
    background-color: #3c3c3c;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.pagination button.active {
    background-color: #ffd60a;
    color: #1e1e1e;
}

.pagination button:hover {
    background-color: #444;
}

.checkbox-wrapper {
    display: flex;
    align-items: center;
    margin-top: 10px;
}

.custom-checkbox {
    display: none;
}

.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    user-select: none;
    color: #f8f9fa;
}

.checkbox-custom {
    width: 20px;
    height: 20px;
    border: 2px solid #ffd60a;
    border-radius: 4px;
    margin-right: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.checkbox-custom::after {
    content: '\f00c';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    color: #1e1e1e;
    font-size: 12px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.custom-checkbox:checked + .checkbox-label .checkbox-custom {
    background-color: #ffd60a;
}

.custom-checkbox:checked + .checkbox-label .checkbox-custom::after {
    opacity: 1;
}

.custom-checkbox:disabled + .checkbox-label {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
</head>
<body>
    <div class="container">
        <h1>Business Scraper</h1>
        
        <!-- Top Section with Form and Command Line -->
        <div class="top-section">
            <div class="left-panel">
                <div class="options-panel">
                    <form id="searchForm">
                        <div class="form-group">
                            <label for="query">Search Query:</label>
                            <input type="text" id="query" required>
                        </div>
                        <div class="form-group">
                            <label for="pages">Number of Pages to Scan:</label>
                            <select id="pages" class="form-control">
                                <option value="1">1 page</option>
                                <option value="2">2 pages</option>
                                <option value="3">3 pages</option>
                                <option value="4">4 pages</option>
                                <option value="5">5 pages</option>
                                <option value="6">6 pages</option>
                                <option value="7">7 pages</option>
                                <option value="8">8 pages</option>
                                <option value="9">9 pages</option>
                                <option value="10">10 pages</option>
                            </select>
                            <label for="count">Results per page:</label>
                            <select id="count" class="form-control">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100" selected>100</option>
                            </select>
                            <div class="checkbox-wrapper">
                                <input type="checkbox" id="useProxy" class="custom-checkbox">
                                <label for="useProxy" class="checkbox-label">
                                    <span class="checkbox-custom"></span>
                                    Use Proxy
                                </label>
                            </div>
                            <div class="checkbox-wrapper">
                                <input type="checkbox" id="useCustomProxy" class="custom-checkbox">
                                <label for="useCustomProxy" class="checkbox-label">
                                    <span class="checkbox-custom"></span>
                                    Use Custom Proxy List
                                </label>
                            </div>
                            <div id="customProxyArea" style="display: none;">
                                <label for="proxyList">Enter proxy list (one per line):</label>
                                <textarea id="proxyList" class="form-control" rows="5" 
                                    placeholder="ip:port&#10;ip:port&#10;ip:port"></textarea>
                            </div>
                        </div>
                        <div class="button-group">
                            <button type="submit" id="submitButton">Start Scraping</button>
                            <button type="button" id="stopButton">Stop Scraping</button>
                        </div>
                    </form>
                    <div class="progress-container">
                        <div class="progress-bar" id="progressBar"></div>
                    </div>
                </div>
            </div>
            <div class="right-panel">
                <div class="command-line" id="commandLine"></div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="stats-section">
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-building"></i>
                    <div class="stat-content">
                        <h3>Total Businesses</h3>
                        <span id="totalBusinesses">0</span>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-phone"></i>
                    <div class="stat-content">
                        <h3>With Phone</h3>
                        <span id="totalWithPhone">0</span>
                        <small id="phonePercentage">(0%)</small>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-globe"></i>
                    <div class="stat-content">
                        <h3>With Website</h3>
                        <span id="totalWithWebsite">0</span>
                        <small id="websitePercentage">(0%)</small>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-file"></i>
                    <div class="stat-content">
                        <h3>Pages Scanned</h3>
                        <span id="pagesScanned">0</span>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-shield-alt"></i>
                    <div class="stat-content">
                        <h3>Tested Proxies</h3>
                        <span id="totalTestedProxies">0</span>
                        <small>total</small>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-check-circle"></i>
                    <div class="stat-content">
                        <h3>Working Proxies</h3>
                        <span id="workingProxies">0</span>
                        <small id="proxyPercentage">(0%)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-section">
            <div class="table-controls">
                <div class="table-header">
                    <h2>Found Businesses</h2>
                    <div class="table-search">
                        <input type="text" id="tableSearch" placeholder="Search in results...">
                        <span id="searchCount"></span>
                    </div>
                </div>
                <div class="export-buttons">
                    <button onclick="exportToExcel()" class="export-btn excel">Export to Excel</button>
                </div>
            </div>
            <div class="table-container">
                <table id="businessTable">
                    <thead>
                        <tr>
                            <th onclick="sortTable(0)">Name ↕</th>
                            <th onclick="sortTable(1)">Address ↕</th>
                            <th onclick="sortTable(2)">Phone ↕</th>
                            <th onclick="sortTable(3)">Website ↕</th>
                        </tr>
                    </thead>
                    <tbody id="businessList">
                    </tbody>
                </table>
            </div>
            <div class="pagination" id="pagination"></div>
        </div>
    </div>
    <script>
        const form = document.getElementById('searchForm');
const submitButton = document.getElementById('submitButton');
const stopButton = document.getElementById('stopButton');
const progressBar = document.getElementById('progressBar');
const commandLine = document.getElementById('commandLine');
const businessList = document.getElementById('businessList');
let abortController = null;
let currentPage = 1;
let totalPages = 1;
let allResults = [];
let searchResults = [];
let totalBusinesses = 0;
let totalWithPhone = 0;
let totalWithWebsite = 0;
let pagesScanned = 0;
let totalTestedProxies = 0;
let workingProxiesCount = 0;

function getTimestamp() {
    const now = new Date();
    return now.toLocaleTimeString();
}

function addBusinessToList(business) {
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${business.name || ''}</td>
        <td>${business.address || ''}</td>
        <td>${business.phone || ''}</td>
        <td>${business.website ? `<a href="${business.website}" target="_blank">${business.website}</a>` : ''}</td>
    `;
    businessList.appendChild(row);
}

async function fetchResults(params) {
    try {
        const response = await fetch(`gmaps.php?${params}`, {
            signal: abortController.signal
        });
        
        const reader = response.body.getReader();
        let buffer = '';

        while (true) {
            const {done, value} = await reader.read();
            if (done) break;
            
            const chunk = new TextDecoder().decode(value);
            buffer += chunk;
            
            let startIndex = 0;
            let curlyCount = 0;
            let jsonStart = -1;

            for (let i = 0; i < buffer.length; i++) {
                if (buffer[i] === '{') {
                    if (curlyCount === 0) jsonStart = i;
                    curlyCount++;
                } else if (buffer[i] === '}') {
                    curlyCount--;
                    if (curlyCount === 0 && jsonStart !== -1) {
                        try {
                            const jsonStr = buffer.substring(jsonStart, i + 1);
                            const data = JSON.parse(jsonStr);
                            processStreamData(data);
                            
                            if (data.status === 'result' && data.data) {
                                allResults.push(data.data);
                                searchResults = [...allResults];
                                
                                totalBusinesses = allResults.length;
                                totalWithPhone = allResults.filter(b => b.phone).length;
                                totalWithWebsite = allResults.filter(b => b.website).length;
                                
                                document.getElementById('totalBusinesses').textContent = totalBusinesses;
                                document.getElementById('totalWithPhone').textContent = totalWithPhone;
                                document.getElementById('totalWithWebsite').textContent = totalWithWebsite;
                                document.getElementById('phonePercentage').textContent = 
                                    `(${Math.round((totalWithPhone/totalBusinesses)*100)}%)`;
                                document.getElementById('websitePercentage').textContent = 
                                    `(${Math.round((totalWithWebsite/totalBusinesses)*100)}%)`;
                                
                                displayTablePage(1);
                            }
                        } catch (e) {
                            console.error('JSON parse error:', e);
                        }
                        startIndex = i + 1;
                        jsonStart = -1;
                    }
                }
            }
            buffer = buffer.substring(startIndex);
        }
    } catch (error) {
        if (error.name === 'AbortError') {
            throw new Error('Scraping stopped by user');
        }
        throw error;
    }
}

function processStreamData(data) {
    const timestamp = getTimestamp();
    let message = '';
    
    switch (data.status) {
        case 'searching':
            message = `<p class="log-progress">[${timestamp}] ${data.message}</p>`;
            break;

        case 'found':
            message = `<p class="log-result">[${timestamp}] ${data.message}</p>`;
            pagesScanned++;
            document.getElementById('pagesScanned').textContent = pagesScanned;
            break;

        case 'proxy_testing':
            totalTestedProxies++;
            document.getElementById('totalTestedProxies').textContent = totalTestedProxies;
            message = `<p class="log-progress">[${timestamp}] ${data.message}</p>`;
            break;

        case 'proxy_success':
            workingProxiesCount++;
            document.getElementById('workingProxies').textContent = workingProxiesCount;
            if (totalTestedProxies > 0) {
                const percentage = Math.round((workingProxiesCount/totalTestedProxies)*100);
                document.getElementById('proxyPercentage').textContent = `(${percentage}%)`;
            }
            message = `<p class="log-result">[${timestamp}] ${data.message}</p>`;
            break;

        case 'proxy_error':
            message = `<p class="log-error">[${timestamp}] ${data.message}</p>`;
            break;

        case 'proxy_info':
            message = `<p class="log-info">[${timestamp}] ${data.message}</p>`;
            break;

        case 'proxy_complete':
            message = `<p class="log-complete">[${timestamp}] ${data.message}</p>`;
            break;

        case 'proxy_testing':
            message = `<p class="log-progress">[${timestamp}] ${data.message}</p>`;
            break;

        case 'connection_info':
            message = `<p class="log-info">[${timestamp}] ${data.message}</p>`;
            break;

        case 'result':
            if (data.data) {
                message = `<p class="log-result">[${timestamp}] Found details for: ${data.data.name}</p>`;
            }
            break;

        case 'processing':
            message = `<p class="log-progress">[${timestamp}] ${data.message}</p>`;
            break;

        case 'complete':
            message = `<p class="log-complete">[${timestamp}] ${data.message}</p>`;
            break;

        case 'error':
            message = `<p class="log-error">[${timestamp}] ${data.message}</p>`;
            break;

        default:
            if (data.message) {
                message = `<p class="log-info">[${timestamp}] ${data.message}</p>`;
            }
            break;
    }
    
    if (message) {
        const wasScrolledToBottom = commandLine.scrollHeight - commandLine.clientHeight <= commandLine.scrollTop + 1;
        commandLine.innerHTML += message;
        if (wasScrolledToBottom) {
            commandLine.scrollTop = commandLine.scrollHeight;
        }
    }
}
// Handle custom proxy checkbox
document.getElementById('useCustomProxy').addEventListener('change', function(e) {
    const customProxyArea = document.getElementById('customProxyArea');
    const useProxyCheckbox = document.getElementById('useProxy');
    
    if (this.checked) {
        customProxyArea.style.display = 'block';
        useProxyCheckbox.checked = false;
        useProxyCheckbox.disabled = true;
    } else {
        customProxyArea.style.display = 'none';
        useProxyCheckbox.disabled = false;
    }
});

// Handle regular proxy checkbox
document.getElementById('useProxy').addEventListener('change', function(e) {
    const useCustomProxyCheckbox = document.getElementById('useCustomProxy');
    
    if (this.checked) {
        useCustomProxyCheckbox.checked = false;
        document.getElementById('customProxyArea').style.display = 'none';
    }
});

// Form submit handler
form.addEventListener('submit', async (event) => {
    event.preventDefault();
    
    abortController = new AbortController();
    
    const query = document.getElementById('query').value;
    const count = document.getElementById('count').value;
    const pagesToScan = parseInt(document.getElementById('pages').value);
    const useProxy = document.getElementById('useProxy').checked;
    const useCustomProxy = document.getElementById('useCustomProxy').checked;
    const customProxyList = useCustomProxy ? document.getElementById('proxyList').value : '';
    
    // Reset UI and data
    commandLine.innerHTML = '';
    businessList.innerHTML = '';
    progressBar.style.width = '0%';
    allResults = [];
    searchResults = [];
    totalBusinesses = 0;
    totalWithPhone = 0;
    totalWithWebsite = 0;
    pagesScanned = 0;
    
    submitButton.disabled = true;
    submitButton.textContent = 'Scraping...';
    stopButton.style.display = 'block';

    try {
        for (let page = 1; page <= pagesToScan; page++) {
            const params = new URLSearchParams({
                query: query,
                count: count,
                first: page,
                pages: pagesToScan,
                proxy: useProxy ? '1' : '0',
                customProxy: useCustomProxy ? '1' : '0',
                proxyList: customProxyList
            });

            commandLine.innerHTML += `<p class="log-start">[${getTimestamp()}] Scanning page ${page} of ${pagesToScan}${useProxy ? ' using proxy' : ''}${useCustomProxy ? ' using custom proxy list' : ''}</p>`;
            await fetchResults(params);
        }
    } catch (error) {
        console.error('Fetch error:', error);
        commandLine.innerHTML += `<p class="log-error">[${getTimestamp()}] Error: ${error.message}</p>`;
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = 'Start Scraping';
        stopButton.style.display = 'none';
    }
});

stopButton.addEventListener('click', () => {
    if (abortController) {
        abortController.abort();
        commandLine.innerHTML += `<p class="log-error">[${getTimestamp()}] Stopping scraper...</p>`;
    }
});

document.getElementById('tableSearch').addEventListener('input', (e) => {
    const searchTerm = e.target.value.toLowerCase();
    
    if (searchTerm) {
        searchResults = allResults.filter(business => 
            business.name?.toLowerCase().includes(searchTerm) ||
            business.address?.toLowerCase().includes(searchTerm) ||
            business.phone?.toLowerCase().includes(searchTerm) ||
            business.website?.toLowerCase().includes(searchTerm)
        );
    } else {
        searchResults = [...allResults];
    }
    
    document.getElementById('searchCount').textContent = 
        `Found ${searchResults.length} matches`;
    
    displayTablePage(1);
});

function sortTable(n) {
    const table = document.getElementById("businessTable");
    let rows, switching = true;
    let i, shouldSwitch, dir = "asc";
    let switchcount = 0;

    while (switching) {
        switching = false;
        rows = table.rows;

        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            const x = rows[i].getElementsByTagName("TD")[n];
            const y = rows[i + 1].getElementsByTagName("TD")[n];
            
            if (dir === "asc") {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }
            } else if (dir === "desc") {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }
            }
        }

        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            switchcount++;
        } else if (switchcount === 0 && dir === "asc") {
            dir = "desc";
            switching = true;
        }
    }
}

function exportToExcel() {
    const table = document.getElementById('businessTable');
    const ws = XLSX.utils.table_to_sheet(table);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Businesses");
    XLSX.writeFile(wb, "businesses.xlsx");
}

function displayTablePage(page) {
    const itemsPerPage = 10;
    const start = (page - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    
    businessList.innerHTML = '';
    const displayData = searchResults.slice(start, end);
    displayData.forEach(business => addBusinessToList(business));
    
    updateTablePagination(page);
}

function updateTablePagination(currentPage) {
    const itemsPerPage = 10;
    const totalPages = Math.ceil(searchResults.length / itemsPerPage);
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';
    
    pagination.appendChild(createPageButton('«', 1));
    
    if (currentPage > 1) {
        pagination.appendChild(createPageButton('‹', currentPage - 1));
    }
    
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages || 1, currentPage + 2);
    
    if (endPage - startPage < 4) {
        if (currentPage < 3) {
            endPage = Math.min(5, totalPages || 1);
        } else {
            startPage = Math.max(1, endPage - 4);
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const button = createPageButton(i, i);
        if (i === currentPage) {
            button.classList.add('active');
        }
        pagination.appendChild(button);
    }
    
    if (currentPage < totalPages) {
        pagination.appendChild(createPageButton('›', currentPage + 1));
    }
    
    pagination.appendChild(createPageButton('»', totalPages || 1));
}

function createPageButton(text, page) {
    const button = document.createElement('button');
    button.textContent = text;
    button.onclick = () => displayTablePage(page);
    return button;
}
</script>
</body>
</html>
