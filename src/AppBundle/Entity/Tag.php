<?php

namespace AppBundle\Entity;

/**
 * Tag
 */
class Tag
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $nameTag;


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
     * Set nameTag
     *
     * @param string $nameTag
     *
     * @return Tag
     */
    public function setNameTag($nameTag)
    {
        $this->nameTag = $nameTag;

        return $this;
    }

    /**
     * Get nameTag
     *
     * @return string
     */
    public function getNameTag()
    {
        return $this->nameTag;
    }
}

