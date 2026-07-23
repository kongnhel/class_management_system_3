<?php
$content = file_get_contents('test_output.pdf');
if (strpos($content, 'KhmerOSbattambang') !== false) {
    echo "Font name found in PDF\n";
}
if (preg_match('/[\x{1780}-\x{17FF}]/u', $content)) {
    echo "Khmer characters found in PDF\n";
} else {
    echo "No Khmer chars in PDF (expected for empty student list)\n";
}