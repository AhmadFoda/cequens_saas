<?php

namespace CequensBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AudioFile
 *
 * @ORM\Table(name="audio_file")
 * @ORM\Entity(repositoryClass="CequensBundle\Repository\AudioFileRepository")
 */
class AudioFile
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
     * @ORM\Column(name="audio_file_name", type="string", length=255)
     */
    private $audioFileName;

    /**
     * @var int
     *
     * @ORM\Column(name="language_id", type="integer")
     */
    private $languageId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var float
     *
     * @ORM\Column(name="audio_file_duration", type="float")
     */
    private $audioFileDuration;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="audio_file_size", type="float")
	 */
	private $audioFileSize;

    /**
     * @var string
     *
     * @ORM\Column(name="file_type", type="string", length=255)
     */
    private $fileType;

    /**
     * @var int
     *
     * @ORM\Column(name="file_id", type="integer")
     */
    private $fileId;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="file_is_package", type="integer")
	 */
	private $fileIsPackage;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="file_package", type="integer", nullable=true)
	 */
	private $filePackage;


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
     * Set audioFileName.
     *
     * @param string $audioFileName
     *
     * @return AudioFile
     */
    public function setAudioFileName($audioFileName)
    {
        $this->audioFileName = $audioFileName;

        return $this;
    }

    /**
     * Get audioFileName.
     *
     * @return string
     */
    public function getAudioFileName()
    {
        return $this->audioFileName;
    }

    /**
     * Set languageId.
     *
     * @param int $languageId
     *
     * @return AudioFile
     */
    public function setLanguageId($languageId)
    {
        $this->languageId = $languageId;

        return $this;
    }

    /**
     * Get languageId.
     *
     * @return int
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }

    /**
     * Set userId.
     *
     * @param int $userId
     *
     * @return AudioFile
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set audioFileDuration.
     *
     * @param float $audioFileDuration
     *
     * @return AudioFile
     */
    public function setAudioFileDuration($audioFileDuration)
    {
        $this->audioFileDuration = $audioFileDuration;

        return $this;
    }

    /**
     * Get audioFileDuration.
     *
     * @return float
     */
    public function getAudioFileDuration()
    {
        return $this->audioFileDuration;
    }

    /**
     * Set fileType.
     *
     * @param string $fileType
     *
     * @return AudioFile
     */
    public function setFileType($fileType)
    {
        $this->fileType = $fileType;

        return $this;
    }

    /**
     * Get fileType.
     *
     * @return string
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * Set fileId.
     *
     * @param int $fileId
     *
     * @return AudioFile
     */
    public function setFileId($fileId)
    {
        $this->fileId = $fileId;

        return $this;
    }

    /**
     * Get fileId.
     *
     * @return int
     */
    public function getFileId()
    {
        return $this->fileId;
    }

	/**
	 * @return float
	 */
	public function getAudioFileSize()
	{
		return $this->audioFileSize;
	}

	/**
	 * @param float $audioFileSize
	 */
	public function setAudioFileSize($audioFileSize)
	{
		$this->audioFileSize = $audioFileSize;
	}

	/**
	 * @return int
	 */
	public function getFileIsPackage()
	{
		return $this->fileIsPackage;
	}

	/**
	 * @param int $fileIsPackage
	 */
	public function setFileIsPackage($fileIsPackage)
	{
		$this->fileIsPackage = $fileIsPackage;
	}


}
