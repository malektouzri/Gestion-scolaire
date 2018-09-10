<?php

namespace GS\BackOfficeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use GS\BackOfficeBundle\Repository\MatiereRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Enseignant
 *
 * @ORM\Table(name="enseignant")
 * @ORM\Entity(repositoryClass="GS\BackOfficeBundle\Repository\EnseignantRepository")
 */
class Enseignant
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
     * @ORM\Column(name="fichier", type="string", length=255,nullable=true)
     * @Assert\NotBlank(message="Veuillez entrer une version PDF")
     * @Assert\File(mimeTypes={ "application/PDF" })
     */
    private $fichier;

    /**
     * @var string
     * @ORM\Column(name="cin", type="string", length=9)
     */
    private $cin;

    /**
     * @var string
     *
     * @ORM\Column(name="Nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="Prenom", type="string", length=255)
     */
    private $prenom;

    /**
     * @var int
     *
     * @ORM\Column(name="Matricule", type="integer", unique=true)
     */
    private $matricule;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateNaissance", type="date")
     */
    private $dateNaissance;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateEmbauche", type="date")
     */
    private $dateEmbauche;

    /**
     * @var string
     *
     * @ORM\Column(name="Specialite", type="string", length=255)
     */
    private $specialite;

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
     * @ORM\Column(name="Email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="Telephone", type="string", length=255)
     */
    private $telephone;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Ecole")
     * @ORM\JoinColumn(name="id_ecole",referencedColumnName="id")
     */
    private $idEcole;

    /**
     * @var string
     * @Assert\NotBlank(message="Veuillez entrer une photo")
     * @Assert\Image()
     * @ORM\Column(name="photo",type="string",length=255,nullable=true)
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
     * @var string
     * @ORM\Column(name="login", type="string", length=255)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="motDePasse", type="string", length=255)
     */
    private $motDePasse;

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
     * @return Enseignant
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
     * @return Enseignant
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
     * Set matricule
     *
     * @param integer $matricule
     *
     * @return Enseignant
     */
    public function setMatricule($matricule)
    {
        $this->matricule = $matricule;

        return $this;
    }

    /**
     * Get matricule
     *
     * @return int
     */
    public function getMatricule()
    {
        return $this->matricule;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     *
     * @return Enseignant
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
     * Set dateEmbauche
     *
     * @param \DateTime $dateEmbauche
     *
     * @return Enseignant
     */
    public function setDateEmbauche($dateEmbauche)
    {
        $this->dateEmbauche = $dateEmbauche;

        return $this;
    }

    /**
     * Get dateEmbauche
     *
     * @return \DateTime
     */
    public function getDateEmbauche()
    {
        return $this->dateEmbauche;
    }

    /**
     * Set specialite
     *
     * @param string $specialite
     *
     * @return Enseignant
     */
    public function setSpecialite($specialite)
    {
        $this->specialite = strtoupper($specialite);

        return $this;
    }

    /**
     * Get specialite
     *
     * @return string
     */
    public function getSpecialite()
    {
        return $this->specialite;
    }

    /**
     * Set sexe
     *
     * @param string $sexe
     *
     * @return Enseignant
     */
    public function setSexe($sexe)
    {
        $this->sexe = strtoupper($sexe);

        return $this;
    }

    /**
     * Get sexe
     *
     * @return string
     */
    public function getSexe()
    {
        return $this->sexe;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Enseignant
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
     * @return Enseignant
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
     * @return Enseignant
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
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param string $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    /**
     * Set motDePasse
     *
     * @param string $motDePasse
     *
     * @return Enseignant
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
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return Enseignant
     */
    public function setLogin($login)
    {
        $this->login = $login;
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
     * @return string
     */
    public function getFichier()
    {
        return $this->fichier;
    }

    /**
     * @param string $fichier
     */
    public function setFichier($fichier)
    {
        $this->fichier = $fichier;
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

