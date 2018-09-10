<?php

namespace GS\BackOfficeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Classe
 *
 * @ORM\Table(name="classe")
 * @ORM\Entity(repositoryClass="GS\BackOfficeBundle\Repository\ClasseRepository")
 */
class Classe
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
     * @ORM\Column(name="Nom", type="string", length=255)
     */
    private $nom;
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
     * @var int
     *
     * @ORM\Column(name="Niveau", type="integer")
     */
    private $niveau;

    /**
     * @var string
     *
     * @ORM\Column(name="AnneeScolaire", type="string", length=255)
     */
    private $anneeScolaire;

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
     *
     * @ORM\Column(name="planning", type="string", length=255,nullable=true)
     * @Assert\NotBlank(message="Veuillez entrer une version PDF")
     * @Assert\File(mimeTypes={ "application/PDF" })
     */
    private $planning;

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
     * @return Classe
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
     * @return string
     */
    public function getAnneeScolaire()
    {
        return $this->anneeScolaire;
    }

    /**
     * @param string $anneeScolaire
     */
    public function setAnneeScolaire($anneeScolaire)
    {
        $this->anneeScolaire = strtoupper($anneeScolaire);
    }

    /**
     * @return int
     */
    public function getNiveau()
    {
        return $this->niveau;
    }

    /**
     * @param int $niveau
     */
    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;
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
     * @return string
     */
    public function getPlanning()
    {
        return $this->planning;
    }

    /**
     * @param string $planning
     */
    public function setPlanning($planning)
    {
        $this->planning = $planning;
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

