<?php
use Ozz\Core\Help;
use Ozz\Core\system\SubHelp;

/**
 * Internal var dump of ozz
 * @param $arg content to dumped
 */
function ozz_dump($arg) {
  return Help::dump($arg); 
}

function _dump($arg) {
  return ozz_dump($arg);
}

/**
 * Pretty SQL Dumper
 * @param string SQL string to highlight with colors
 */
function sql_dumper($str) {
  return SubHelp::sqlDumper($str);
}

/**
 * Pretty Dumper for JSON
 * @param string The json string to dump
 * @return html dumped DOM
 */
function json_dump($str, $bg_padd=true) {
  $id = 'jd_'.random_str();
  return SubHelp::jsonDumper($id, $str, $bg_padd);
}