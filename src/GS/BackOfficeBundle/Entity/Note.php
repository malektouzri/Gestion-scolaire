<?php

namespace GS\BackOfficeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Note
 *
 * @ORM\Table(name="note")
 * @ORM\Entity(repositoryClass="GS\BackOfficeBundle\Repository\NoteRepository")
 */
class Note
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;



    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Classe")
     * @ORM\JoinColumn(name="id_classe",referencedColumnName="id")
     */
    private $idClasse;


    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Matiere")
     * @ORM\JoinColumn(name="id_matiere",referencedColumnName="id")
     */
    private $idMat;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Eleve")
     * @ORM\JoinColumn(name="id_eleve",referencedColumnName="id")
     */
    private $idElv;

    /**
     * @var float
     *
     * @ORM\Column(name="note", type="float")
     */
    private $note;

    /**
     * @var string
     *
     * @ORM\Column(name="remarque", type="string", length=255)
     */
    private $remarque;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;


    /**
     * @var string
     *
     * @ORM\Column(name="fichier", type="string", length=255,nullable=true)
     * @Assert\NotBlank(message="Veuillez entrer une version PDF")
     * @Assert\File(mimeTypes={ "application/PDF" })
     */
    private $fichier;


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
     * Set idClasse
     *
     * @param integer $idClasse
     *
     * @return Note
     */
    public function setIdClasse($idClasse)
    {
        $this->idClasse = $idClasse;

        return $this;
    }

    /**
     * Get idClasse
     *
     * @return int
     */
    public function getIdClasse()
    {
        return $this->idClasse;
    }

    /**
     * Set idMat
     *
     * @param integer $idMat
     *
     * @return Note
     */
    public function setIdMat($idMat)
    {
        $this->idMat = $idMat;

        return $this;
    }

    /**
     * Get idMat
     *
     * @return int
     */
    public function getIdMat()
    {
        return $this->idMat;
    }

    /**
     * Set idElv
     *
     * @param integer $idElv
     *
     * @return Note
     */
    public function setIdElv($idElv)
    {
        $this->idElv = $idElv;

        return $this;
    }

    /**
     * Get idElv
     *
     * @return int
     */
    public function getIdElv()
    {
        return $this->idElv;
    }

    /**
     * Set note
     *
     * @param float $note
     *
     * @return Note
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return float
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set remarque
     *
     * @param string $remarque
     *
     * @return Note
     */
    public function setRemarque($remarque)
    {
        $this->remarque = $remarque;

        return $this;
    }

    /**
     * Get remarque
     *
     * @return string
     */
    public function getRemarque()
    {
        return $this->remarque;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }



    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
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




}

