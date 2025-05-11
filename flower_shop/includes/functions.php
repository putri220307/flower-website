<?php
function getColorCode($colorName) {
    $colors = [
        'Biru' => '#0000FF',
        'Ungu' => '#800080',
        'Pink' => '#FFC0CB',
        'Kuning' => '#FFFF00',
        'Hijau' => '#008000',
        'Putih' => '#FFFFFF',
        'Merah' => '#FF0000',
        'Hitam' => '#000000'
    ];
    
    return $colors[$colorName] ?? '#584C43';
}

function getProductDescription($product) {
    $descriptions = [
        'Lavender' => "Lavender dengan warna ungu memiliki aroma menenangkan, sempurna untuk dekorasi rumah atau hadiah. Memberikan sentuhan elegan dan suasana damai di setiap ruang.<br><br>Keunggulan:<br>✔ Aroma menenangkan dan menyegarkan<br>✔ Cocok untuk dekorasi dan pengharum alami<br>✔ Mudah ditanam di iklim hangat dan kering",
        'Mawar Merah' => "Mawar merah klasik melambangkan cinta dan gairah. Sempurna untuk hadiah romantis atau dekorasi meja.<br><br>Keunggulan:<br>✔ Simbol cinta abadi<br>✔ Tahan lama sebagai bunga potong<br>✔ Cocok untuk semua acara spesial",
        'Dandelions' => "Dandelions dengan warna kuning Dandelion (Taraxacum officinale) adalah bunga liar yang terkenal dengan bentuknya yang unik dan kemampuannya menyebarkan biji melalui angin. Bunga ini berasal dari kawasan Eurasia, tetapi kini tumbuh secara luas di berbagai belahan dunia, termasuk di ladang, taman, dan pinggir jalan.",
        'Lilies' => " Keanggunan dalam Setiap Kelopak Bunga Lily melambangkan kemurnian, keanggunan, dan cinta yang tulus. Dengan kelopaknya yang lembut dan aroma yang memikat, Lily menjadi pilihan sempurna untuk berbagai momen spesial, seperti perayaan, ungkapan kasih sayang, atau sekadar mempercantik ruangan.",
    ];
    
    return $descriptions[$product['name']] ?? "Bunga indah dengan warna " . $product['color'] . ". Sempurna untuk dekorasi atau hadiah.";
}
?>