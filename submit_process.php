<?php
session_start();
require_once "config.php";

// Check user login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['submit'])) {

    $title       = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $category    = $conn->real_escape_string($_POST['category']);
    $creator_id  = $_SESSION['user_id'];

    // ✅ Allowed file extensions
    $allowed_extensions = [
        'jpg','jpeg','png','gif',
        'pdf',
        'doc','docx',
        'ppt','pptx',
        'xls','xlsx',
        'mp4','avi','mov','mkv',
        'mp3','wav',
        'zip','rar',
        'txt'
    ];

    $fileName  = $_FILES['file']['name'];
    $fileTmp   = $_FILES['file']['tmp_name'];
    $fileSize  = $_FILES['file']['size'];
    $fileExt   = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // ✅ Check extension is allowed
    if (!in_array($fileExt, $allowed_extensions)) {
        echo "<script>alert('File type not allowed!'); window.location='submit.php';</script>";
        exit();
    }

    // ✅ Max file size: 50MB
    if ($fileSize > 50 * 1024 * 1024) {
        echo "<script>alert('File too large! Max 50MB allowed.'); window.location='submit.php';</script>";
        exit();
    }

    // ✅ Rename file to avoid conflicts
    $newFileName = time() . '_' . basename($fileName);
    $uploadDir   = "uploads/";
    $filePath    = $uploadDir . $newFileName;

    // ✅ Generate thumbnail path for images, null for other files
    $thumbnail_path = NULL;
    $image_extensions = ['jpg','jpeg','png','gif'];
    if (in_array($fileExt, $image_extensions)) {
        $thumbnail_path = $filePath; // use same file as thumbnail for images
    }

    if (move_uploaded_file($fileTmp, $filePath)) {

        // ✅ Updated INSERT to match new table structure
        $stmt = $conn->prepare("INSERT INTO contents
            (title, description, category, file_path, thumbnail_path, creator_id, download_count, status, visibility) 
            VALUES (?,?,?,?,?,?,0,'pending','visible')");

        $stmt->bind_param("sssssi",
            $title,
            $description,
            $category,
            $filePath,
            $thumbnail_path,
            $creator_id
        );

        if ($stmt->execute()) {
            header("Location: success.php");
            exit();
        } else {
            echo "Database Error: " . $stmt->error;
        }

    } else {
        echo "<script>alert('File upload failed!'); window.location='submit.php';</script>";
    }
}
?>