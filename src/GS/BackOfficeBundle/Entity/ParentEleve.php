<?php

namespace GS\BackOfficeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Parent
 *
 * @ORM\Table(name="parent")
 * @ORM\Entity(repositoryClass="GS\BackOfficeBundle\Repository\ParentEleveRepository")
 */
class ParentEleve
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
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     * @ORM\Column(name="cin", type="string", length=9)
     */
    private $cin;

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
     * @ORM\Column(name="Adresse", type="string", length=255)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=255)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=255)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="Sexe", type="string", length=255)
     */
    private $sexe;

    /**
     * @var string
     *
     * @ORM\Column(name="motDePasse", type="string", length=255)
     */
    private $motDePasse;

    /**
     * @var string
     *@Assert\NotBlank(message="Veuillez entrer une photo")
     * @Assert\Image()
     * @ORM\Column(name="photo", type="string", length=255,nullable=true)
     */
    private $photo;


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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCin()
    {
        return $this->cin;
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
     * @param string $cin
     */
    public function setCin($cin)
    {
        $this->cin = $cin;
    }



    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return ParentEleve
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
     * Set prenom
     *
     * @param string $prenom
     *
     * @return ParentEleve
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
     * @return ParentEleve
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
     * @return ParentEleve
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
     * Set email
     *
     * @param string $email
     *
     * @return ParentEleve
     */
    public function setEmail($email)
    {
        $this->email = strtoupper($email);

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return ParentEleve
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set login
     *
     * @param string $login
     *
     * @return ParentEleve
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set motDePasse
     *
     * @param string $motDePasse
     *
     * @return ParentEleve
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
     * @return ParentEleve
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

