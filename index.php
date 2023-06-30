<?php
declare(strict_types = 1);

require_once('FileSystem.php');
require_once('FileSystemUnitOfWork.php');

$filesDirectory = dirname(__FILE__)  . "/Files";

$fileSystem = new FileSystem($filesDirectory);

// Successfully create 3 files
echo "- Creating the first three files\n";
$fileSystemUnitOfWork = new FileSystemUnitOfWork($fileSystem);
$fileSystemUnitOfWork->Add('file1', 'content');
$fileSystemUnitOfWork->Add('file2', 'content');
$fileSystemUnitOfWork->Add('file3', 'content');
$fileSystemUnitOfWork->Execute();

// Fail on deleting the last file then rollback
echo "- Creating two files and attempting to delete three files\n";
$fileSystemUnitOfWork->Add('file4', 'content');
$fileSystemUnitOfWork->Add('file5', 'content');
$fileSystemUnitOfWork->Delete('file1');
$fileSystemUnitOfWork->Delete('file2');
$fileSystemUnitOfWork->Delete('file3');

// Now lets simulate a failure that could happen (file doesnt exist for file3).
unlink($filesDirectory . "/file3");

try
{
    $fileSystemUnitOfWork->Execute();
}
catch (Exception $e)
{
    echo sprintf("Execution failed because %s\n", $e->getMessage());
}