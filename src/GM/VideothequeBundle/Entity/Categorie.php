<?php

namespace GM\VideothequeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Service\GuidGenerator;
use AppBundle\Entity\User as Owner;

use JsonSerializable;


/**
 * Categorie
 *
 * @ORM\Table(name="categorie")
 * @ORM\Entity(repositoryClass="GM\VideothequeBundle\Repository\CategorieRepository")
 */
class Categorie implements JsonSerializable
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
     * @var guid
     *
     * @ORM\Column(name="guid", type="guid", nullable=false, unique=true)
     */
    private $guid;

    /** @ORM\Column(type="datetime", nullable=false) */
    private $create_date;

    /** @ORM\Column(type="datetime", nullable=false) */
    private $update_date;

    /**
    * @ORM\Column(type="string", nullable=false, unique=false)
    * @Assert\NotNull(message="Field is mandatory.")
    */
    protected $nom;

    /**
    * @ORM\Column(type="integer", nullable=true)
    */
    protected $nombre_vue;

    /**
    * @var Collection
    *
    * @ORM\OneToMany(targetEntity="GM\VideothequeBundle\Entity\Film", cascade={"remove"}, mappedBy="categorie")
    */
    protected $films;


    /**
    * 
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="videotheque_categories")
    * @ORM\JoinColumn(name="owner_user_id", referencedColumnName="id", nullable=false)
    * @Assert\NotNull()
    */
    protected $owner;

    

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
     * Constructor
     */
    public function __construct(Owner $owner)
    {
        $this->films = new \Doctrine\Common\Collections\ArrayCollection();
        $this->create_date = new \DateTime("now");
        $this->update_date = new \DateTime("now"); 
        $guidObj = new GuidGenerator();
        $this->setGuid($guidObj->GUIDv4());
        $this->setOwner($owner);
    }

    /**
     * Set guid
     *
     * @param guid $guid
     *
     * @return Categorie
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
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return Categorie
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

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

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     *
     * @return Categorie
     */
    public function setUpdateDate($updateDate = null)
    {
        if($updateDate == null){
            $this->update_date = new \DateTime("now");
        }
        else{
            $this->update_date = $updateDate;
        }
        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Categorie
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Add film
     *
     * @param \GM\VideothequeBundle\Entity\Film $film
     *
     * @return Categorie
     */
    public function addFilm(\GM\VideothequeBundle\Entity\Film $film)
    {
        $this->films[] = $film;

        return $this;
    }

    /**
     * Remove film
     *
     * @param \GM\VideothequeBundle\Entity\Film $film
     */
    public function removeFilm(\GM\VideothequeBundle\Entity\Film $film)
    {
        $this->films->removeElement($film);
    }

    /**
     * Set owner
     *
     * @param \AppBundle\Entity\User $owner
     *
     * @return Categorie
     */
    public function setOwner(\AppBundle\Entity\User $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \AppBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Check if the user is the owner
     */
    public function isOwner($id){

        if($this->getOwner()->getId() === $id){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Get films
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFilms()
    {
        return $this->films;
    }

    public function whoIAm(){
        return "Categorie";
    }

    public function __toString() {
        $format = "%s\n";
        return sprintf($format, $this->nom);
    }

    public function jsonSerialize() // For choosing what will be seriali
    {
        return array(
            'id'=> $this->id,
            'guid' => $this->guid,
            'create_date' => $this->create_date,
            'update_date'=> $this->update_date,
            'nom' => $this->nom,
            'films' => $this->films,
            'owner'=> $this->owner,
        );
    }
    /* Be aware of security issue
    function getJsonData(){
        $var = get_object_vars($this);
        foreach ($var as &$value) {
            if (is_object($value) && method_exists($value,'getJsonData')) {
                $value = $value->getJsonData();
            }
        }
        return $var;
    }
    */
}