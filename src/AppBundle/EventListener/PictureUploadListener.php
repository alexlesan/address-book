<?php

namespace AppBundle\EventListener;


use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

use AppBundle\Entity\Contact;
use AppBundle\Service\FileUploader;

class PictureUploadListener
{
    private $uploader;

    /**
     * PictureUploadListener constructor.
     *
     * @param \AppBundle\Service\FileUploader $uploader
     */
    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->upload($entity);
    }

    /**
     * @param \Doctrine\ORM\Event\PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->upload($entity);
    }

    /**
     * return path of picture
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     *
     * @return bool
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Contact) {
            return false;
        }

        $sPicName = $entity->getPicture();
        try {
            new File($this->uploader->getUploadDirectory(). '/' . $sPicName);
        } catch (\Exception $exception) {
            $sPicName = 'default.png';
        }

        $entity->setPicture($sPicName);

    }

    /**
     * remove picture when contact is deleted.
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     *
     * @return bool
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Contact) {
            return false;
        }

        try {
            $sPicture = new File($this->uploader->getUploadDirectory(). '/' . $entity->getPicture());
            unlink($sPicture);
        } catch (\Exception $exception) {
            return false;
        }

    }

    /**
     * upload picture to server
     * @param $entity
     *
     * @return bool
     */
    private function upload($entity)
    {
        // do the upload only for Contact entity
        if (!$entity instanceof Contact) {
            return false;
        }

        // get picture
        $file = $entity->getPicture();

        // upload new pictures
        if ($file instanceof UploadedFile) {
            $fileName = $this->uploader->upload($file);
            $entity->setPicture($fileName);
        } elseif ($file instanceof File) {
            $entity->setPicture($file->getFilename());
        }
    }
}