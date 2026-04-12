<?php

if ($argc <2) {
	die("FATALITY! You must provide which environment to send latest version to. qa, dev, or prod\n");
}
$env = strtolower($argv[1]);

$deploying = parse_ini_file(__DIR__ . '/deployer.ini', true);
$deploy = $deploying['database'];
$db = new mysqli($deploy['host'], $deploy['user'], $deploy['pass'], $deploy['name']);

$result = $db->query("SELECT version, path FROM packages WHERE status = 'pending' ORDER BY dateDeployed DESC LIMIT 1");

if ($result->num_rows === 0) {
	die("FATALITY! There are no versions pending to be deployed. create a new version first!\n");
}

$row = $result->fetch_assoc();
$version = $row['version'];
$path = $row['path'];
$fileName = basename($path);

echo "Deploying version $version to ALL $env machines....\n";

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

$db->query("UPDATE packages SET status = 'deployed', environment = '$env' WHERE version = '$version'");

echo "Well Done, Version $version has been deployed to $env successfully!\n";

?>
