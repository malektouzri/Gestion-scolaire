<?php

namespace GS\BackOfficeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EnsMatClasse
 *
 * @ORM\Table(name="ens_mat_classe")
 * @ORM\Entity(repositoryClass="GS\BackOfficeBundle\Repository\EnsMatClasseRepository")
 */
class EnsMatClasse
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
     *
     * @ORM\ManyToOne(targetEntity="Enseignant", inversedBy="ensMatClasse")
     * @ORM\JoinColumn(name="ens_id", referencedColumnName="id")
     */
    private $ensId;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Matiere", inversedBy="ensMatClasse")
     * @ORM\JoinColumn(name="mat_id", referencedColumnName="id")
     */
    private $matId;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Classe", inversedBy="ensMatClasse")
     * @ORM\JoinColumn(name="cl_id", referencedColumnName="id")
     */
    private $classeId;


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
     * Set ensId
     *
     * @param integer $ensId
     *
     * @return EnsMatClasse
     */
    public function setEnsId($ensId)
    {
        $this->ensId = $ensId;

        return $this;
    }

    /**
     * Get ensId
     *
     * @return int
     */
    public function getEnsId()
    {
        return $this->ensId;
    }

    /**
     * Set matId
     *
     * @param integer $matId
     *
     * @return EnsMatClasse
     */
    public function setMatId($matId)
    {
        $this->matId = $matId;

        return $this;
    }

    /**
     * Get matId
     *
     * @return int
     */
    public function getMatId()
    {
        return $this->matId;
    }

    /**
     * Set classeId
     *
     * @param integer $classeId
     *
     * @return EnsMatClasse
     */
    public function setClasseId($classeId)
    {
        $this->classeId = $classeId;

        return $this;
    }

    /**
     * Get classeId
     *
     * @return int
     */
    public function getClasseId()
    {
        return $this->classeId;
    }
}

