<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity
 */
class Hero
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column()
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Universe")
     * @ORM\JoinColumn(nullable=false)
     */
    private $affiliatedUniverse;

    /**
     * @var string
     *
     * @ORM\Column()
     */
    private $tagLine;

    /**
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTagLine()
    {
        return $this->tagLine;
    }

    /**
     * @param string $tagLine
     */
    public function setTagLine($tagLine)
    {
        $this->tagLine = $tagLine;
    }

    /**
     * @param Universe $affiliatedUniverse
     */
    public function setAffiliatedUniverse($affiliatedUniverse)
    {
        $this->affiliatedUniverse = $affiliatedUniverse;
    }

    /**
     * @return Universe
     */
    public function getAffiliatedUniverse()
    {
        return $this->affiliatedUniverse;
    }
}
