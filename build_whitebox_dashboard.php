<?php
/**
 * Put beside artisan, then run: php build_whitebox_dashboard.php
 * Reads existing report files only. Does not run tests or access the database.
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Run from Terminal: php build_whitebox_dashboard.php');
}

$reportDir = __DIR__ . DIRECTORY_SEPARATOR . 'reports';
$specs = [
    ['WhiteBoxCore', 'Đăng nhập & thêm giỏ', ['whitebox-core.xml', 'whitebox-core-before.xml'], false],
    ['WhiteBoxDefects', 'Kiểm tra số lượng giỏ', ['whitebox-defects.xml', 'whitebox-defects-before.xml'], false],
    ['RegistrationCore', 'Đăng ký — logic hiện có', ['01-registration-core.xml'], false],
    ['RegistrationRequirements', 'Đăng ký — yêu cầu', ['01-registration-requirements.xml'], false],
    ['AccountSecurity', 'Tài khoản & phân quyền', ['02-account-security.xml'], false],
    ['CartManagement', 'Quản lý giỏ hàng', ['03-cart-management.xml'], false],
    ['CartRequirements', 'Giỏ hàng — yêu cầu', ['03-cart-requirements.xml'], false],
    ['ProductData', 'Dữ liệu sản phẩm', ['04-product-data.xml'], false],
    ['JavaScriptCore', 'Tìm kiếm, lọc & JavaScript', ['04-javascript-core.json'], false],
    ['JavaScriptRequirements', 'Tìm kiếm không dấu — điều kiện', ['04-javascript-requirements.json'], true],
];

$groups = [];
$rows = [];
foreach ($specs as [$key, $label, $filenames, $conditional]) {
    $group = ['key' => $key, 'label' => $label, 'conditional' => $conditional,
        'file' => null, 'modified' => null, 'issue' => null, 'count' => 0];
    $candidates = [];
    foreach ($filenames as $filename) {
        $path = $reportDir . DIRECTORY_SEPARATOR . $filename;
        if (is_file($path)) {
            $candidates[] = $path;
        }
    }
    // Select just one report for each suite; prefer the newest available file.
    usort($candidates, static fn (string $a, string $b): int => filemtime($b) <=> filemtime($a));
    $groupRows = [];
    if (!$candidates) {
        $group['issue'] = 'Chưa có báo cáo: ' . implode(' hoặc ', $filenames);
    } else {
        $path = $candidates[0];
        $group['file'] = basename($path);
        $group['modified'] = date(DATE_ATOM, (int) filemtime($path));
        try {
            $bytes = file_get_contents($path);
            if ($bytes === false) {
                throw new RuntimeException('Không đọc được file.');
            }
            if (str_ends_with($path, '.json')) {
                $report = json_decode($bytes, true, 512, JSON_THROW_ON_ERROR);
                if (!isset($report['results']) || !is_array($report['results'])) {
                    throw new RuntimeException('JSON không chứa danh sách results.');
                }
                foreach ($report['results'] as $test) {
                    $status = strtoupper((string) ($test['status'] ?? ''));
                    if (!in_array($status, ['PASS', 'FAIL', 'ERROR', 'SKIP'], true)) {
                        throw new RuntimeException('Trạng thái test JSON không hợp lệ.');
                    }
                    $groupRows[] = ['name' => (string) ($test['name'] ?? '(không có tên)'),
                        'status' => $status, 'seconds' => ((float) ($test['milliseconds'] ?? 0)) / 1000,
                        'assertions' => null, 'message' => (string) ($test['message'] ?? '')];
                }
            } else {
                if (!class_exists(DOMDocument::class)) {
                    throw new RuntimeException('PHP thiếu DOM extension để đọc XML.');
                }
                $previous = libxml_use_internal_errors(true);
                try {
                    $doc = new DOMDocument();
                    if (!$doc->loadXML($bytes, LIBXML_NONET)) {
                        throw new RuntimeException('File XML không hợp lệ.');
                    }
                } finally {
                    libxml_clear_errors();
                    libxml_use_internal_errors($previous);
                }
                $xpath = new DOMXPath($doc);
                $suite = $xpath->query('//testsuite[@name="' . $key . '"]')->item(0);
                if (!$suite) {
                    throw new RuntimeException('Không tìm thấy testsuite ' . $key . ' trong XML.');
                }
                foreach ($xpath->query('.//testcase', $suite) as $test) {
                    $status = 'PASS';
                    $messages = [];
                    foreach ($test->childNodes as $child) {
                        if (!($child instanceof DOMElement)) {
                            continue;
                        }
                        if (in_array($child->tagName, ['failure', 'error', 'skipped'], true)) {
                            $status = ['failure' => 'FAIL', 'error' => 'ERROR', 'skipped' => 'SKIP'][$child->tagName];
                            $messages[] = trim($child->getAttribute('type') . "\n" . $child->textContent);
                        }
                    }
                    $groupRows[] = ['name' => $test->getAttribute('name'), 'status' => $status,
                        'seconds' => (float) $test->getAttribute('time'),
                        'assertions' => (int) $test->getAttribute('assertions'),
                        'message' => implode("\n\n", $messages)];
                }
            }
            if (!$groupRows) {
                throw new RuntimeException('Báo cáo chưa có testcase.');
            }
            foreach ($groupRows as $row) {
                $row['group'] = $key;
                $row['conditional'] = $conditional;
                $rows[] = $row;
            }
            $group['count'] = count($groupRows);
        } catch (Throwable $error) {
            $group['issue'] = 'Không tổng hợp được báo cáo: ' . $error->getMessage();
        }
    }
    $groups[] = $group;
}

$payload = json_encode(['created' => date(DATE_ATOM), 'groups' => $groups, 'rows' => $rows],
    JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);

$html = <<<'HTML'
<!doctype html>
<html lang="vi">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Báo cáo kiểm thử hộp trắng · Nhóm 4</title>
<style>
:root{color-scheme:light;--ink:#172b40;--muted:#53667b;--line:#dce4ec;--bg:#f3f6fa;--pass:#086b4a;--fail:#b32635;--error:#9b4e00}
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--ink);font:15px/1.5 system-ui,-apple-system,"Segoe UI",sans-serif}main{max-width:1320px;margin:auto;padding:36px 24px 60px}
header{display:flex;justify-content:space-between;align-items:center;gap:20px;margin-bottom:24px}.eyebrow{font-size:12px;letter-spacing:1.5px;font-weight:750;color:var(--pass)}h1{font-size:30px;line-height:1.2;margin:8px 0}h2{font-size:19px;margin:0}p{margin:8px 0;color:var(--muted)}button,input,select{font:inherit}button,select,input{border:1px solid #becbd8;border-radius:8px;background:white;padding:10px 12px;color:var(--ink)}button{cursor:pointer}button:hover{background:#eef3f8}button:focus-visible,input:focus-visible,select:focus-visible{outline:3px solid #86b9ef;outline-offset:2px}.primary{background:var(--ink);color:white;border-color:var(--ink)}.primary:hover{background:#304c68}.meta{font-size:13px}.cards{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:14px;margin:22px 0}.card,.panel{background:white;border:1px solid var(--line);border-radius:12px}.card{padding:18px}.card span{color:var(--muted);font-size:13px}.card strong{display:block;font-size:34px;line-height:1.3;margin-top:5px}.pass{color:var(--pass)}.fail{color:var(--fail)}.error{color:var(--error)}.note{border-left:4px solid #6e8ba9;padding:12px 16px;background:#e9f0f8;border-radius:0 8px 8px 0;margin:16px 0;color:#304b66}.note.warning{border-color:#bd7900;background:#fff5dc;color:#714700}.panel{padding:20px;margin-top:20px}.groups{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:16px}.group{border:1px solid var(--line);border-radius:9px;padding:12px 14px;text-align:left;width:100%;min-width:0}.group .heading{display:flex;justify-content:space-between;gap:12px;font-weight:650}.group small{display:block;color:var(--muted);overflow-wrap:anywhere;margin-top:4px}.group .counts{font-size:13px;margin-top:6px}.missing{background:#faf5e9}.toolbar{display:flex;gap:10px;flex-wrap:wrap;margin:16px 0}.toolbar input{flex:1;min-width:200px}.toolbar select{max-width:100%}.count{font-size:13px;color:var(--muted);margin-bottom:10px}.tablewrap{overflow:auto}table{border-collapse:collapse;width:100%;font-size:14px;text-align:left}th{color:var(--muted);font-size:12px;background:#f6f8fb;white-space:nowrap}th,td{padding:12px;border-bottom:1px solid var(--line);vertical-align:top}td.name{min-width:270px;overflow-wrap:anywhere}td.groupcell{min-width:165px;max-width:235px}.badge{display:inline-block;padding:3px 9px;border-radius:20px;font-size:12px;font-weight:750;white-space:nowrap}.badge.PASS{background:#dff4e9;color:var(--pass)}.badge.FAIL{background:#fde8eb;color:var(--fail)}.badge.ERROR{background:#ffeddb;color:var(--error)}.badge.SKIP{background:#eaeef3;color:var(--muted)}.condition{font-size:11px;display:block;color:#805900;margin-top:4px}details{margin-top:8px}summary{cursor:pointer;color:#385b83}pre{white-space:pre-wrap;overflow-wrap:anywhere;background:#f7f9fc;padding:12px;border-radius:7px;max-height:340px;overflow:auto;font-size:12px;line-height:1.6}.empty{text-align:center;padding:24px;color:var(--muted)}footer{font-size:12px;color:var(--muted);margin-top:20px}code{font-size:13px}#conditional:empty,#issues:empty{display:none}
@media(max-width:760px){main{padding:22px 14px}header{align-items:flex-start;flex-direction:column}h1{font-size:25px}.cards{grid-template-columns:repeat(2,minmax(0,1fr))}.card:first-child{grid-column:1/-1}.groups{grid-template-columns:1fr}.panel{padding:14px}}
@media print{body{background:white}main{max-width:none;padding:0}header button,.toolbar,#showProblems{display:none}.panel,.card,.group{break-inside:avoid}.tablewrap{overflow:visible}details pre{max-height:none}th,td{padding:7px}.cards{gap:8px}.card strong{font-size:26px}}
</style>
</head>
<body><main>
<header><div><div class="eyebrow">NHÓM 4 · WHITE BOX TESTING</div><h1>Kết quả kiểm thử hộp trắng</h1><p>Tổng hợp báo cáo PHPUnit và JavaScript đã chạy.</p><div class="meta" id="created"></div></div><button id="print">In / lưu PDF</button></header>
<div class="cards" id="cards"></div>
<div class="note">Đây là bản tổng hợp tại thời điểm tạo file. Muốn cập nhật: chạy lại bộ kiểm thử, sau đó chạy <code>php build_whitebox_dashboard.php</code> và tải lại trang. Tỷ lệ test đạt không phải độ bao phủ mã nguồn.</div>
<div id="conditional" class="note warning"></div><div id="issues" class="note warning"></div>
<section class="panel"><h2>Các nhóm kiểm thử</h2><p class="meta">Chọn một nhóm để xem chi tiết. Mỗi nhóm chỉ lấy một file báo cáo; không cộng trùng bản sao.</p><div id="groups" class="groups"></div></section>
<section class="panel"><h2>Chi tiết từng ca kiểm thử</h2>
<div class="toolbar"><input id="search" type="search" aria-label="Tìm ca kiểm thử" placeholder="Tìm tên test hoặc nội dung lỗi…"><select id="groupFilter" aria-label="Lọc nhóm"><option value="">Tất cả nhóm</option></select><select id="statusFilter" aria-label="Lọc trạng thái"><option value="">Tất cả trạng thái</option><option>PASS</option><option>FAIL</option><option>ERROR</option><option>SKIP</option></select><button id="showProblems" class="primary">Chỉ xem FAIL / ERROR</button><button id="reset">Đặt lại</button></div>
<div id="count" class="count" aria-live="polite"></div><div class="tablewrap"><table><thead><tr><th>Trạng thái</th><th>Ca kiểm thử / chi tiết lỗi</th><th>Nhóm</th><th>Assertions</th><th>Thời gian</th></tr></thead><tbody id="tests"></tbody></table></div></section>
<footer>PASS: đạt các assertion đã chạy · FAIL: assertion không đạt · ERROR: lỗi khi thực thi · SKIP: bỏ qua.<br>Kiểm tra giao diện trình duyệt thực tế và số liệu coverage chưa được xác nhận bởi trang tổng hợp này. Số ca FAIL / ERROR không tương đương số lỗi độc lập của phần mềm.</footer>
</main><script id="report-data" type="application/json">__REPORT_DATA__</script>
<script>
'use strict';
const report=JSON.parse(document.getElementById('report-data').textContent);
const $=id=>document.getElementById(id);
const labels=new Map(report.groups.map(g=>[g.key,g.label]));
const make=(tag,text,cls)=>{const e=document.createElement(tag);if(text!==undefined)e.textContent=text;if(cls)e.className=cls;return e;};
const counts=rows=>{const n={PASS:0,FAIL:0,ERROR:0,SKIP:0};for(const r of rows)n[r.status]++;return n;};
const formatDate=s=>new Date(s).toLocaleString('vi-VN');
$('created').textContent='Tạo báo cáo: '+formatDate(report.created)+' · Đã đọc '+report.groups.filter(g=>!g.issue).length+'/'+report.groups.length+' nhóm';
const all=counts(report.rows);
for(const [label,value,cls] of [['Tổng ca đã đọc',report.rows.length,''],['PASS',all.PASS,'pass'],['FAIL',all.FAIL,'fail'],['ERROR',all.ERROR,'error'],['SKIP',all.SKIP,'']]){const c=make('div',undefined,'card');c.append(make('span',label),make('strong',String(value),cls));$('cards').append(c);}
const conditional=report.rows.filter(r=>r.conditional);
if(conditional.length){const c=counts(conditional);$('conditional').textContent='Yêu cầu cần xác nhận: '+conditional.length+' ca tìm kiếm không dấu ('+c.PASS+' PASS, '+c.FAIL+' FAIL, '+c.ERROR+' ERROR) đã nằm trong tổng số ở trên. Chỉ kết luận đây là lỗi yêu cầu khi báo cáo / đặc tả quy định tìm không dấu phải khớp tên có dấu.';}
const issues=report.groups.filter(g=>g.issue);
if(issues.length){$('issues').append(make('strong','Còn '+issues.length+' nhóm chưa tổng hợp được:'));const ul=make('ul');for(const g of issues)ul.append(make('li',g.label+': '+g.issue));$('issues').append(ul);}
for(const g of report.groups){const option=make('option',g.label);option.value=g.key;$('groupFilter').append(option);const b=make('button',undefined,'group'+(g.issue?' missing':''));b.type='button';const heading=make('div',undefined,'heading');heading.append(make('span',g.label),make('span',g.count+' ca'));b.append(heading);if(g.issue){b.append(make('small',g.issue));}else{const c=counts(report.rows.filter(r=>r.group===g.key));b.append(make('div',c.PASS+' PASS · '+c.FAIL+' FAIL · '+c.ERROR+' ERROR · '+c.SKIP+' SKIP','counts'),make('small',g.file+' · '+formatDate(g.modified)));}if(g.conditional)b.append(make('small','Điều kiện: cần xác nhận yêu cầu tìm kiếm không dấu.'));b.onclick=()=>{$('groupFilter').value=g.key;$('statusFilter').value='';problemsOnly=false;render();$('count').scrollIntoView({behavior:'smooth',block:'center'});};$('groups').append(b);}
let problemsOnly=false;
function render(){const query=$('search').value.trim().toLocaleLowerCase('vi');const group=$('groupFilter').value;const status=$('statusFilter').value;const rows=report.rows.filter(r=>(!group||r.group===group)&&(!status||r.status===status)&&(!problemsOnly||r.status==='FAIL'||r.status==='ERROR')&&(!query||(r.name+' '+r.message+' '+labels.get(r.group)).toLocaleLowerCase('vi').includes(query)));$('tests').replaceChildren();for(const r of rows){const tr=make('tr');const s=make('td');s.append(make('span',r.status,'badge '+r.status));if(r.conditional)s.append(make('span','Cần xác nhận yêu cầu','condition'));const name=make('td',undefined,'name');name.append(make('div',r.name));if(r.message){const details=make('details');details.append(make('summary','Xem chi tiết'),make('pre',r.message));name.append(details);}tr.append(s,name,make('td',labels.get(r.group),'groupcell'),make('td',r.assertions===null?'—':String(r.assertions)),make('td',Number(r.seconds).toFixed(4)+' s'));$('tests').append(tr);}if(!rows.length){const tr=make('tr');const td=make('td',report.rows.length?'Không có ca kiểm thử phù hợp bộ lọc.':'Chưa đọc được kết quả. Hãy đặt script cạnh artisan và kiểm tra thư mục reports.','empty');td.colSpan=5;tr.append(td);$('tests').append(tr);}$('count').textContent='Hiển thị '+rows.length+' / '+report.rows.length+' ca'+(problemsOnly?' · Đang lọc FAIL / ERROR':'');$('showProblems').textContent=problemsOnly?'Hiện tất cả trạng thái':'Chỉ xem FAIL / ERROR';}
$('search').oninput=render;$('groupFilter').onchange=render;$('statusFilter').onchange=()=>{problemsOnly=false;render();};$('showProblems').onclick=()=>{problemsOnly=!problemsOnly;$('statusFilter').value='';render();};$('reset').onclick=()=>{$('search').value='';$('groupFilter').value='';$('statusFilter').value='';problemsOnly=false;render();};$('print').onclick=()=>window.print();render();
</script></body></html>
HTML;

if (!is_dir($reportDir) && !mkdir($reportDir, 0777, true) && !is_dir($reportDir)) {
    fwrite(STDERR, "Cannot create reports directory.\n");
    exit(1);
}
$output = $reportDir . DIRECTORY_SEPARATOR . 'whitebox-dashboard.html';
if (file_put_contents($output, str_replace('__REPORT_DATA__', $payload, $html)) === false) {
    fwrite(STDERR, "Cannot write dashboard HTML.\n");
    exit(1);
}
echo 'Dashboard: ' . $output . PHP_EOL;
echo 'Test cases read: ' . count($rows) . PHP_EOL;
$missing = count(array_filter($groups, static fn (array $g): bool => $g['issue'] !== null));
if ($missing) {
    echo 'Missing/unreadable report groups: ' . $missing . ' (see dashboard).' . PHP_EOL;
}
echo 'PowerShell: Start-Process .\reports\whitebox-dashboard.html' . PHP_EOL;
