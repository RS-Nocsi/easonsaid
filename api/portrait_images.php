<?php
// CORS头 - 允许前端跨域访问
header("Access-Control-Allow-Origin: *");

$img_array = glob("pics/portrait/*.{gif,jpg,png,webp}", GLOB_BRACE);

if (empty($img_array)) {
    // 如果没有图片，返回默认图片或错误
    header("HTTP/1.0 404 Not Found");
    exit;
}

$random_img = $img_array[array_rand($img_array)];

// 获取图片信息
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

// 设置正确的Content-Type
header("Content-Type: " . $mime_type);
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// 直接输出图片内容
readfile($random_img);
exit;
?>