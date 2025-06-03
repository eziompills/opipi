<?php
/**
 * File-based caching helper.
 */
function cache_set(string $key, string $value, int $ttl = 300): void
{
    $dir = sys_get_temp_dir() . '/oplani_cache';
    if (!is_dir($dir)) mkdir($dir, 0700, true);
    $file = $dir . '/' . md5($key);
    file_put_contents($file, json_encode(['exp'=>time()+$ttl,'val'=>$value]));
}
function cache_get(string $key): ?string
{
    $file = sys_get_temp_dir() . '/oplani_cache/' . md5($key);
    if (!is_file($file)) return null;
    $data = json_decode(file_get_contents($file), true);
    if (!$data || $data['exp'] < time()) { @unlink($file); return null; }
    return $data['val'];
}
?>
