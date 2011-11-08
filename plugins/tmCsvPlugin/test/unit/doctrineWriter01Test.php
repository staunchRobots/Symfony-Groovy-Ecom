<?php
require dirname(__file__) . '/bootstrap.php';

$t = new lime_test(null, new lime_output_color());

$doctrine = Doctrine_Manager::getInstance();


$manager = new sfDatabaseManager($configuration);
$con = $manager->getDatabase('doctrine')->getConnection();

$doctrineConn = Doctrine_Manager::getInstance()->getConnection('doctrine');
$doctrineConn->beginTransaction();

//...
