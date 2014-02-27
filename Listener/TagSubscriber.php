<?php

namespace FPN\TagBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs,
    Doctrine\ORM\Event\PostFlushEventArgs,
    Doctrine\ORM\Event\OnFlushEventArgs,
    Doctrine\ORM\Event\PreFlushEventArgs,
    Doctrine\Common\EventSubscriber,
    Doctrine\ORM\Events;

use Symfony\Component\DependencyInjection\ContainerInterface;


class TagSubscriber implements EventSubscriber
{


    protected $container;

    protected $tagClass = null;

    protected $taggingClass = null;

    protected $subscribedEvents= [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        if ($this->container->hasParameter('fpn_tag.entity.tag.class')) {
            $this->tagClass = $this->container->getParameter('fpn_tag.entity.tag.class');
            $this->taggingClass = $this->container->getParameter('fpn_tag.entity.tagging.class');
            $this->subscribedEvents = [
                                        Events::postLoad,
                                        Events::preFlush,
                                    ];
        }
    }

    public function getSubscribedEvents()
    {
        return $this->subscribedEvents;
    }


    /**
     * test if a class use TaggagleBehavior and save tagging relations
     * @param  LifecycleEventArgs $args [description]
     * @return [type]                   [description]
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entities = new \ArrayIterator();
        $entity = $args->getEntity();
        $tagManager = $this->container->get('fpn_tag.tag_manager');
        if (in_array('DoctrineExtensions\Taggable\Taggable',class_implements($entity))) {
            $getTagsClosure = function() use ($tagManager, $entity){
                $tagManager->loadTagging($entity);
            };
            $entity->setTags($getTagsClosure);
        }
    }

    /**
     * test if a class use TaggagleBehavior and save tagging relations
     * @param  LifecycleEventArgs $args [description]
     * @return [type]                   [description]
     */
    public function preFlush(PreFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();
        $toFlush = [];
        foreach ($uow->getIdentityMap() as $entity) {
            $entities = (!is_array($entity)) ? array($entity) : $entity;
            foreach ($entities as $entity) {
                $flushed = $this->checkForTags($entity);
                if (count($flushed)) {
                    $toFlush = array_merge($toFlush, $flushed);
                }
            }
        }

        if ( count($toFlush)) {
            foreach($toFlush as $flushed) {
                $uow->computeChangeSet($em->getClassMetadata(get_class($flushed)), $flushed);
                $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(get_class($flushed)), $flushed);
            }
        }

    }

    private function checkForTags($entity)
    {
        $toFlush=[];
        if (in_array('DoctrineExtensions\Taggable\Taggable',class_implements($entity))) {
            $tagManager = $this->container->get('fpn_tag.tag_manager');
            $toFlush = $tagManager->saveTagging($entity, false);
        }
        return $toFlush;
    }
}