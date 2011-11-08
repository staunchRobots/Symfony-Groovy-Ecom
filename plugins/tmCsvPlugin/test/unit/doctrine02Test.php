<?php
require dirname(__file__) . '/bootstrap.php';

$t = new lime_test(null, new lime_output_color());

$doctrine = Doctrine_Manager::getInstance();


$manager = new sfDatabaseManager($configuration);
$con = $manager->getDatabase('doctrine')->getConnection();

$doctrineConn = Doctrine_Manager::getInstance()->getConnection('doctrine');
$doctrineConn->beginTransaction();

$testFile = '_doctrine02.csv';

$t->comment('Import with header columns set to be case-sensitive (default)');
$csv = new tmDoctrineReader($testFile, array('header'=>true));
$csv->setTable(Doctrine::getTable('Employee'));
$testResults = $csv->parse();
$first = $testResults['valid'][0];
$t->cmp_ok($first['id'], '===',null);
$t->is($first['unit_id'], 17);
$t->is($first['firstname'], null);
$t->is($first['lastname'], 'lastname1');
$t->is($first['age'], 28);
$t->is($first['created_at'], "2009-05-13");
$t->is($first['updated_at'], "2009-05-14 17:11");
$csv->close();

$t->comment('Import with header columns case-insensitive ');
$csv = new tmDoctrineReader($testFile, array('header'=>true,'case_sensitive_columns'=>false));
$csv->setTable(Doctrine::getTable('Employee'));
$testResults = $csv->parse();
$first = $testResults['valid'][0];
$t->is($first['firstname'], 'name1');
