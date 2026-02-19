<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$configFile = 'config.json';
$uploadDir = 'uploads/';

// Bikin folder uploads otomatis
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Tangkap input mentah (JSON)
$rawInput = file_get_contents('php://input');
$jsonData = json_decode($rawInput, true);

// Deteksi action dari URL atau JSON
$action = isset($_GET['action']) ? $_GET['action'] : '';
if (!$action && isset($_POST['action'])) $action = $_POST['action'];
if (!$action && isset($jsonData['action'])) $action = $jsonData['action'];
if (!$action) $action = 'save_config'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // FUNGSI UPLOAD GAMBAR
    if ($action === 'upload') {
        if (isset($_FILES['image'])) {
            $fileName = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", basename($_FILES['image']['name']));
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                echo json_encode(["status" => "success"]);
            } else {
                echo json_encode(["status" => "error"]);
            }
        }
        exit;
    } 
    // FUNGSI HAPUS GAMBAR
    elseif ($action === 'delete') {
        $file = isset($jsonData['file']) ? $jsonData['file'] : '';
        if ($file && file_exists($file)) {
            unlink($file); // Eksekusi hapus file
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "File tidak ditemukan"]);
        }
        exit;
    } 
    // FUNGSI SAVE CONFIG
    elseif ($action === 'save_config') {
        if ($rawInput) {
            file_put_contents($configFile, $rawInput);
            echo json_encode(["status" => "success"]);
        }
        exit;
    }
} 
// METHOD GET
else {
    if ($action === 'get_images') {
        $images = [];
        if (is_dir($uploadDir)) {
            $files = scandir($uploadDir);
            foreach ($files as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $images[] = $uploadDir . $file;
                }
            }
        }
        echo json_encode($images);
        exit;
    } 
    elseif ($action === 'get_config') {
        if (file_exists($configFile)) {
            echo file_get_contents($configFile);
        } else {
            echo json_encode([]);
        }
        exit;
    }
}
?>