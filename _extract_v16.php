<?php
// Extract what we need from v1.6 to apply to blade
$v16   = file_get_contents('HTMLe/ENESA_Formularz_HTML_Master_v1_6.html');
$v14   = file_get_contents('HTMLe/ENESA_Formularz_HTML_Master_v1_4.html');

$lines16 = explode("\n", $v16);

// ---- 1. Find body elements unique to v1.6 ----
echo "=== HTML ELEMENTS UNIQUE TO v1.6 ===\n";
$searchIds = ['audit-type-selector','audit-cards-container','show-all-btn-startup','audit-comparison-table','mode-enesa-banner'];
foreach ($searchIds as $id) {
    foreach ($lines16 as $i => $line) {
        if (stripos($line, $id) !== false) {
            echo "\n[id=$id at L".($i+1)."]:\n";
            for ($j = $i; $j < min(count($lines16), $i+60); $j++) {
                echo $lines16[$j]."\n";
                if ($j > $i && str_contains($lines16[$j], '</div>')) {
                    break; // first closing div
                }
            }
            break;
        }
    }
}

// ---- 2. Show context around audit-type-selector to understand placement ----
echo "\n\n=== PLACEMENT CONTEXT (8 lines before first new element) ===\n";
foreach ($lines16 as $i => $line) {
    if (stripos($line, 'audit-type-selector') !== false) {
        for ($j = max(0,$i-8); $j <= $i+2; $j++) {
            echo "L".($j+1).": ".$lines16[$j]."\n";
        }
        break;
    }
}

// ---- 3. Extract JS section from v1.6 ----
$js16Start = strrpos($v16, '<script>') + 8;
$js16End   = strrpos($v16, '</script>');
$js16 = substr($v16, $js16Start, $js16End - $js16Start);

// ---- 4. Extract ONLY programmer's 8 new functions ----
$newFns = ['renderAuditCards','enesaSelectAuditFromCard','countFieldsForProfile',
           'applyProfileSectionVisibility','enesaToggleComparisonTable',
           'enesaShowAllSections','updateProgressInfo','initArchitectureC'];

echo "\n\n=== 8 NEW JS FUNCTIONS (programmer) ===\n";
foreach ($newFns as $fn) {
    $pos = strpos($js16, "function $fn(");
    if ($pos === false) $pos = strpos($js16, "function $fn (");
    if ($pos !== false) {
        // Find the start of comment block before function
        $lineStart = strrpos(substr($js16, 0, $pos), "\n") + 1;
        $braceStart = strpos($js16, '{', $pos);
        $depth = 0;
        $end = $braceStart;
        for ($k = $braceStart; $k < strlen($js16); $k++) {
            if ($js16[$k] === '{') $depth++;
            elseif ($js16[$k] === '}') {
                $depth--;
                if ($depth === 0) { $end = $k; break; }
            }
        }
        $funcCode = substr($js16, $lineStart, $end - $lineStart + 1);
        echo "\n// --- $fn ---\n" . trim($funcCode) . "\n";
    } else {
        echo "\n// --- $fn --- NOT FOUND\n";
    }
}

// ---- 5. initArchitectureC call sites ----
echo "\n\n=== initArchitectureC call in v1.6 ===\n";
foreach ($lines16 as $i => $line) {
    if (stripos($line, 'initArchitectureC') !== false) {
        echo "L".($i+1).": ".$line."\n";
    }
}

// ---- 6. mode-enesa banner ----
echo "\n\n=== mode-enesa-banner in v1.6 ===\n";
foreach ($lines16 as $i => $line) {
    if (stripos($line, 'enesa-banner') !== false || stripos($line, 'mode-enesa') !== false) {
        echo "L".($i+1).": ".$line."\n";
    }
}
echo "\nDONE\n";

// --- 1. Find where audit-type-selector HTML block is in v1_6 ---
// Find by ID
$startMarker = 'id="audit-type-selector"';
$pos = strpos($v16, $startMarker);
if ($pos === false) { die("audit-type-selector not found!\n"); }
// Walk back to opening <div
$start = strrpos($v16, '<div', $pos - strlen($v16)) ? strrpos(substr($v16, 0, $pos), '<div') : $pos;
// Walk forward to matching </div>
$depth = 0; $i = $start; $len = strlen($v16);
while ($i < $len) {
    if (substr($v16, $i, 4) === '<div') $depth++;
    if (substr($v16, $i, 6) === '</div>') { $depth--; if ($depth === 0) { $end = $i + 6; break; } }
    $i++;
}
$auditSelectorHtml = substr($v16, $start, $end - $start);
echo "=== AUDIT-TYPE-SELECTOR HTML ===\n";
echo "Length: " . strlen($auditSelectorHtml) . " chars\n";
echo "Lines: " . substr_count($auditSelectorHtml, "\n") . "\n";
echo "First 200 chars: " . substr($auditSelectorHtml, 0, 200) . "\n\n";

