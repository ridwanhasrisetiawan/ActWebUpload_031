<?php
$target_dir = "uploads/";
if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
 
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$uploadMessage = "";
$uploadSuccess = false;
$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
 
if (isset($_POST["submit"])) {
 
    // Periksa apakah berkas sudah ada
    if (file_exists($target_file)) {
        $uploadMessage = "⚠️ Maaf, berkas sudah ada.";
        $uploadOk = 0;
    }
 
    // Periksa ukuran berkas (500KB)
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        $uploadMessage = "⚠️ Maaf, berkas Anda terlalu besar (maks. 500KB).";
        $uploadOk = 0;
    }
 
    // Proses upload
    if ($uploadOk == 0) {
        if (empty($uploadMessage)) $uploadMessage = "❌ Maaf, berkas Anda tidak dapat diunggah.";
        $uploadSuccess = false;
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $uploadMessage = "✅ Berkas \"" . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . "\" berhasil diunggah!";
            $uploadSuccess = true;
        } else {
            $uploadMessage = "❌ Maaf, terjadi kesalahan saat mengunggah berkas.";
            $uploadSuccess = false;
        }
    }
}
 
// Scan file untuk ditampilkan
$files = array_diff(scandir($target_dir), ['.', '..']);
$totalFiles = count($files);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Upload</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Syne:wght@400;600;800&display=swap" rel="stylesheet">
</head>
<body>
 
<div class="noise"></div>
 
<div class="container">
    <header class="header">
        <div class="header-badge">FILE MANAGER</div>
        <h1 class="title">Web <span class="accent">Upload</span></h1>
        <p class="subtitle">Unggah, pratinjau, unduh &amp; hapus file dengan mudah</p>
    </header>
 
    <!-- UPLOAD SECTION -->
    <section class="upload-section">
        <form action="upload.php" method="post" enctype="multipart/form-data" id="uploadForm">
            <div class="drop-zone" id="dropZone">
                <div class="drop-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                </div>
                <p class="drop-text">Seret &amp; lepas file di sini</p>
                <p class="drop-sub">atau</p>
                <label for="fileToUpload" class="browse-btn">Pilih File</label>
                <input type="file" name="fileToUpload" id="fileToUpload" hidden>
                <p class="drop-limit">Maks. 500KB per file</p>
            </div>
 
            <!-- PREVIEW AREA -->
            <div class="preview-area" id="previewArea" style="display:none;">
                <div class="preview-header">
                    <span class="preview-label">PRATINJAU FILE</span>
                    <button type="button" class="clear-btn" id="clearBtn">✕ Batal</button>
                </div>
                <div class="preview-content" id="previewContent"></div>
                <div class="preview-info" id="previewInfo"></div>
                <button type="submit" name="submit" class="upload-btn" id="uploadBtn">
                    <span>Unggah File</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                </button>
            </div>
        </form>
 
        <?php if (!empty($uploadMessage)): ?>
        <div class="alert <?= $uploadSuccess ? 'alert-success' : 'alert-error' ?>">
            <?= $uploadMessage ?>
        </div>
        <?php endif; ?>
    </section>
 
    <!-- FILE LIST SECTION -->
    <section class="files-section">
        <div class="files-header">
            <h2 class="files-title">File Tersimpan</h2>
            <span class="files-count"><?= $totalFiles ?> file</span>
        </div>
 
        <div class="files-grid">
            <?php if ($totalFiles === 0): ?>
            <div class="empty-state">
                <div class="empty-icon">📂</div>
                <p>Belum ada file yang diunggah</p>
            </div>
            <?php else: ?>
            <?php foreach ($files as $file):
                $filePath = $target_dir . $file;
                $fileSize = filesize($filePath);
                $fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $isImage = in_array($fileExt, ['jpg','jpeg','png','gif','webp','svg']);
                $humanSize = $fileSize < 1024 ? $fileSize . ' B'
                    : ($fileSize < 1048576 ? round($fileSize/1024, 1) . ' KB'
                    : round($fileSize/1048576, 1) . ' MB');
                $icons = ['pdf'=>'📄','doc'=>'📝','docx'=>'📝','xls'=>'📊','xlsx'=>'📊',
                          'zip'=>'🗜️','rar'=>'🗜️','mp3'=>'🎵','mp4'=>'🎬','txt'=>'📃'];
            ?>
            <div class="file-card">
                <div class="file-thumb">
                    <?php if ($isImage): ?>
                        <img src="<?= htmlspecialchars($filePath) ?>" alt="<?= htmlspecialchars($file) ?>">
                    <?php else: ?>
                        <div class="file-icon"><?= $icons[$fileExt] ?? '📁' ?></div>
                    <?php endif; ?>
                    <div class="file-ext-badge"><?= strtoupper($fileExt) ?></div>
                </div>
                <div class="file-info">
                    <p class="file-name" title="<?= htmlspecialchars($file) ?>"><?= htmlspecialchars($file) ?></p>
                    <p class="file-size"><?= $humanSize ?></p>
                </div>
                <div class="file-actions">
                    <a href="download.php?file=<?= urlencode($file) ?>" class="btn-download" title="Unduh">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Unduh
                    </a>
                    <a href="delete.php?file=<?= urlencode($file) ?>"
                       class="btn-delete"
                       onclick="return confirm('Hapus file <?= htmlspecialchars($file) ?>?')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14H6L5 6"/>
                            <path d="M10 11v6M14 11v6"/>
                            <path d="M9 6V4h6v2"/>
                        </svg>
                        Hapus
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>
 
<script src="script.js"></script>
</body>
</html>