<?php

if ($argc <2) {
        die("FATALITY! You must provide which environment to rollback to. qa, dev, or prod\n");
}
$env = strtolower($argv[1]);

$deploying = parse_ini_file(__DIR__ . '/deployer.ini', true);
$deploy = $deploying['database'];
$db = new mysqli($deploy['host'], $deploy['user'], $deploy['pass'], $deploy['name']);

$result = $db->query("SELECT version, environment FROM packages WHERE status = 'FAIL' AND environment = '$env' ORDER BY dateDeployed DESC LIMIT 1");

if ($result->num_rows === 0) {
        die("FATALITY! There are no failed versions on the $env environment to rollback.\n");
}

$row = $result->fetch_assoc();
$version = $row['version'];
$environment = $row['environment'];

$result2 = $db->query("SELECT version, path FROM packages WHERE status = 'deployed' AND environment = '$env' ORDER BY dateDeployed DESC LIMIT 1");
$row = $result2->fetch_assoc();
$oldVersion = $row['version'];
$path = $row['path'];
$fileName = basename($path);

echo "Rolling back version $version to $oldVersion in $env environment....\n";

foreach ($deploying as $machine => $vm) {

        if (strpos($machine, $env . '_') === 0) {

		$ip = $vm['ip'];
		$user = $vm['user'];
                $destination = $vm['path'];

                echo " Sending package to $machine at $ip....\n";

                exec("scp $path $user@$ip:/tmp/$fileName");

                exec("ssh $user@$ip 'tar -xzf /tmp/$fileName -C $destination'");
        }
}

$db->query("UPDATE packages SET status = 'ROLLED BACK' WHERE version = '$version'");

echo "Well Done, Version $version has been rolled back to version $oldVersion\n";


$db->close();
?>

