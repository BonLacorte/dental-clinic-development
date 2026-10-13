<?php

function e($value): string
{
    return htmlspecialchars(is_scalar($value) ? (string)$value : '', ENT_QUOTES, 'UTF-8');
}

function je($data, $type = 'json') {
    if ($type === 'json') {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    // future types: html, xml, etc.
}

function uppercaseData(array $data, array $skip = []): array
{
    foreach ($data as $key => $value) {
        if (!in_array($key, $skip, true) && is_string($value)) {
            $data[$key] = strtoupper($value);
        }
    }
    return $data;
}

function uuidv4() {
    $data = random_bytes(16);

    // Set version to 4
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);

    // Set variant to 10xx
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function getRandomPart($length = 5) {
    //$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
    $l = strlen($chars) - 1;
    $id = '';
    for ($i = 0; $i < $length; $i++) {
        $id .= $chars[random_int(0, $l)];
    }
    return $id;
}

function sortByKey(&$array, $key, $asc = true) {
    usort($array, function ($a, $b) use ($key, $asc) {
        return $asc 
            ? $a[$key] <=> $b[$key]
            : $b[$key] <=> $a[$key];
    });
    return $array;
}