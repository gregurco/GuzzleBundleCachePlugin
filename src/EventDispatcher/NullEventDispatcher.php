<?php

namespace Gregurco\Bundle\GuzzleBundleCachePlugin\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NullEventDispatcher implements EventDispatcherInterface
{
    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, Event $event = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function removeListener($eventName, $listener)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners($eventName = null)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getListenerPriority($eventName, $listener)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function hasListeners($eventName = null)
    {
        return false;
    }
}
