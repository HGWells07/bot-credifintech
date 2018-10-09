<?php
function obtenerListaImagenes($images){
    $i = 0;
    $urls = '';
    foreach ($images as $image) {
        $url = $image->getUrl(); // The direct url
        $i++;
        $urls.="Imagen $i.- $url ";
    }
    return $urls;
}
?>