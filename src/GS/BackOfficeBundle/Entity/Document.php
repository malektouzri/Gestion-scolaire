<?php

namespace GS\BackOfficeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Document
 *
 * @ORM\Table(name="document")
 * @ORM\Entity(repositoryClass="GS\BackOfficeBundle\Repository\DocumentRepository")
 */
class Document
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
     * @var int
     * @ORM\ManyToOne(targetEntity="Matiere")
     */
    private $idMat;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Classe")
     */
    private $idClasse;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Enseignant")
     */
    private $idEns;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="fichier", type="string", length=255)
     * @Assert\NotBlank(message="Veuillez entrer une version PDF")
     * @Assert\File(mimeTypes={ "application/PDF" })
     */
    private $fichier;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;


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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getIdEns()
    {
        return $this->idEns;
    }

    /**
     * @param int $idEns
     */
    public function setIdEns($idEns)
    {
        $this->idEns = $idEns;
    }

    /**
     * @return int
     */
    public function getIdClasse()
    {
        return $this->idClasse;
    }

    /**
     * @param int $idClasse
     */
    public function setIdClasse($idClasse)
    {
        $this->idClasse = $idClasse;
    }




    /**
     * Set type
     *
     * @param string $type
     *
     * @return Document
     */
    public function setType($type)
    {
        $this->type = strtoupper($type);

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set fichier
     *
     * @param string $fichier
     *
     * @return Document
     */
    public function setFichier($fichier)
    {
        $this->fichier = $fichier;

        return $this;
    }

    /**
     * Get fichier
     *
     * @return string
     */
    public function getFichier()
    {
        return $this->fichier;
    }

    /**
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }



    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Document
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return integer
     */
    public function getIdMat()
    {
        return $this->idMat;
    }

    /**
     * @param integer $idMat
     */
    public function setIdMat($idMat)
    {
        $this->idMat = $idMat;
    }


}

