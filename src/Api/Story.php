<?php

namespace PlayStation\Api;

use PlayStation\Client;
use PlayStation\Api\User;

class Story extends AbstractApi 
{
    public const ACTIVITY_ENDPOINT    = 'https://activity.api.np.km.playstation.net/activity/api/';

    private $story;
    private $user;

    public function __construct(Client $client, object $story, User $user) 
    {
        parent::__construct($client);
        
        $this->story = $story;
        $this->user = $user;        
    }

    /**
     * Gets the info for the Story.
     *
     * @return object
     */
    public function info() : \stdClass
    {
        return $this->story;
    }

    /**
     * Gets the User who posted the Story.
     *
     * @return User
     */
    public function user() : User
    {
        return $this->user;
    }

    /**
     * Gets the Story ID.
     *
     * @return string
     */
    public function storyId() : string
    {
        return $this->info()->storyId;
    }

    /**
     * Gets the Story type.
     *
     * @return string
     */
    public function storyType() : string
    {
        return $this->info()->storyType;
    }

    /**
     * Gets the title ID for the game the Story is for.
     *
     * @return string
     */
    public function titleId() : string
    {
        return $this->info()->titleId;        
    }

    /**
     * Checks if the logged in user has liked this Story.
     *
     * @return boolean
     */
    public function liked() : bool
    {
        return $this->info()->liked;        
    }

    /**
     * Gets the post date for the Story.
     *
     * @return \DateTime
     */
    public function postDate() : \DateTime
    {
        return new \DateTime($this->info()->date);
    }

    /**
     * Gets the amount of comments on the Story.
     *
     * @return integer
     */
    public function commentCount() : int
    {
        return $this->info()->commentCount;
    }

    /**
     * Gets the amount of likes on the Story.
     *
     * @return integer
     */
    public function likeCount() : int
    {
        return $this->info()->likeCount;
    }

    /**
     * Generates the caption shown on PlayStation.
     *
     * @return string
     */
    public function caption() : string
    {
        $template = $this->info()->captionTemplate;

        foreach ($this->info()->captionComponents as $variable) {
            $template = str_replace('$' . $variable->key, $variable->value, $template);
        }

        return $template;
    }

    /**
     * Gets the Game the Story is for.
     *
     * @return Game
     */
    public function Game() : Game
    {
        return new Game($this->client, $this->titleId(), $this->user());
    }

    /**
     * Leave a comment on the Story.
     *
     * @param string $message The comment.
     * @return Comment|null
     */
    public function comment(string $message) : ?Comment
    {
        $comment = $this->postJson(sprintf(self::ACTIVITY_ENDPOINT . 'v1/users/me/comment/%s', $this->storyId()), [
            'commentString' => $message
        ]);

        // Since I couldn't find an endpoint that gave me a comment's info using just it's comment id, let's just grab the newest comment.
        $newest = $this->comments(0, 1, 'ASC');

        if (count($newest) === 0) return null;
        
        return $newest[0];
    }

    /**
     * Gets all the Comments for the Story.
     *
     * @param integer $start Which comments to start from.
     * @param integer $count How many comments to get.
     * @param string $sort How comments are sorted (ASC/DESC).
     * @return array Array of API\Comment.
     */
    public function comments(int $start = 0, int $count = 10, string $sort = 'ASC') : array
    {
        $returnComments = [];

        if ($this->commentCount() === 0) return $returnComments;

        $comments = $this->get(sprintf(self::ACTIVITY_ENDPOINT . 'v1/users/%s/stories/%s/comments', $this->user()->onlineIdParameter(), $this->storyId()), [
            'start' => $start,
            'count' => $count,
            'sort' => $sort
        ]);

        foreach ($comments->userComments as $comment) {
            $returnComments[] = new Comment($this->client, $comment, $this);
        }
        
        return $returnComments;
    }

}