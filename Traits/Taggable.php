<?php

namespace FPN\TagBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;

trait Taggable
{
    /**
     * @var \ArrayCollection
     */
    protected $tags;


    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public static function getTaggableType()
    {
        $reflect = new \ReflectionClass(get_called_class());
        return strtolower($reflect->getShortName().'_tag');
    }

    /**
     * Add tag
     *
     * @param object $tag
     * @return this
     */
    public function addTag( $tag)
    {
        $this->tags->add($tag);

        return $this;
    }

    /**
     * Add tags
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tags
     * @return this
     */
    public function setTags(ArrayCollection $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param object $tag
     */
    public function removeTag( $tag)
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * taggable interface functions
     * @return ArrayCollection
     */
    public function getTags()
    {
        $this->tags = $this->tags ?: new ArrayCollection();
        return $this->tags;
    }

    public function getTaggableId()
    {
        return $this->getId();
    }

}
