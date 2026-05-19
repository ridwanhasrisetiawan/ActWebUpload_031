<?php
$uploadDir = "uploads/";
 
if (isset($_GET['file'])) {
    $filename = basename($_GET['file']); // keamanan: ambil basename saja
    $filePath = $uploadDir . $filename;
 
    if (file_exists($filePath)) {
        unlink($filePath);
        header("Location: upload.php?deleted=" . urlencode($filename));
    } else {
        header("Location: upload.php?error=notfound");
    }
} else {
    header("Location: upload.php");
}
exit;
?>