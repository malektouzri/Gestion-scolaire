<?php

namespace GS\BackOfficeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Eleve
 *
 * @ORM\Table(name="eleve")
 * @ORM\Entity(repositoryClass="GS\BackOfficeBundle\Repository\EleveRepository")
 */
class Eleve
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
     * @var integer
     * @ORM\ManyToOne(targetEntity="Ecole")
     * @ORM\JoinColumn(name="id_ecole",referencedColumnName="id")
     */
    private $idEcole;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Classe")
     * @ORM\JoinColumn(name="id_classe",referencedColumnName="id")
     */
    private $classe;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="ParentEleve")
     * @ORM\JoinColumn(name="id_parent",referencedColumnName="id")
     */
    private $parent;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    private $prenom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateNaissance", type="date")
     */
    private $dateNaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="Sexe", type="string", length=255)
     */
    private $sexe;

    /**
     * @var string
     *
     * @ORM\Column(name="Adresse", type="string", length=255)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="numInscription", type="string", length=255)
     */
    private $numInscription;

    /**
     * @var boolean
     * @ORM\Column(name="Annuler", type="boolean", options={"default":false})
     */
    private $annuler;

    /**
     * @var \DateTime
     * @ORM\Column(name="dateAnnuler", type="date", nullable=true)
     */
    private $dateAnnuler;

    /**
     * @var string
     *
     * @ORM\Column(name="motDePasse", type="string", length=255)
     */
    private $motDePasse;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=255)
     */
    private $login;

    /**
     * @var string
     *@Assert\NotBlank(message="Veuillez ajouter une photo")
     * @Assert\Image()
     * @ORM\Column(name="Photo", type="string", length=255,nullable=true)
     */
    private $photo;


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
     * Set nom
     *
     * @param string $nom
     *
     * @return Eleve
     */
    public function setNom($nom)
    {
        $this->nom = strtoupper($nom);

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
     * @return \DateTime
     */
    public function getDateAnnuler()
    {
        return $this->dateAnnuler;
    }

    /**
     * @param \DateTime $dateAnnuler
     */
    public function setDateAnnuler($dateAnnuler)
    {
        $this->dateAnnuler = $dateAnnuler;
    }



    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Eleve
     */
    public function setPrenom($prenom)
    {
        $this->prenom = strtoupper($prenom);

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     *
     * @return Eleve
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Eleve
     */
    public function setAdresse($adresse)
    {
        $this->adresse = strtoupper($adresse);

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set numInscription
     *
     * @param string $numInscription
     *
     * @return Eleve
     */
    public function setNumInscription($numInscription)
    {
        $this->numInscription = $numInscription;

        return $this;
    }

    /**
     * Get numInscription
     *
     * @return string
     */
    public function getNumInscription()
    {
        return $this->numInscription;
    }

    /**
     * @return string
     */
    public function getSexe()
    {
        return $this->sexe;
    }

    /**
     * @param string $sexe
     */
    public function setSexe($sexe)
    {
        $this->sexe = strtoupper($sexe);
    }



    /**
     * Set motDePasse
     *
     * @param string $motDePasse
     *
     * @return Eleve
     */
    public function setMotDePasse($motDePasse)
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }

    /**
     * Get motDePasse
     *
     * @return string
     */
    public function getMotDePasse()
    {
        return $this->motDePasse;
    }

    /**
     * Set photo
     *
     * @param string $photo
     *
     * @return Eleve
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @return integer
     */
    public function getClasse()
    {
        return $this->classe;
    }

    /**
     * @param integer $classe
     */
    public function setClasse($classe)
    {
        $this->classe = $classe;
    }

    /**
     * @return int
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param int $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return bool
     */
    public function isAnnuler()
    {
        return $this->annuler;
    }

    /**
     * @param bool $annuler
     */
    public function setAnnuler($annuler)
    {
        $this->annuler = $annuler;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return int
     */
    public function getIdEcole()
    {
        return $this->idEcole;
    }

    /**
     * @param int $idEcole
     */
    public function setIdEcole($idEcole)
    {
        $this->idEcole = $idEcole;
    }




}

