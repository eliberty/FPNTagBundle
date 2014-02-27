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
    public function addTag($tag)
    {
        $this->getTags()->add($tag);

        return $this;
    }

    /**
     * Add tags
     *
     * @param $tags
     * @return this
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param object $tag
     */
    public function removeTag($tag)
    {
        $this->getTags()->removeElement($tag);

        return $this;
    }

    /**
     * taggable interface functions
     * @return ArrayCollection
     */
    public function getTags()
    {
        if (is_callable($this->tags)) {
            $this->tags->__invoke();
        }
        $this->tags = $this->tags ?: new ArrayCollection();
        return $this->tags;
    }

    public function getTaggableId()
    {
        return $this->getId();
    }

}
