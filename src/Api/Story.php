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

    public function info() : object
    {
        return $this->story;
    }

    public function user() : User
    {
        return $this->user;
    }

    public function storyId() : string
    {
        return $this->info()->storyId;
    }

    public function storyType() : string
    {
        return $this->info()->storyType;
    }

    public function titleId() : string
    {
        return $this->info()->titleId;        
    }

    public function liked() : bool
    {
        return $this->info()->liked;        
    }

    public function postDate() : \DateTime
    {
        return new \DateTime($this->info()->date);
    }

    public function commentCount() : int
    {
        return $this->info()->commentCount;
    }

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

    public function Game() : Game
    {
        return new Game($this->client, $this->titleId(), $this->user());
    }

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

    public function comments(int $start = 0, int $count = 10, string $sort = 'ASC') : array
    {
        if ($this->commentCount() === 0) return [];

        $returnComments = [];
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