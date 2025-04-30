<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.0 403 Forbidden');
    exit('Access forbidden');
}

header('Content-Type: application/json');

$uploadDir = 'uploads/';
$maxFileSize = 5 * 1024 * 1024;
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    $errorMsg = 'Error upload file: ';
    
    switch ($_FILES['image']['error']) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            $errorMsg .= 'File terlalu besar';
            break;
        case UPLOAD_ERR_PARTIAL:
            $errorMsg .= 'File hanya terupload sebagian';
            break;
        case UPLOAD_ERR_NO_FILE:
            $errorMsg .= 'Tidak ada file yang diunggah';
            break;
        default:
            $errorMsg .= 'Error tidak diketahui';
    }
    
    echo json_encode(['success' => false, 'message' => $errorMsg]);
    exit;
}

$file = $_FILES['image'];
$fileName = $file['name'];
$fileTmpPath = $file['tmp_name'];
$fileSize = $file['size'];
$fileError = $file['error'];

$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if (!in_array($fileExt, $allowedExtensions)) {
    echo json_encode(['success' => false, 'message' => 'Format file tidak valid. Hanya JPG, JPEG, PNG, dan GIF yang diperbolehkan.']);
    exit;
}

if ($fileSize > $maxFileSize) {
    echo json_encode(['success' => false, 'message' => 'Ukuran file terlalu besar. Maksimal 5MB.']);
    exit;
}

$newFileName = uniqid() . '_' . time() . '.' . $fileExt;
$uploadPath = $uploadDir . $newFileName;

if (move_uploaded_file($fileTmpPath, $uploadPath)) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $protocol . $host . rtrim(dirname($_SERVER['PHP_SELF']), '/') . '/';
    $fileUrl = $baseUrl . $uploadPath;
    
    echo json_encode([
        'success' => true, 
        'message' => 'File berhasil diupload',
        'file_name' => $newFileName,
        'file_url' => $fileUrl
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal mengupload file. Silakan coba lagi.']);
}
?>
