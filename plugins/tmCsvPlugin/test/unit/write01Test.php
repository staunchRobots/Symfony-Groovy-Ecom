<?php

require_once 'bootstrap.php';

$tmp = '/tmp/a.csv';
$t = new lime_test(null, new lime_output_color());

$writer = new tmCsvWriter();
$writer->setHeader(array('one','two','three'));
$writer->add(array('one'=>111));
$writer->add(array('two'=>222));
$writer->add(array('three'=>333));
$writer->add(array('two'=>'2', 'one'=>'1', 'three'=>'3'));
$writer->save($tmp);
$writer->close();
$out = file_get_contents($tmp);
$expected = 'one,two,three
111,,
,222,
,,333
1,2,3
';
$t->is($out, $expected);
