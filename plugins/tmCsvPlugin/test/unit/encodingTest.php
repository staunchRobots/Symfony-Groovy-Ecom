<?php

require dirname(__file__) . '/bootstrap.php';

$testFile = '_encoding_iso-8859-2.csv';

$t = new lime_test(null, new lime_output_color());

$t->comment('Testing encoding');

if(setlocale(LC_ALL, 'pl_PL.ISO-8859-2') === false) {
  $t->fail("Can't set the locale needed for this test");
}

$csv = new tmCsvReader($testFile, array('header'=>false, 'from'=>'iso-8859-2'));
$arr = $csv->toArray();
$csv->close();
$strUtf8 =  "\x67\xc5\xbc\x65\x67\xc5\xbc\xc3\xb3\xc5\x82\x6b\x61";
$t->is($strUtf8, $arr[0][0]);
$strUtf8="\xc4\x99\xc3\xb3\xc5\x82\xc5\x84\xc5\xbc\xc5\xba\xc4\x87\xc4\x85\xc5\x9b";
$t->is($strUtf8, $arr[0][1]);

if(setlocale(LC_ALL, 'en_US.UTF-8') === false) {
  $t->fail("Can't set the locale needed for this test");
}

$testFile = '_encoding_utf-8.csv';
$csv = new tmCsvReader($testFile, array('header'=>false, 'from'=>'utf-8', 'to'=>'iso-8859-2'));
$arr = $csv->toArray();
//var_dump($arr[0]);
$csv->close();
$strIso =  "\x67\xbf\x65\x67\xbf\xf3\xb3\x6b\x61";
$t->is($strIso, $arr[0][0]);
$strIso = "\xea\xf3\xb3\xf1\xbf\xbc\xe6\xb1\xb6";
//var_dump($strUtf8);
$t->is($strIso, $arr[0][1]);
