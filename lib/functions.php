<?php

function require_json($file) {
    return json_decode(file_get_contents($file));
}

function parse_xml($xml, callable $callback) {
    $data = null;
    try {
        $data = simplexml_load_string($xml);
        $data = json_decode(json_encode($data));
    } catch (Exception $e) {
     // do nothing
    }

    return call_user_func($callback, empty($data), $data);
}
