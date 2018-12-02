<?php

namespace BackendBundle\Entity;

/**
 * Sav
 */
class Sav
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $tittre;

    /**
     * @var string
     */
    private $commentaire;

    /**
     * @var \DateTime
     */
    private $date;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tittre
     *
     * @param string $tittre
     *
     * @return Sav
     */
    public function setTittre($tittre)
    {
        $this->tittre = $tittre;

        return $this;
    }

    /**
     * Get tittre
     *
     * @return string
     */
    public function getTittre()
    {
        return $this->tittre;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return Sav
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Sav
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
}

