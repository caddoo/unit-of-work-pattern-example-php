<?php
declare(strict_types = 1);

final class FileSystemUnitOfWork {

    private array $_fileContentBuffer = [];
    private array $_deleteBuffer = [];

    public function __construct(private FileSystem $_fileSystem)
    {
    }

    public function Add(string $fileName, string $content): void
    {
        if ($this->_fileSystem->Exists($fileName)) {
            throw new Exception("File exists already");
        }

        $this->_fileContentBuffer[$fileName] = $content;
    }

    public function Delete(string $fileName): void
    {
        if (array_key_exists($fileName, $this->_fileContentBuffer)) {
            unset($this->_fileContentBuffer[$fileName]);
            return;
        }

        $fileContent = $this->_fileSystem->TryReadFile($fileName);

        if ($fileContent === null) {
            throw new Exception("File doesn't exist or not readable");
        }

        $this->_deleteBuffer[$fileName] = $fileContent;
    }

    public function Execute(): void
    {
        try {
            foreach ($this->_fileContentBuffer as $fileName => $fileContent) {
                $this->_fileSystem->WriteFile($fileName, $fileContent);
            }

            foreach (array_keys($this->_deleteBuffer) as $fileName) {
                $this->_fileSystem->Delete($fileName);
            }

        } catch (Exception $e) {
            $this->_rollback();
            throw $e;
        }

        $this->_fileContentBuffer = [];
        $this->_deleteBuffer = [];
    }

    private function _rollback(): void
    {
        echo "Performing rollback\n";
        foreach (array_keys($this->_fileContentBuffer) as $fileName) {
            $this->_fileSystem->Delete($fileName);
        }

        foreach ($this->_deleteBuffer as $fileName => $fileContent) {
            if ($this->_fileSystem->Exists($fileName) === false) {
                $this->_fileSystem->WriteFile($fileName, $fileContent);
            }
        }
    }
}