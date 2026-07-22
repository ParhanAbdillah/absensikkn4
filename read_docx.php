<?php
$z = new ZipArchive();
if ($z->open('FORMAT ABSENSI.docx') === true) {
    $xml = $z->getFromName('word/document.xml');
    $content = preg_replace('/<[^>]+>/', ' ', $xml);
    echo trim($content);
    $z->close();
} else {
    echo "Failed to open zip";
}
