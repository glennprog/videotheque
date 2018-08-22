<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as FosUSER;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Service\GuidGenerator;

/**
 * User
 *
 * @ORM\Table(name="utilisateur")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User extends FosUSER
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct(){
        parent::__construct();
        $this->create_date = new \DateTime("now");
        $guidObj = new GuidGenerator();
        $this->setGuid($guidObj->GUIDv4());
    }

    /**
     * @var guid
     *
     * @ORM\Column(name="guid", type="guid", nullable=false, unique=true)
     */
    private $guid;

    /** @ORM\Column(type="datetime", nullable=false) */
    private $create_date;

    /**
    * @ORM\Column(type="string", nullable=true)
    * @Assert\Length(
    *      min = 3,
    *      max = 50,
    *      minMessage = "Name must be at least {{ limit }} characters long",
    *      maxMessage = "Name cannot be longer than {{ limit }} characters"
    * )
    * @Assert\NotNull()
    */
    protected $lastname;


    /**
    * @ORM\Column(type="string", nullable=true)
    * @Assert\Length(
    *      min = 3,
    *      max = 50,
    *      minMessage = "Name must be at least {{ limit }} characters long",
    *      maxMessage = "Name cannot be longer than {{ limit }} characters"
    * )
    * @Assert\NotNull()
    */
    protected $firstname;

    /**
    * @var Collection
    *
    * @ORM\OneToMany(targetEntity="GM\VideothequeBundle\Entity\Categorie", cascade={"remove"}, mappedBy="owner")
    */
    protected $videotheque_categories;

    /**
    * @var Collection
    *
    * @ORM\OneToMany(targetEntity="GM\VideothequeBundle\Entity\Film", cascade={"remove"}, mappedBy="owner")
    */
    protected $videotheque_films;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set guid
     *
     * @param guid $guid
     *
     * @return Configuration
     */
    public function setGuid($guid)
    {
        $this->guid = $guid;

        return $this;
    }

    /**
     * Get guid
     *
     * @return guid
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get wording
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get wording
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Add photo
     *
     * @param \GM\PhototequeBundle\Entity\Photo $photo
     *
     * @return User
     */
    public function addPhoto(\GM\PhototequeBundle\Entity\Photo $photo)
    {
        $this->photos[] = $photo;

        return $this;
    }

    /**
     * Remove photo
     *
     * @param \GM\PhototequeBundle\Entity\Photo $photo
     */
    public function removePhoto(\GM\PhototequeBundle\Entity\Photo $photo)
    {
        $this->photos->removeElement($photo);
    }

    /**
     * Get photos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhotos()
    {
        return $this->photos;
    }



    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return User
     */
    public function setCreateDate($create_date)
    {
        $this->create_date = $create_date;
        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    public function whoIAm(){
        return "User";
    }

}