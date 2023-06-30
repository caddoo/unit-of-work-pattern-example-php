<?php
declare(strict_types = 1);

final class FileSystem
{
    public function __construct(private string $_basePath)
    {

    }

    public function WriteFile(string $fileName, string $content): void
    {
        echo sprintf("Creating file %s\n", $fileName);
        file_put_contents($this->_createFilePath($fileName), $content);
    }

    public function TryReadFile(string $fileName): ?string
    {
        $content = file_get_contents($this->_createFilePath($fileName));

        if ($content === false) {
            return null;
        }

        return $content;
    }

    public function Delete(string $fileName): void
    {
        if ($this->Exists($fileName) === false) {
            throw new Exception("File doesn't exist so can't delete");
        }

        echo sprintf("Deleting file %s\n", $fileName);
        unlink($this->_createFilePath($fileName));
    }

    public function Exists(string $fileName): bool
    {
        return file_exists($this->_createFilePath($fileName));
    }

    private function _createFilePath(string $fileName): string
    {
        return $this->_basePath . "/" . $fileName;
    }
}