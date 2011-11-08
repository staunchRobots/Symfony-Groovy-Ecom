<?php
require dirname(__file__) . '/bootstrap.php';

$t = new lime_test(null, new lime_output_color());

$doctrine = Doctrine_Manager::getInstance();


$manager = new sfDatabaseManager($configuration);
$con = $manager->getDatabase('doctrine')->getConnection();

$doctrineConn = Doctrine_Manager::getInstance()->getConnection('doctrine');
$doctrineConn->beginTransaction();

$testFile = '_doctrine01.csv';

//$e = new Employee();

$csv = new tmDoctrineReader($testFile, array('header'=>true));
$csv->setTable(Doctrine::getTable('Employee'));
$testResults = $csv->testParse();
$t->ok((array_search("fakestuff",$csv->getRemoved()) !== false), '"fakestuff" should be among the removed fields');
$t->ok((array_search("id",$csv->getRemoved()) !== false));
$t->cmp_ok($testResults[2]['valid'], '===',true);
$t->cmp_ok($testResults[3]['valid'], '===',false);
$csv->close();


$csv = new tmDoctrineReader($testFile, array('header'=>true));
$csv->setTable(Doctrine::getTable('Employee'));
$testResults = $csv->parse();
$first = $testResults['valid'][0];
$t->cmp_ok($first['id'], '===',null);
$t->is($first['unit_id'], 17);
$t->is($first['firstname'], 'name1');
$t->is($first['lastname'], 'lastname1');
$t->is($first['age'], 28);
$t->is($first['created_at'], "2009-05-13");
$t->is($first['updated_at'], "2009-05-14 17:11");
$csv->close();

$t->comment('Test using a record as a template');
$csv = new tmDoctrineReader($testFile, array('header'=>true));
$eTmpl = new Employee();
$eTmpl['firstname'] = 'Zabuch';
$csv->setTemplate($eTmpl);
$parsed = $csv->parse();
$third = $parsed['valid'][1];
$t->is($third['firstname'], 'Zabuch');
$t->is($third['lastname'], 'lastname4');
$csv->close();

