<?php
// Padrão de todas as fotos: 600x800 px (proporção 3:4), salvas em JPEG, sem cortar a imagem.
const IMG_LARGURA   = 600;
const IMG_ALTURA    = 800;
const IMG_PASTA     = 'uploads/produtos/';   // relativa à raiz do projeto
const IMG_MAX_BYTES = 5 * 1024 * 1024;       // 5 MB

function salvar_imagem(array $arq): string {
    if (!function_exists('imagecreatetruecolor')) throw new RuntimeException('A extensão GD do PHP não está ativada (extension=gd no php.ini).');
    if ($arq['error'] === UPLOAD_ERR_INI_SIZE || $arq['error'] === UPLOAD_ERR_FORM_SIZE) throw new RuntimeException('Imagem maior que o limite do PHP (aumente upload_max_filesize e post_max_size no php.ini).');
    if ($arq['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Falha no envio da imagem (código ' . $arq['error'] . ').');
    if ($arq['size'] > IMG_MAX_BYTES)    throw new RuntimeException('Imagem maior que 5 MB.');

    $info = @getimagesize($arq['tmp_name']);
    if (!$info) throw new RuntimeException('Arquivo não é uma imagem.');

    switch ($info[2]) {
        case IMAGETYPE_JPEG: $origem = imagecreatefromjpeg($arq['tmp_name']); break;
        case IMAGETYPE_PNG:  $origem = imagecreatefrompng($arq['tmp_name']);  break;
        case IMAGETYPE_WEBP: $origem = imagecreatefromwebp($arq['tmp_name']); break;
        default: throw new RuntimeException('Use JPG, PNG ou WEBP.');
    }
    if (!$origem) throw new RuntimeException('Não foi possível ler a imagem.');

    // corrige a rotação de fotos tiradas no celular
    if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
        $exif   = @exif_read_data($arq['tmp_name']);
        $angulo = [3 => 180, 6 => -90, 8 => 90][$exif['Orientation'] ?? 1] ?? 0;
        if ($angulo) $origem = imagerotate($origem, $angulo, 0);
    }

    // encaixa a imagem inteira dentro de 600x800, sem cortar (sobra fundo branco nas laterais ou em cima/baixo)
    $w = imagesx($origem);
    $h = imagesy($origem);
    $escala = min(IMG_LARGURA / $w, IMG_ALTURA / $h);
    $nw = max(1, (int)round($w * $escala));
    $nh = max(1, (int)round($h * $escala));
    $dx = (int)((IMG_LARGURA - $nw) / 2);
    $dy = (int)((IMG_ALTURA - $nh) / 2);

    $destino = imagecreatetruecolor(IMG_LARGURA, IMG_ALTURA);
    imagefill($destino, 0, 0, imagecolorallocate($destino, 255, 255, 255)); // fundo branco (também p/ PNG transparente)
    imagecopyresampled($destino, $origem, $dx, $dy, 0, 0, $nw, $nh, $w, $h);

    $pasta = __DIR__ . '/../' . IMG_PASTA;
    if (!is_dir($pasta) && !mkdir($pasta, 0755, true)) throw new RuntimeException('Não foi possível criar a pasta de imagens.');

    $nome = bin2hex(random_bytes(8)) . '.jpg';
    if (!imagejpeg($destino, $pasta . $nome, 85)) throw new RuntimeException('Não foi possível salvar a imagem.');

    return IMG_PASTA . $nome;
}

function apagar_imagem(?string $caminho): void {
    if (!$caminho || strpos($caminho, IMG_PASTA) !== 0 || strpos($caminho, '..') !== false) return;
    $arquivo = __DIR__ . '/../' . $caminho;
    if (is_file($arquivo)) @unlink($arquivo);
}