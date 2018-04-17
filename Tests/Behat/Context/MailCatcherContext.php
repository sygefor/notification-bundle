<?php

namespace NotificationBundle\Tests\Behat\Context;

use Behat\Mink\Exception\ExpectationException;
use Behat\MinkExtension\Context\RawMinkContext;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareContext;

class MailCatcherContext extends RawMinkContext implements KernelAwareContext
{
    private $kernel;

    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    protected function getContainer()
    {
        return $this->kernel->getContainer();
    }

    /**
     * Opens the last received email
     * Example: And I check the last received email
     * Example: When I check the last received email.
     *
     * @When /^(?:|I )check the last received email$/
     *
     * @param string $format
     */
    public function checkLastMail(string $format = 'json')
    {
        $host = $this->getContainer()->getParameter('mailer_host');
        $port = $this->getContainer()->getParameter('mailer_http_port');
        $url = "http://$host:$port";

        $this->getSession('external')->visit($url.'/messages');
        $content = $this->getSession('external')->getPage()->getContent();
        $json = json_decode($content);

        $lastMessage = end($json);
        if (!$lastMessage) {
            throw new \Exception('No mail in queue.');
        }

        $lastId = $lastMessage->id;

        $this->getSession('external')->visit($url.'/messages/'.$lastId.'.'.$format);
    }

    /**
     * Checks that subject contains specified text.
     *
     * Example: Then the subject should contain "my subject"
     * Example: And the subject should contain "Batman"
     *
     * @Then /^the subject should contain "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function theSubjectShouldContain($text)
    {
        $message = sprintf('The text "%s" was not found in body.', $text);
        $this->theNodeShouldContain('subject', $text, $message);
    }

    /**
     * Checks that body contains specified text.
     *
     * Example: Then the body should contain "my subject"
     * Example: And the body should contain "Batman"
     *
     * @Then /^the body should contain "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function theBodyShouldContain($text)
    {
        $message = sprintf('The text "%s" was not found in body.', $text);
        $this->theNodeShouldContain('source', $text, $message);
    }

    /**
     * @When /^(?:I) follow the first link in the last received email/
     *
     * @throws \Exception
     */
    public function iFollowFirstLink()
    {
        $session = $this->getSession('external');
        $this->checkLastMail('html');
        $el = $session->getPage()->find('css', 'a');

        if (empty($el)) {
            throw new \Exception('No link found in mail.');
        }

        $session = $this->getSession('default');

        $session->visit($el->getAttribute('href'));
    }

    /**
     * Checks that json node contains specified text.
     *
     * Example: Then the node "subject" should contain "my subject"
     * Example: And the node "body" should contain "Batman"
     *
     * @Then /^the node "(?P<node>[^"]*)" should contain "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function theNodeShouldContain($node, $text, $message = null)
    {
        $content = $this->getSession('external')->getPage()->getContent();
        $object = json_decode($content);

        $content = $object->$node;
        if ('source' == $node) {
            $content = preg_replace('/=[\n\r|\n|\r]\s*/i', '', $content);
        }

        $regex = '/'.preg_quote($text, '/').'/ui';
        $message = $message ? $message : sprintf('The text "%s" was not found in the node "%s".', $text, $node);

        $this->assertText((bool) preg_match($regex, $content), $message);
    }

    /**
     * @param $condition
     * @param $message
     *
     * @throws ExpectationException
     */
    private function assertText($condition, $message)
    {
        if ($condition) {
            return;
        }
        throw new ExpectationException($message, $this->getSession()->getDriver());
    }
}
