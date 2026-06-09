<?php
// CORS头 - 允许前端跨域访问
header("Access-Control-Allow-Origin: *");

$img_array = glob("pics/landscape/*.{gif,jpg,png,webp}", GLOB_BRACE);

if (empty($img_array)) {
    header("HTTP/1.0 404 Not Found");
    exit;
}

$random_img = $img_array[array_rand($img_array)];

$image_info = getimagesize($random_img);
if ($image_info === false) {
    header("HTTP/1.0 500 Internal Server Error");
    exit;
}

$mime_type = $image_info['mime'];
$allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($mime_type, $allowed_mimes)) {
    header("HTTP/1.0 403 Forbidden");
    exit;
}

header("Content-Type: " . $mime_type);
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

readfile($random_img);
exit;
?>