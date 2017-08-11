<?php

/**
 * Class BookSapphireRender
 */
final class BookSapphireRender
{
    /**
     * @var IBook
     */
    private $book;

    public function __construct(IBook $book)
    {
        $this->book = $book;
    }

    public function draw()
    {
        Requirements::css("marketplace/code/ui/frontend/css/marketplace.css");
        Requirements::javascript("marketplace/code/ui/frontend/js/book.page.js");

        return Controller::curr()->Customise($this->book)->renderWith(array('BooksDirectoryPage_book', 'BooksDirectoryPage', 'MarketPlacePage'));
    }
} 