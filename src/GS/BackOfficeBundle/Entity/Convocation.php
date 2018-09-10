<?php

namespace GS\BackOfficeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Convocation
 *
 * @ORM\Table(name="convocation")
 * @ORM\Entity(repositoryClass="GS\BackOfficeBundle\Repository\ConvocationRepository")
 */
class Convocation
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
     * @ORM\ManyToOne(targetEntity="Enseignant")
     * @ORM\JoinColumn(name="id_ens",referencedColumnName="id")
     */
    private $idEns;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Eleve")
     * @ORM\JoinColumn(name="id_eleve",referencedColumnName="id")
     */
    private $idEleve;

    /**
     * @var string
     *
     * @ORM\Column(name="motif", type="string", length=255)
     */
    private $motif;

    /**
     * @var boolean
     *
     * @ORM\Column(name="lu", type="boolean", options={"default":false})
     */
    private $lu;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Ecole")
     * @ORM\JoinColumn(name="id_ecole",referencedColumnName="id")
     */
    private $idEcole;



    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", length=255)
     */
    private $etat;


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
     * Set idEns
     *
     * @param integer $idEns
     *
     * @return Convocation
     */
    public function setIdEns($idEns)
    {
        $this->idEns = $idEns;

        return $this;
    }

    /**
     * Get idEns
     *
     * @return int
     */
    public function getIdEns()
    {
        return $this->idEns;
    }

    /**
     * Set idEleve
     *
     * @param integer $idEleve
     *
     * @return Convocation
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
     * Set motif
     *
     * @param string $motif
     *
     * @return Convocation
     */
    public function setMotif($motif)
    {
        $this->motif = $motif;

        return $this;
    }

    /**
     * Get motif
     *
     * @return string
     */
    public function getMotif()
    {
        return $this->motif;
    }

    /**
     * @return bool
     */
    public function isLu()
    {
        return $this->lu;
    }

    /**
     * @param bool $lu
     */
    public function setLu($lu)
    {
        $this->lu = $lu;
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
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param string $etat
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
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

