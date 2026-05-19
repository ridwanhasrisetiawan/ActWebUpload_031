<?php
$upload_dir = "uploads/";
$message = "";
$message_type = "";
 
// Buat folder uploads jika belum ada
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
 
// ===== PROSES UPLOAD =====
if (isset($_POST['upload'])) {
    $file = $_FILES['fileToUpload'];
    $filename = basename($file['name']);
    $target = $upload_dir . $filename;
 
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $message = "Gagal mengunggah file.";
        $message_type = "error";
    } elseif (file_exists($target)) {
        $message = "File '$filename' sudah ada.";
        $message_type = "error";
    } elseif ($file['size'] > 500000) {
        $message = "File terlalu besar (maks. 500KB).";
        $message_type = "error";
    } elseif (move_uploaded_file($file['tmp_name'], $target)) {
        $message = "File '$filename' berhasil diunggah!";
        $message_type = "success";
    } else {
        $message = "Terjadi kesalahan saat mengunggah.";
        $message_type = "error";
    }
}
 
// ===== PROSES DELETE =====
if (isset($_GET['delete'])) {
    $filename = basename($_GET['delete']);
    $filepath = $upload_dir . $filename;
    if (file_exists($filepath)) {
        unlink($filepath);
        $message = "File '$filename' berhasil dihapus.";
        $message_type = "success";
    } else {
        $message = "File tidak ditemukan.";
        $message_type = "error";
    }
}
 
// ===== PROSES DOWNLOAD =====
if (isset($_GET['download'])) {
    $filename = basename($_GET['download']);
    $filepath = $upload_dir . $filename;
    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    } else {
        $message = "File tidak ditemukan.";
        $message_type = "error";
    }
}
 
// ===== AMBIL DAFTAR FILE =====
$files = array_diff(scandir($upload_dir), ['.', '..']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Web Upload</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: 40px auto; padding: 0 20px; background: #f5f5f5; }
        h1 { text-align: center; color: #333; }
        h2 { color: #555; border-bottom: 2px solid #ddd; padding-bottom: 8px; }
 
        /* Form Upload */
        .upload-form { background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 24px; }
        .upload-form input[type="file"] { display: block; margin: 12px 0; }
        .upload-form input[type="submit"] { background: #2563eb; color: #fff; border: none; padding: 10px 24px; border-radius: 6px; cursor: pointer; font-size: 15px; }
        .upload-form input[type="submit"]:hover { background: #1d4ed8; }
 
        /* Preview */
        #preview-container { margin: 12px 0; min-height: 40px; }
        #preview-img { max-width: 100%; max-height: 200px; border-radius: 6px; display: none; border: 1px solid #ddd; }
        #preview-name { font-size: 14px; color: #555; margin-top: 6px; }
 
        /* Pesan */
        .msg { padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; font-weight: bold; }
        .success { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
        .error   { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
 
        /* Tabel File */
        .file-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .file-table th { background: #2563eb; color: #fff; padding: 12px 14px; text-align: left; }
        .file-table td { padding: 10px 14px; border-bottom: 1px solid #eee; vertical-align: middle; }
        .file-table tr:last-child td { border-bottom: none; }
        .file-table tr:hover td { background: #f0f7ff; }
 
        /* Tombol aksi */
        .btn { display: inline-block; padding: 6px 14px; border-radius: 5px; text-decoration: none; font-size: 13px; font-weight: bold; }
        .btn-download { background: #2563eb; color: #fff; margin-right: 6px; }
        .btn-download:hover { background: #1d4ed8; }
        .btn-delete { background: #dc2626; color: #fff; }
        .btn-delete:hover { background: #b91c1c; }
 
        .empty { text-align: center; color: #999; padding: 24px; font-style: italic; }
    </style>
</head>
<body>
 
<h1>📁 Web Upload</h1>
 
<?php if ($message): ?>
<div class="msg <?= $message_type ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
 
<!-- FORM UPLOAD -->
<div class="upload-form">
    <h2>Unggah File</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <label>Pilih file:</label>
        <input type="file" name="fileToUpload" id="fileToUpload" onchange="previewFile(this)">
 
        <!-- PREVIEW -->
        <div id="preview-container">
            <img id="preview-img" src="" alt="Preview">
            <div id="preview-name"></div>
        </div>
 
        <input type="submit" name="upload" value="Unggah File">
    </form>
</div>
 
<!-- DAFTAR FILE -->
<h2>File Tersimpan (<?= count($files) ?> file)</h2>
 
<?php if (empty($files)): ?>
    <p class="empty">Belum ada file yang diunggah.</p>
<?php else: ?>
<table class="file-table">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama File</th>
            <th>Ukuran</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php $no = 1; foreach ($files as $file):
        $path = $upload_dir . $file;
        $size = filesize($path);
        $humanSize = $size < 1024 ? $size . ' B'
            : ($size < 1048576 ? round($size/1024, 1) . ' KB'
            : round($size/1048576, 1) . ' MB');
    ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($file) ?></td>
            <td><?= $humanSize ?></td>
            <td>
                <a href="?download=<?= urlencode($file) ?>" class="btn btn-download">⬇ Unduh</a>
                <a href="?delete=<?= urlencode($file) ?>" class="btn btn-delete"
                   onclick="return confirm('Hapus file ini?')">🗑 Hapus</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
 
<script>
function previewFile(input) {
    const img = document.getElementById('preview-img');
    const nameDiv = document.getElementById('preview-name');
 
    if (!input.files || !input.files[0]) return;
 
    const file = input.files[0];
    nameDiv.textContent = file.name + ' (' + (file.size < 1024 ? file.size + ' B' : (file.size < 1048576 ? (file.size/1024).toFixed(1) + ' KB' : (file.size/1048576).toFixed(2) + ' MB')) + ')';
 
    // Tampilkan preview jika gambar
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; img.style.display = 'block'; };
        reader.readAsDataURL(file);
    } else {
        img.style.display = 'none';
    }
}
</script>
 
</body>
</html>