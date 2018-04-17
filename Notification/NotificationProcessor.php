<?php

namespace NotificationBundle\Notification;

/**
 * Class NotificationProcessor.
 */
class NotificationProcessor
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * NotificationProcessor constructor.
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke($record)
    {
        $context = $record['context'];
        /** @var NotificationTypeInterface $notification */
        $notification = $record['context']['notification'];

        // compile message
        $template = $this->twig->createTemplate($notification->getMessage());
        $html = $template->render($context);
        $record['message'] = $html;

        // strip tags in message
        $filter = $this->twig->getFilter('striptags')->getCallable();
        $record['extra']['plain_message'] = $filter($html);

        // compile description
        if ($notification->getDescription()) {
            $template = $this->twig->createTemplate($notification->getDescription());
            $record['extra']['description'] = $template->render($context);
        }

        // compile route
        if ($notification->getRoute()) {
            $record['extra']['route'] = $notification->getRoute();
            $record['extra']['route_parameters'] = $notification->getRouteParameters();
        }

        return $record;
    }
}
