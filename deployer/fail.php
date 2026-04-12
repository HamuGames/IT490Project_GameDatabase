<?php

$deploy = parse_ini_file(__DIR__ . '/deployer.ini', true)['database'];
$db = new mysqli($deploy['host'], $deploy['user'], $deploy['pass'], $deploy['name']);

$result = $db->query("SELECT version, status FROM packages ORDER BY dateDeployed DESC LIMIT 1");

$row = $result->fetch_assoc();
$version = $row['version'];
$status = $row['status'];
$db->query("UPDATE packages SET status = 'FAIL' WHERE version = '$version'");


if ($status = "FAIL") 
{
die("FATALITY: latest version $version has already been marked as FAILED! run rollback (qa,dev or prod) to rollback to the latest GOOD version\n");
} else {
echo ("Version $version has been marked as FAILED! run rollback (qa,dev or prod) to rollback to the latest GOOD version\n");
}
$db->close();
?>
