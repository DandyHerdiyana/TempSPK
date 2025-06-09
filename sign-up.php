<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define path constants
define('BASE_DIR', 'C:/xampp/htdocs/umtas/');
define('UPLOAD_DIR', 'uploads/students/');
define('FULL_UPLOAD_PATH', BASE_DIR . UPLOAD_DIR);

// 1. Check if files were uploaded
if (empty($_FILES)) {
    echo json_encode(['success' => false, 'message' => 'No files were uploaded']);
    exit;
}

// 2. Database connection
$db = new mysqli('localhost', 'root', '', 'mydatabase');
if ($db->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// 3. Improved file upload function
function uploadFile($fileInputName, $userId, $allowedTypes) {
    if (!isset($_FILES[$fileInputName]) {
        return ['success' => false, 'message' => 'No file uploaded'];
    }

    $file = $_FILES[$fileInputName];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error'];
    }

    // Validate file extension
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExt, $allowedTypes['extensions'])) {
        return ['success' => false, 'message' => 'Invalid file extension'];
    }

    // Create user-specific directory
    $userDir = FULL_UPLOAD_PATH . $userId . '/';
    if (!file_exists($userDir)) {
        mkdir($userDir, 0755, true);
    }

    // Generate unique filename
    $newFilename = uniqid() . '.' . $fileExt;
    $serverPath = $userDir . $newFilename;
    $relativePath = UPLOAD_DIR . $userId . '/' . $newFilename;

    // Move the file
    if (move_uploaded_file($file['tmp_name'], $serverPath)) {
        return [
            'success' => true, 
            'path' => $relativePath,
            'filename' => $newFilename
        ];
    }

    return ['success' => false, 'message' => 'File move failed'];
}

try {
    // 4. Validate required fields
    $requiredFields = ['email', 'nama_lengkap', 'no_hp', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field $field is required");
        }
    }

    // 5. Check if email exists
    $email = $db->real_escape_string($_POST['email']);
    $result = $db->query("SELECT id FROM users WHERE email = '$email'");
    if ($result->num_rows > 0) {
        throw new Exception('Email already registered');
    }

    // 6. File upload configurations
    $uploadConfig = [
        'foto' => [
            'extensions' => ['jpg', 'jpeg', 'png', 'webp'],
            'mime' => ['image/jpeg', 'image/png', 'image/webp']
        ],
        'ijazah' => [
            'extensions' => ['pdf'],
            'mime' => ['application/pdf']
        ],
        'raport' => [
            'extensions' => ['pdf'],
            'mime' => ['application/pdf']
        ]
    ];

    $db->begin_transaction();

    // 7. Insert basic user data
    $stmt = $db->prepare("INSERT INTO users (nama_lengkap, email, role) VALUES (?, ?, 'mahasiswa')");
    $stmt->bind_param("ss", $_POST['nama_lengkap'], $_POST['email']);
    $stmt->execute();
    $userId = $db->insert_id;

    // 8. Upload files
    $uploadedFiles = [];
    foreach ($uploadConfig as $field => $config) {
        $result = uploadFile($field, $userId, $config);
        if (!$result['success']) {
            throw new Exception("File $field upload failed: " . $result['message']);
        }
        $uploadedFiles[$field] = $result['path']; // This contains relative path
    }

    // 9. Insert complete registration data
    $stmt = $db->prepare("INSERT INTO pendaftaran (
        user_id, nama_lengkap, email, no_hp, jenis_kelamin, 
        tempat_lahir, tanggal_lahir, foto, ijazah, raport
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("isssssssss", 
        $userId,
        $_POST['nama_lengkap'],
        $_POST['email'],
        $_POST['no_hp'],
        $_POST['jenis_kelamin'],
        $_POST['tempat_lahir'],
        $_POST['tanggal_lahir'],
        $uploadedFiles['foto'],    // e.g. "uploads/students/123/abc123.jpg"
        $uploadedFiles['ijazah'],  // e.g. "uploads/students/123/def456.pdf"
        $uploadedFiles['raport']   // e.g. "uploads/students/123/ghi789.pdf"
    );
    $stmt->execute();

    $db->commit();
    echo json_encode(['success' => true, 'message' => 'Registration successful']);

} catch (Exception $e) {
    $db->rollback();
    
    // Clean up any uploaded files if transaction failed
    if (!empty($uploadedFiles)) {
        foreach ($uploadedFiles as $filePath) {
            $fullPath = BASE_DIR . $filePath;
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }
    
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}