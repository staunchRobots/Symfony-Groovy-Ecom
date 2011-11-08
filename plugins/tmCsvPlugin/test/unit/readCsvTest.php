<?php

require dirname(__file__) . '/bootstrap.php';

$expectedHeader = array("id1"=>"id1","id2"=>"id2","foreignKey"=>"foreignKey","name"=>"name","lastname"=>"lastname","age"=>"age","date"=>"date","bonus"=>"bonus");
$testFile = '_employees01.csv';

$t = new lime_test(null, new lime_output_color());

$csv = new tmCsvReader($testFile, array('header'=>true));
$arr = $csv->toArray();
$csv->close();
$t->is(count($arr), 3);

$csv = new tmCsvReader($testFile, array('header'=>false));
$arr = $csv->toArray();
$csv->close();
$t->is(count($arr), 4);


$csv = new tmCsvReader($testFile, array('header'=>true));
$count = 0;
while($csv->next()) {
	$count++;
}
$csv->close();
$t->is($count, 3);


$t->comment("Header-related checks");

//Try to remove non-existing column from a header
$csv = new tmCsvReader($testFile, array('header'=>true));
//$csv->removeHeader('blah');
//$t->is($csv->getHeader(), $expectedHeader);
$csv->close();

//Remove an existing column from a header
$csv = new tmCsvReader($testFile, array('header'=>true));

$csv->removeHeader('id1');
$t->is($csv->getHeader(), array_diff_key($expectedHeader, array('id1'=>'id1')));
$t->is($csv->getRemoved(), array('id1'=>'id1'));

$csv->removeHeader(array('id2'));
$t->is($csv->getHeader(), array_diff_key($expectedHeader, array('id1'=>'id1', 'id2'=>'id2')));
$t->is($csv->getRemoved(), array('id1'=>'id1','id2'=>'id2' ));

$csv->removeHeader(array('name'=>'name', 'lastname'=>'lastname'));
$t->is($csv->getHeader(), array_diff_key($expectedHeader, array('id1'=>'id1', 'id2'=>'id2','name'=>'name', 'lastname'=>'lastname')));
$t->is($csv->getRemoved(), array('id1'=>'id1','id2'=>'id2','name'=>'name', 'lastname'=>'lastname' ));

$csv->close();


$testFile = '_employees02.csv';
$csv = new tmCsvReader($testFile, array (
  'header' => true ));

$expectedValues = array (
  0 => array (
  'id1' => '17', 'id2' => '12', 'foreignKey' => '34', 'name' => 'Nam\'\'e1', 'lastname' => 'very long last
name with
few
newlines embedded', 'age' => '17', 'date' => '2009-12-01', 'bonus' => '17' ) );
$t->is_deeply($expectedValues,$csv->toArray());