// Is it present in v1_4?
echo "In v1_4: " . (str_contains($v14, 'id="audit-type-selector"') ? 'YES' : 'NO') . "\n\n";

// --- 2. Find mode-enesa-banner ---
$bannerPos = strpos($v16, 'mode-enesa-banner');
if ($bannerPos !== false) {
    $bannerStart = strrpos(substr($v16, 0, $bannerPos), '<div');
    $depth = 0; $i = $bannerStart;
    while ($i < $len) {
        if (substr($v16, $i, 4) === '<div') $depth++;
        if (substr($v16, $i, 6) === '</div>') { $depth--; if ($depth === 0) { $bannerEnd = $i + 6; break; } }
        $i++;
    }
    $bannerHtml = substr($v16, $bannerStart, $bannerEnd - $bannerStart);
    echo "=== MODE-ENESA-BANNER HTML ===\n";
    echo trim($bannerHtml) . "\n\n";
}

// --- 3. Find show-all button in sidenav (nav) ---
$showAllPos = strpos($v16, 'show-all-toggle');
if ($showAllPos !== false) {
    // Get the button/element
    $btnStart = strrpos(substr($v16, 0, $showAllPos), '<button');
    if ($btnStart === false) $btnStart = strrpos(substr($v16, 0, $showAllPos), '<div');
    $btnEnd = strpos($v16, '>', $showAllPos) + 1;
    // Find full element end
    $btnEnd = strpos($v16, "\n", $showAllPos) + 1;
    $showAllBtn = substr($v16, $btnStart, $btnEnd - $btnStart);
    echo "=== SHOW-ALL-TOGGLE ELEMENT ===\n";
    echo trim($showAllBtn) . "\n\n";
}

// --- 4. Find progress info element ---
$progressPos = strpos($v16, 'audit-progress-info');
if ($progressPos !== false) {
    $pStart = strrpos(substr($v16, 0, $progressPos), '<div');
    $depth = 0; $i = $pStart;
    while ($i < $len) {
        if (substr($v16, $i, 4) === '<div') $depth++;
        if (substr($v16, $i, 6) === '</div>') { $depth--; if ($depth === 0) { $pEnd = $i + 6; break; } }
        $i++;
    }
    $progressHtml = substr($v16, $pStart, $pEnd - $pStart);
    echo "=== AUDIT-PROGRESS-INFO HTML ===\n";
    echo trim($progressHtml) . "\n\n";
}

// --- 5. Find where audit-type-selector sits in relation to the body ---
// What comes before and after?
echo "=== CONTEXT AROUND AUDIT-TYPE-SELECTOR IN V1_6 ===\n";
$context = substr($v16, max(0, $start - 300), 300) . "\n[[[ AUDIT-TYPE-SELECTOR ]]]\n" . substr($v16, $end, 200);
echo $context . "\n\n";

// --- 6. Extract new JS functions ---
$jsStart = strrpos($v16, '<script>') + 8;
$jsEnd = strrpos($v16, '</script>');
$js16 = substr($v16, $jsStart, $jsEnd - $jsStart);

$newFnsToExtract = ['renderAuditCards', 'enesaSelectAuditFromCard', 'countFieldsForProfile',
    'applyProfileSectionVisibility', 'enesaToggleComparisonTable', 'enesaShowAllSections',
    'updateProgressInfo', 'initArchitectureC'];

foreach ($newFnsToExtract as $fn) {
    echo "=== JS: $fn ===\n";
    // Find function definition
    $fnPos = strpos($js16, "function $fn(");
    if ($fnPos === false) { 
        // Try arrow/const form
        $fnPos = strpos($js16, "$fn = function");
        if ($fnPos === false) { echo "NOT FOUND\n\n"; continue; }
    }
    // Find opening brace
    $braceStart = strpos($js16, '{', $fnPos);
    $depth = 0; $i = $braceStart;
    while ($i < strlen($js16)) {
        if ($js16[$i] === '{') $depth++;
        if ($js16[$i] === '}') { $depth--; if ($depth === 0) { $fnEnd = $i + 1; break; } }
        $i++;
    }
    // Walk back to get full declaration including preceding comment
    $declStart = strrpos(substr($js16, 0, $fnPos), "\n") + 1;
    // Check if there's a comment before
    $commentCheck = substr($js16, max(0, $declStart - 200), 200);
    if (preg_match('/\/\/[^\n]*\n\s*$/', $commentCheck, $mc, PREG_OFFSET_CAPTURE)) {
        $commentLineStart = $declStart - 200 + $mc[0][1];
        $declStart = $commentLineStart;
    }
    $fnCode = substr($js16, $declStart, $fnEnd - $declStart);
    echo strlen($fnCode) . " chars:\n";
    echo substr($fnCode, 0, 150) . "...\n\n";
}

// --- 7. Check what's around initArchitectureC call ---
echo "=== initArchitectureC CALL SITE ===\n";
$callPos = strpos($js16, 'initArchitectureC()');
if ($callPos !== false) {
    echo substr($js16, max(0, $callPos - 100), 300) . "\n";
}
