<?php

function require_json($file) {
    return json_decode(file_get_contents($file));
}

function parseXml($xml, callable $callback){
    $data = simplexml_load_string($xml);
    $data = json_decode(json_encode($data));

    return call_user_func($callback, empty($data), $data);
}
