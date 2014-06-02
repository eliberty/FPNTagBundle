<?php

namespace FPN\TagBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;

use FPN\TagBundle\Util\Slugifier;
use JMS\Serializer\Annotation\Accessor,
    JMS\Serializer\Annotation\Expose;

trait Taggable
{
    /**
     * @var \ArrayCollection
     * @Expose
     */
    protected $tags;

    /**
     * @var Slugifier
     */
    protected $tagSlugifier;


    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->tagSlugifier = new Slugifier();
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
        // lazy loading
        if (is_callable($this->tags)) {
            $this->tags->__invoke();
        }
        $this->tags = $this->tags ? $this->tags : new ArrayCollection();
        return $this->tags;
    }

    /**
     * check if an taggable entity contains this tag
     * @param  [type]  $stringTag [description]
     * @return boolean            [description]
     */
    public function hasTag($stringTag)
    {
        $has=  false;
        $slug = $this->tagSlugifier->slugify($stringTag);
        $currentTags = $this->getTags();
        if (count($currentTags)) {
            foreach ($currentTags as $tag) {
                if ($tag->getSlug() == $slug) {
                    $has = true;
                    break;
                }
            }
        }
        return $has;
    }

    /**
     * get array of tag name instead of tags entity
     * @return [type] [description]
     */
    public function getStringTags()
    {
        $tags = new ArrayCollection();
        $currentTags = $this->getTags();
        if (count($currentTags)) {
            foreach ($currentTags as $t) {
                $tags->add($t->getName());
            }
        }

        return $tags;
    }

    /**
     * return entity id
     * @return [type] [description]
     */
    public function getTaggableId()
    {
        return $this->getId();
    }

}
