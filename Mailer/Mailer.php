<?php

namespace NotificationBundle\Mailer;

use Pelago\Emogrifier;
use Html2Text\Html2Text;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Symfony\Component\Cache\Exception\InvalidArgumentException;

/**
 * Class Mailer.
 */
class Mailer
{
    /**
     * @var \Swift_Mailer
     */
	protected $mailer;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var string
     */
	protected $senderAddress;

    /**
     * @var string
     */
	protected $senderName;

    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var string
     */
	protected $subjectTemplate;

    /**
     * Mailer constructor.
     *
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $twig
     * @param array $configuration
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, array $configuration)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->configuration = $configuration;
    }

    /**
     * @param string $address
     * @param string $name
     */
    public function setSender($address, $name = null)
    {
        $this->senderAddress = $address;
        $this->senderName = $name;
    }

    /**
     * @param string $subjectTemplate
     */
    public function setSubjectTemplate($subjectTemplate)
    {
        $this->subjectTemplate = $subjectTemplate;
    }

    /**
     * @param $code
     * @param $recipient
     * @param array $data
     * @param array $excludes
     *
     * @return int
     *
     * @throws \Html2Text\Html2TextException
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function send($code, $recipient, array $data = [], array $excludes = [])
    {
        if ($recipient instanceof MailerRecipientsInterface) {
            $recipient = $recipient->getRecipients();
        }

        if (is_array($recipient) || $recipient instanceof \Traversable) {
            $success = 0;
            foreach ($recipient as $_recipient) {
                $success += $this->send($code, $_recipient, $data, $excludes);
            }

            return $success;
        }

        if ($recipient instanceof MailerRecipientInterface) {
            if (in_array($recipient, $excludes)) {
                return 0;
            }
            $data['recipient'] = $recipient;
            $recipient = $recipient->getEmail();
        }

        $emailValidator = new EmailValidator();
        if (!$emailValidator->isValid($recipient, (new RFCValidation()))) {
            return 0;
        }

        if (in_array($recipient, $excludes)) {
            return 0;
        }

        $email = $this->getRenderedEmail($code, $data);
        if (!$email->isEnabled()) {
            return 0;
        }

        // precode the subject to avoid switfmailer bug
        // fix bug https://github.com/swiftmailer/swiftmailer/issues/665
        $subject = '=?UTF-8?B?' . base64_encode($email->getSubject()) . '?=';
        $message = (new \Swift_Message(null, null, 'text/html', 'utf-8'))
            ->setSubject($subject)
            ->setFrom($email->getSenderAddress(), $email->getSenderName())
            ->setTo([$recipient]);

        $message->setBody($email->getBody());
        $message->addPart(Html2Text::convert($email->getBody()), 'text/plain');

        return $this->mailer->send($message);
    }

    /**
     * @param $code
     * @param array $data
     *
     * @return Email
     *
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function getRenderedEmail($code, array $data)
    {
        // load email from config
        $email = $this->getEmailFromConfiguration($code);

        // process template
        $data = $this->twig->mergeGlobals($data);
        $template = $this->twig->loadTemplate($email->getTemplate());
        $subject = $template->renderBlock('subject', $data);
        $html = $template->render($data);

        // inline css
        $emogrifier = new Emogrifier($html);
        $body = $emogrifier->emogrify();

        // alter email
        $email->setBody($body);
        if ($subject) {
            $email->setSubject($subject);
        }

        if ($this->subjectTemplate) {
            $email->setSubject(sprintf($this->subjectTemplate, $email->getSubject()));
        }

        return $email;
    }

    /**
     * @param string $code
     *
     * @return Email
     */
	protected function getEmailFromConfiguration($code)
    {
        if (!isset($this->configuration[$code])) {
            throw new InvalidArgumentException(sprintf('Email with code "%s" does not exist!', $code));
        }

        $configuration = $this->configuration[$code];

        $email = new Email();
        $email->setSenderAddress($this->senderAddress);
        $email->setSenderName($this->senderName);
        $email->setCode($code);
        $email->setTemplate($configuration['template']);

        if (isset($configuration['subject'])) {
            $email->setSubject($configuration['subject']);
        }

        if (isset($configuration['enabled']) && false === $configuration['enabled']) {
            $email->setEnabled(false);
        }
        if (isset($configuration['sender']['name'])) {
            $email->setSenderName($configuration['sender']['name']);
        }
        if (isset($configuration['sender']['address'])) {
            $email->setSenderAddress($configuration['sender']['address']);
        }

        return $email;
    }
}
