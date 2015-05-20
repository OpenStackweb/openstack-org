<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Context\SnippetAcceptingContext;

/**
 * WebContext context.
 */
class WebContext extends MinkContext implements SnippetAcceptingContext
{
    /**
     * @When I wait for the suggestion box to appear
     */
    public function iWaitForTheSuggestionBoxToAppear()
    {

        $this->getSession()->executeScript("
        $('#searchInput').trigger('keypress');");
        $this->getSession()->wait(5000,
            "$('.suggestions-results').children().length > 0"
        );
    }

}
