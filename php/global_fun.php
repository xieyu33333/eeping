<?php
function magjcid($suffix = 6) {
  $index = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $base  = strlen($index);
  $out = '';
  $timestamp = time() - mktime(0, 0, 0, 12, 1, 2011);
  for ($t = floor(log($timestamp, $base)); $t >= 0; $t--) {
    $p = pow($base, $t);
    $a = floor($timestamp / $p) % $base;
    $out .= substr($index, $a, 1);
    $timestamp  = $timestamp - ($a * $p);
  }
  for ($i = 0; $i < $suffix; $i++) {
    $out .= substr($index, mt_rand(0, $base -1), 1);
  }
  return $out;
}
function n2s($numId) {
  $index = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $base  = strlen($index);
  $out = '';
  for ($t = floor(log($numId, $base)); $t >= 0; $t--) {
    $p = pow($base, $t);
    $a = floor($numId / $p) % $base;
    $out .= substr($index, $a, 1);
    $numId  = $numId - ($a * $p);
  }
  return $out;
}
function s2n($strId) {
  $index = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $base = strlen($strId);
  $out = 0;
  for ($i = 0; $i < $base; $i++) {
    $a = substr($strId, $i, 1);
    $p = strpos($index, $a);
    $out += $p * pow(62, $base - $i - 1);
  }
  return $out;
}
function outputDbError($result) {
  if (!$result) exit(json_encode(array(0, mysql_error())));
  // $r[0] = 1;
  return $result;
}
?>