<?php

function validateFile(string $field, array $rules = []): array
{
    $allowed = [
        'application/pdf' => 'pdf',
        'image/jpeg'      => 'jpg',
        'image/png'       => 'png',
    ];

    // Check existence
    if (!isset($_FILES[$field])) {
        return ['error' => "$field is missing."];
    }

    $file = $_FILES[$field];

    // Upload error handling
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => "$field upload error code: " . $file['error']];
    }

    // Size check
    if (isset($rules['max_size']) && $file['size'] > $rules['max_size']) {
        return ['error' => "$field exceeds max size."];
    }

    // Ensure valid uploaded file
    if (!is_uploaded_file($file['tmp_name'])) {
        return ['error' => "$field is not a valid uploaded file."];
    }

    // MIME detection
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    if ($mime === false) {
        return ['error' => "$field cannot determine file type."];
    }

    // Restrict allowed MIME types if provided
    if (isset($rules['mime'])) {
        $allowed = array_filter(
            $allowed,
            fn($k) => in_array($k, $rules['mime']),
            ARRAY_FILTER_USE_KEY
        );
    }

    if (!isset($allowed[$mime])) {
        return ['error' => "$field invalid file type."];
    }

    // PDF validation
    if ($mime === 'application/pdf') {
        $handle = fopen($file['tmp_name'], 'rb');
        if ($handle === false) {
            return ['error' => "$field cannot be read."];
        }

        $header = fread($handle, 5);
        fclose($handle);

        if ($header !== '%PDF-') {
            return ['error' => "$field is not a valid PDF."];
        }
    }

    // Image validation
    if (str_starts_with($mime, 'image/')) {
        // Optional: prevent heavy processing on huge images
        if ($file['size'] > (5 * 1024 * 1024)) {
            return ['error' => "$field image too large for validation."];
        }

        if (!@getimagesize($file['tmp_name'])) {
            return ['error' => "$field is not a valid image."];
        }
    }

    return [
        'file' => $file,
        'mime' => $mime,
        'ext'  => $allowed[$mime]
    ];
}
/* controller
==================================
## upload function
$attachment = $_POST['attachment'];
$fileResult = validateFile($attachment, [
    'max_size' => 10 * 1024 * 1024, // 5MB
    'mime' => [
        'application/pdf',
        'image/jpeg',
        'image/png'
    ]
]);


if (isset($fileResult['error'])) {
    echo json_encode([
        'type' => 'error',
        'message' => $fileResult['error']
    ]);
    return;
}


$storagePath = 'attachments/' . $path . '/');

$storage = new Storage($storagePath);

$prefix = strtoupper(substr($data['file-category'] ?? 'file', 0, 3)); // just for file name, can name whatever you want
// $storedFilename = $prefix . '_' . bin2hex(random_bytes(4));
$filename = $storage->save(
    $fileResult['file'],
    $fileResult['ext'],
    $prefix
);

if (!$filename) {
    echo json_encode([
        'type' => 'error',
        'message' => 'Failed to save file'
    ]);
    return;
}

// save data to db (file info)
$basePublicPath = '/storage/' . trim($storagePath, '/') . '/'; // storage folder + file folder
$data['file_path'] = $basePublicPath . $filename; // for viewing, saved in db
$data['file_name'] = $filename;
$data['file_type'] = $fileResult['mime'];
$data['file_size'] = $fileResult['file']['size'];
$data['file_ext']  = $fileResult['ext'];
===========================================

## view function
public function viewAttachment()
    {
        $file = $_GET['file'] ?? '';

        if (!$file) {
            http_response_code(400);
            echo "File not specified";
            return;
        }

        // Normalize (remove /storage prefix)
        if (str_starts_with($file, '/storage/')) {
            $file = substr($file, strlen('/storage/'));
        }

        // Prevent traversal
        $file = str_replace(['../', './'], '', $file);

        // Build full path
        $fullPath = rtrim(STORAGE_PATH, '/') . '/' . ltrim($file, '/');

        if (!file_exists($fullPath)) {
            http_response_code(404);
            echo "File not found";
            return;
        }

        // Detect mime
        $mime = mime_content_type($fullPath);

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($fullPath));
        header('Content-Disposition: inline; filename="' . basename($fullPath) . '"');

        readfile($fullPath);
        exit;
    }
*/