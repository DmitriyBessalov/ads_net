<?php
$redis = new Redis();
$redis->pconnect('185.75.90.54', 6379);
$redis->select(0);
$cache=$redis->get('cdn_cache_update');
if ($cache) {
    $cache=str_replace('\/','/', $cache);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://my.selectel.ru/api/cdn/v1/projects/eea58cbe2da447ee97f58ec39edd2f97/resources/aaa4950a-8e38-466f-b36a-93c4c2b9051a/records/71dc89aa-e85b-4907-9277-9797e266b414/purge');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($cache),'X-Token: 5aUAb3gc59uag6DubPuANad7S_93166'));
    curl_setopt($ch, CURLOPT_ENCODING, 'deflate, gzip');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS,$cache);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);

    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpcode==204)
        $cache=$redis->del('cdn_cache_update');

    curl_close($ch);

}
$redis->close();