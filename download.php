<?php
$uploadDir = "uploads/";
 
if (isset($_GET['file'])) {
    $filename = basename($_GET['file']); // keamanan: ambil basename saja
    $filePath = $uploadDir . $filename;
 
    if (file_exists($filePath)) {
        // Set header untuk download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        flush();
        readfile($filePath);
        exit;
    } else {
        header("Location: upload.php?error=notfound");
        exit;
    }
} else {
    header("Location: upload.php");
    exit;
}
?>