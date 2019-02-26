<?php

namespace NotificationBundle\Mailer;

/**
 * Class Email.
 */
class Email
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $template;

    /**
     * @var string
     */
    private $body;

    /**
     * @var bool
     */
    private $enabled = true;

    /**
     * @var string
     */
    private $senderAddress;

    /**
     * @var string
     */
    private $senderName;

	/**
	 * @var array
	 */
    private $additionalParams;

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->subject = trim($subject);
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getSenderAddress()
    {
        return $this->senderAddress;
    }

    /**
     * @param string $senderAddress
     */
    public function setSenderAddress($senderAddress)
    {
        $this->senderAddress = $senderAddress;
    }

    /**
     * @return string
     */
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * @param string $senderName
     */
    public function setSenderName($senderName)
    {
        $this->senderName = $senderName;
    }

	/**
	 * @return mixed
	 */
	public function getAdditionalParams()
	{
		return $this->additionalParams;
	}

	/**
	 * @param array $additionalParams
	 */
	public function setAdditionalParams($additionalParams)
	{
		$this->additionalParams = $additionalParams;
	}

	/**
	 * @param  string $key
	 *
	 * @return bool
	 */
	public function hasAdditionalParam($key)
	{
		return isset($this->additionalParams[$key]);
	}

	/**
	 * @param  $key string
	 *
	 * @return mixed
	 */
	public function getAdditionalParam($key)
	{
		return $this->additionalParams[$key];
	}
}
