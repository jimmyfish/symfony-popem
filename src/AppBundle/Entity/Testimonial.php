<?php

namespace AppBundle\Entity;

/**
 * Testimonial.
 */
class Testimonial
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $nameTestimonial;

    /**
     * @var int
     */
    private $status;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Testimonial
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set nameTestimonial.
     *
     * @param string $nameTestimonial
     *
     * @return Testimonial
     */
    public function setNameTestimonial($nameTestimonial)
    {
        $this->nameTestimonial = $nameTestimonial;

        return $this;
    }

    /**
     * Get nameTestimonial.
     *
     * @return string
     */
    public function getNameTestimonial()
    {
        return $this->nameTestimonial;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
