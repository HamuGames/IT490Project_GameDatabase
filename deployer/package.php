<?php

if ($argc < 2) {
	die("You must provide a version number!\n");
}

$version = $argv[1];
$projectFolder = "/home/fabdul25/git/IT490_Project";
$versionFolder = "/home/fabdul25/deployment/versions";
$fileName = "$versionFolder/v$version.tar.gz";

echo "New version v$version in process...";
exec("cd $projectFolder && git checkout main && git pull origin main");
exec("tar -czf $fileName -C $projectFolder .");
exec("cd $projectFolder && git checkout Deployment");

echo "New Version v$version has been created in $versionFolder\n";

?>
