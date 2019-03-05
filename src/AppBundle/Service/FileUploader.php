<?php
namespace AppBundle\Service;


use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{

    /**
     * upload's directory
     */
    private $uploadDirectory;

    /**
     * FileUploader constructor.
     *
     * @param $uploadDirectory
     */
    public function __construct($uploadDirectory)
    {
        $this->uploadDirectory = $uploadDirectory;
    }

    /**
     * does the upload of the pic.
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return bool|string
     *
     */
    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        try {
            $file->move($this->uploadDirectory, $fileName);
        } catch (FileException $exception) {
            return false;
        }
        return $fileName;
    }


    /**
     * return the path to upload directory
     * @return mixed
     */
    public function getUploadDirectory()
    {
        return $this->uploadDirectory;
    }

}