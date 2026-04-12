<?php

$projectFolder = "/home/fabdul25/git/IT490_Project";
$versionFolder = "/home/fabdul25/deployment/versions";

$deploy = parse_ini_file(__DIR__ . '/deployer.ini', true)['database'];
$db = new mysqli($deploy['host'], $deploy['user'], $deploy['pass'], $deploy['name']);

$result = $db->query("SELECT version FROM packages ORDER BY dateDeployed DESC LIMIT 1");
$version = "v1.0.0";

if ($row = $result->fetch_assoc()) {
	$num = explode('.', ltrim($row['version'], 'v'));
	$num[2]++;
	$version = "v" . implode('.', $num);
}
$fileName = "$versionFolder/$version.tar.gz";
echo "New version $version in process...";
exec("cd $projectFolder && git checkout main && git pull origin main");
exec("tar -czf $fileName -C $projectFolder .");
exec("cd $projectFolder && git checkout Deployment");

$db->query("INSERT INTO packages (version, path, status, environment) VALUES ('$version', '$fileName', 'pending', 'na')");

echo "New Version $version has been created in $versionFolder\n";


?>
