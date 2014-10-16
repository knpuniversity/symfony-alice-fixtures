<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="video_game_character")
 * @ORM\Entity
 */
class Character
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
     * @ORM\Column()
     */
    private $name;

    /**
     * @ORM\Column()
     */
    private $realName;

    /**
     * @ORM\Column(type="integer")
     */
    private $highScore;

    /**
     * @ORM\Column()
     */
    private $email;

    /**
     * @var Universe
     * @ORM\ManyToOne(targetEntity="Universe")
     */
    private $universe;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $tagLine;

    /**
     * @ORM\Column(nullable=true)
     */
    private $avatarFilename;

    /**
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTagLine()
    {
        return $this->tagLine;
    }

    public function setTagLine($tagLine)
    {
        $this->tagLine = $tagLine;
    }

    public function setUniverse($affiliatedUniverse)
    {
        $this->universe = $affiliatedUniverse;
    }

    /**
     * @return Universe
     */
    public function getUniverseName()
    {
        return $this->universe ? $this->universe->getName() : '';
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setHighScore($highScore)
    {
        $this->highScore = $highScore;
    }

    public function getHighScore()
    {
        return $this->highScore;
    }

    public function setAvatarFilename($logoFilename)
    {
        $this->avatarFilename = $logoFilename;
    }

    public function getAvatarFilename()
    {
        return $this->avatarFilename;
    }

    public function setRealName($realName)
    {
        $this->realName = $realName;
    }

    public function getRealName()
    {
        return $this->realName;
    }
}
