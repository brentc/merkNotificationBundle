<?php

namespace merk\NotificationBundle\Features\Context;

use Behat\BehatBundle\Context\BehatContext,
Behat\BehatBundle\Context\MinkContext;
use Behat\Behat\Context\ClosuredContextInterface,
Behat\Behat\Context\TranslatedContextInterface,
Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
Behat\Gherkin\Node\TableNode;

use merk\NotificationBundle\Features\Document\Post;
use merk\NotificationBundle\Features\Document\User;

/**
 * Require 3rd-party libraries here:
 */
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';


/**
 * Feature context.
 */
class FeatureContext extends BehatContext //MinkContext if you want to test web
{
    /**
     * @BeforeScenario
     */
    public function createUsers($event)
    {
        $om = $this->getObjectManager();
        $user = new User();
        $user->setUsername('SiteOwner');
        $om->persist($user);
        $user = new User();
        $user->setUsername('SiteGuest');
        $om->persist($user);
        $om->flush();
    }

    /**
     * @AfterScenario
     */
    public function emptyMongoCollections($event)
    {
        $dm = $this->getObjectManager();
        $dm->createQueryBuilder('merk\NotificationBundle\Features\Document\User')
            ->remove()
            ->getQuery()
            ->execute();
        $dm->createQueryBuilder('merk\NotificationBundle\Features\Document\Post')
            ->remove()
            ->getQuery()
            ->execute();
    }

    /**
     * @Given /^"([^"]*)" creates a new post named "([^"]*)"$/
     */
    public function createsANewPostNamed($author, $name)
    {
        $om = $this->getObjectManager();
        $author = $om->createQueryBuilder('merk\NotificationBundle\Features\Document\User')
            ->field('username')->equals($author)
            ->getQuery()
            ->execute()
            ->getSingleResult();

        $post = new Post();
        $post->setName($name);
        $post->setAuthor($author);
        $om->persist($post);
        $om->flush();
        assertNotNull($post->getId());
    }

    /**
     * @Given /^the "([^"]*)" publishes the post "([^"]*)"$/
     */
    public function thePublishesThePost($author, $postName)
    {
        $om = $this->getObjectManager();
        $post = $om->createQueryBuilder('merk\NotificationBundle\Features\Document\Post')
            ->field('name')->equals($postName)
            ->getQuery()
            ->execute()
            ->getSingleResult();
        $post->publish();
        $om->flush();
    }

    /**
     * @Then /^a notification is created$/
     */
    public function aNotificationIsCreated()
    {
        $om = $this->getObjectManager();
        assertNotNull(null);
    }

    /**
     * @Given /^a reference to "([^"]*)" is persisted as the "([^"]*)" of the notification$/
     */
    public function aReferenceToIsPersistedAsTheOfTheNotification($object, $part)
    {
        throw new PendingException();
    }

    /**
     * @Given /^"([^"]*)" is persisted as the verb of the notification$/
     */
    public function isPersistedAsTheVerbOfTheNotification($argument1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^the "([^"]*)" updates the post "([^"]*)"$/
     */
    public function theUpdatesThePost($author, $argument2)
    {
        throw new PendingException();
    }

    /**
     * @Given /^"([^"]*)" creates a comment on the post "([^"]*)"$/
     */
    public function createsACommentOnThePost($author, $postName)
    {
        throw new PendingException();
    }

    /**
     * @Given /^"([^"]*)" deletes the post "([^"]*)"$/
     */
    public function deletesThePost($author, $postName)
    {
        throw new PendingException();
    }

    protected function getObjectManager()
    {
        return $this->getContainer()->get('doctrine.odm.mongodb.default_document_manager');
    }
}
