<?php

$im = imagecreatetruecolor(800, 600);
$bg = imagecolorallocate($im, 20, 20, 20);
imagefilledrectangle($im, 0, 0, 800, 600, $bg);
$gold = imagecolorallocate($im, 201, 164, 92);
$white = imagecolorallocate($im, 200, 200, 200);
imagerectangle($im, 40, 40, 760, 560, $gold);
imagestring($im, 5, 330, 280, 'KEMIN EXPORTS', $gold);
imagestring($im, 3, 310, 310, 'PREMIUM PACKAGING & PRINT', $white);
imagewebp($im, 'public/images/product-placeholder.webp', 85);
imagedestroy($im);

$im2 = imagecreatetruecolor(400, 300);
$bg2 = imagecolorallocate($im2, 245, 245, 245);
imagefilledrectangle($im2, 0, 0, 400, 300, $bg2);
$text2 = imagecolorallocate($im2, 100, 100, 100);
imagestring($im2, 5, 140, 140, 'PARTNER LOGO', $text2);
imagewebp($im2, 'public/images/company-placeholder.webp', 85);
imagedestroy($im2);

echo "Placeholders generated!\n";
