<?php
// datouzai_icon.php
// CORS头 - 允许前端跨域访问
header("Access-Control-Allow-Origin: *");

$img_array = glob("icon/*.{gif,jpg,png,ico,webp}", GLOB_BRACE);

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
$allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/x-icon', 'image/vnd.microsoft.icon'];
if (!in_array($mime_type, $allowed_mimes)) {
    header("HTTP/1.0 403 Forbidden");
    exit;
}

// 完全禁用所有缓存
header("Content-Type: " . $mime_type);
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// 添加随机数强制刷新
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

readfile($random_img);
exit;
?>