<?php

namespace GS\BackOfficeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Absence
 *
 * @ORM\Table(name="absence")
 * @ORM\Entity(repositoryClass="GS\BackOfficeBundle\Repository\AbsenceRepository")
 */
class Absence
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
     * @ORM\ManyToOne(targetEntity="Eleve")
     * @ORM\JoinColumn(name="id_eleve",referencedColumnName="id")
     */
    private $idEleve;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Classe")
     * @ORM\JoinColumn(name="id_classe",referencedColumnName="id")
     */
    private $classe;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Matiere")
     * @ORM\JoinColumn(name="id_matiere",referencedColumnName="id")
     */
    private $idMat;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure", type="time")
     */
    private $heure;

    /**
     * @var string
     *
     * @ORM\Column(name="Remarque", type="string", length=255)
     */
    private $remarque;


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
     * Set idEleve
     *
     * @param integer $idEleve
     *
     * @return Absence
     */
    public function setIdEleve($idEleve)
    {
        $this->idEleve = $idEleve;

        return $this;
    }

    /**
     * Get idEleve
     *
     * @return int
     */
    public function getIdEleve()
    {
        return $this->idEleve;
    }

    /**
     * Set idMat
     *
     * @param integer $idMat
     *
     * @return Absence
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Absence
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
     * Set heure
     *
     * @param \DateTime $heure
     *
     * @return Absence
     */
    public function setHeure($heure)
    {
        $this->heure = $heure;

        return $this;
    }

    /**
     * Get heure
     *
     * @return \DateTime
     */
    public function getHeure()
    {
        return $this->heure;
    }

    /**
     * Set remarque
     *
     * @param string $remarque
     *
     * @return Absence
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
     * @return int
     */
    public function getClasse()
    {
        return $this->classe;
    }

    /**
     * @param int $classe
     */
    public function setClasse($classe)
    {
        $this->classe = $classe;
    }


}

