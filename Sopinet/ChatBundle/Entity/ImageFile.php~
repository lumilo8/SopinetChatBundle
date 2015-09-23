<?php
namespace Sopinet\ChatBundle\Entity;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Exclude;

/**
 * @ORM\Entity
 * @ORM\Table(name="sopinet_chatbundle_imagefile")
 * @DoctrineAssert\UniqueEntity("id")
 * @ORM\HasLifecycleCallbacks
 */
class ImageFile
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"public"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @ORM\OneToOne(targetEntity="MessageImage", mappedBy="file", cascade={"persist"})
     */
    protected $messageImage;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }


    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    public function getFrontPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../web'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return '';
    }

    public function getName()
    {
        return explode('/', $this->getPath())[2];
    }

    public function getHttpWebPath(Request $request) {
        $httpWeb = $request->getUriForPath($this->getWebPath());
        $httpWeb = str_replace("app_dev.php/", "", $httpWeb);
        return $httpWeb;
    }

    /**
     * @ORM\PostRemove
     */
    public function deleteFile(LifecycleEventArgs $event)
    {
        $fs = new Filesystem();
        //$fs->remove($this->getAbsolutePath());
    }

    public function setMessageImage(MessageImage $messageImage = null)
    {
        $this->messageimage = $messageImage;

        return $this;
    }

    public function getMessageImage()
    {
        return $this->messageImage;
    }
}
