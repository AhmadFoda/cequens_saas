<?php

namespace CequensBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * File
 *
 * @ORM\Table(name="file")
 * @ORM\Entity(repositoryClass="CequensBundle\Repository\FileRepository")
 */
class File
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", length=255)
     */
    private $fileName;

    /**
     * @var string
     *
     * @ORM\Column(name="file_safe_name", type="string", length=255)
     */
    private $fileSafeName;

    /**
     * @var string
     *
     * @ORM\Column(name="file_path", type="string", length=255)
     */
    private $filePath;

    /**
     * @var string
     *
     * @ORM\Column(name="file_size", type="string", length=255)
     */
    private $fileSize;

    /**
     * @var int
     *
     * @ORM\Column(name="file_type", type="integer")
     */
    private $fileType;

    /**
     * @var int
     *
     * @ORM\Column(name="file_version", type="integer")
     */
    private $fileVersion;


    /**
     * @var int
     *
     * @ORM\Column(name="file_status", type="integer")
     */
    private $fileStatus;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fileName.
     *
     * @param string $fileName
     *
     * @return File
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set fileSafeName.
     *
     * @param string $fileSafeName
     *
     * @return File
     */
    public function setFileSafeName($fileSafeName)
    {
        $this->fileSafeName = $fileSafeName;

        return $this;
    }

    /**
     * Get fileSafeName.
     *
     * @return string
     */
    public function getFileSafeName()
    {
        return $this->fileSafeName;
    }

    /**
     * Set filePath.
     *
     * @param string $filePath
     *
     * @return File
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Get filePath.
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Set fileSize.
     *
     * @param string $fileSize
     *
     * @return File
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * Get fileSize.
     *
     * @return string
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * Set fileType.
     *
     * @param int $fileType
     *
     * @return File
     */
    public function setFileType($fileType)
    {
        $this->fileType = $fileType;

        return $this;
    }

    /**
     * Get fileType.
     *
     * @return int
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * Set fileVersion.
     *
     * @param int $fileVersion
     *
     * @return File
     */
    public function setFileVersion($fileVersion)
    {
        $this->fileVersion = $fileVersion;

        return $this;
    }

    /**
     * Get fileVersion.
     *
     * @return int
     */
    public function getFileVersion()
    {
        return $this->fileVersion;
    }

    /**
     * Set fileStatus.
     *
     * @param int $fileStatus
     *
     * @return File
     */
    public function setFileStatus($fileStatus)
    {
        $this->fileStatus = $fileStatus;

        return $this;
    }

    /**
     * Get fileStatus.
     *
     * @return int
     */
    public function getFileStatus()
    {
        return $this->fileStatus;
    }
}
