<?php

if (!is_file($_SERVER['DOCUMENT_ROOT'] . $_SERVER['SCRIPT_NAME'])) {
    $_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT'] . '/index.php';
    require $_SERVER['SCRIPT_FILENAME'];
    exit;
}