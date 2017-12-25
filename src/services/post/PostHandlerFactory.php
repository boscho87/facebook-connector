<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 25.12.2017
 * Time: 17:05
 */

namespace itscoding\facebookconnector\services\post;

class PostHandlerFactory
{

    /**
     * @var PostCreator
     */
    private $postCreator;
    /**
     * @var PostUpdater
     */
    private $postUpdater;

    /**
     * PostHandlerFactory constructor.
     * @param PostCreator|null $postCreator
     * @param PostUpdater|null $postUpdater
     */
    public function __construct(PostCreator $postCreator = null, PostUpdater $postUpdater = null)
    {
        $this->postCreator = $postCreator ?? new PostCreator();
        $this->postUpdater = $postUpdater ?? new PostUpdater();
    }

    /**
     * @return PostCreator
     */
    public function getPostCreator()
    {
        return $this->postCreator;
    }

    /**
     * @return PostUpdater
     */
    public function getPostUpdater()
    {
        return $this->postUpdater;
    }
}
