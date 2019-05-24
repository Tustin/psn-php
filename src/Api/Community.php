<?php

namespace PlayStation\Api;

use PlayStation\Client;
use PlayStation\Api\User;

class Community extends AbstractApi 
{
    const COMMUNITY_ENDPOINT    = 'https://communities.api.playstation.com/v1/';
    const SATCHEL_ENDPOINT      = 'https://satchel.api.playstation.com/v1/item/community/%s';

    private $community;
    private $communityId;
    private $members;
    private $threads;

    public function __construct(Client $client, string $communityId) 
    {
        parent::__construct($client);

        $this->communityId = $communityId;
    }

    public function info(bool $force = false) : \stdClass
    {
        if ($this->community === null || $force) {
            $this->community = $this->get(sprintf(self::COMMUNITY_ENDPOINT . 'communities/%s', $this->communityId), [
                'includeFields' => 'backgroundImage,description,id,isCommon,members,name,profileImage,role,unreadMessageCount,sessions,timezoneUtcOffset,language,titleName',
            ]);
        }

        return $this->community;
    }

    public function id() : string
    {
        return $this->info()->id;
    }

    public function name() : string
    {
        return $this->info()->name;
    }

    public function description() : string
    {
        return $this->info()->description;
    }

    /**
     * Gets the amount of Users in the Community.
     *
     * @return integer
     */
    public function memberCount() : int 
    {
        return $this->info()->members->size;
    }

    /**
     * Sets the associated Game for the Community.
     *
     * @param string $titleId Title ID for the Game.
     * @return void
     */
    public function setGame(string $titleId) : void
    {
        $this->set([
            'titleId' => $titleId
        ]);
    }

    /**
     * Sets the image for the Community.
     *
     * @param string $imageData Raw bytes of the image.
     * @return void
     */
    public function setImage(string $imageData) : void
    {
        $url = $this->uploadImage('communityProfileImage', $imageData);

        $this->set([
            'profileImage' => [
                'sourceUrl' => $url
            ]
        ]);
    }

    /**
     * Sets the background image for the Community.
     *
     * @param string $imageData Raw bytes of the image.
     * @return void
     */
    public function setBackgroundImage(string $imageData) : void
    {
        $url = $this->uploadImage('communityBackgroundImage', $imageData);

        $this->set([
            'backgroundImage' => [
                'sourceUrl' => $url
            ]
        ]);
    }

    /**
     * Sets the background color for the Community.
     *
     * @param integer $color RGB value (e.g. 0x000000).
     * @return void
     */
    public function setBackgroundColor(int $color) : void
    {
        $background = $this->info(true)->backgroundImage;

        $this->set([
            'backgroundImage' => [
                'color' => sprintf('%06X', $color),
                'sourceUrl' => $background->sourceUrl ?? ''
            ]
        ]);
    }

    /**
     * Set who can join the Community.
     *
     * @param string $status 'open' or 'closed'.
     * @return void
     */
    public function setStatus(string $status) : void
    {
        $this->set([
            'type' => $status
        ]);
    }

    /**
     * Invite one or more Users to the Community.
     *
     * @param array $users Array of each User's onlineId as string.
     * @return void
     */
    public function invite(array $users) : void
    {
        $data = (object)[
            'onlineIds' => $users
        ];

        $this->postJson(sprintf(self::COMMUNITY_ENDPOINT . 'communities/%s/members', $this->id()), $data);
    }

    /**
     * Get the Users in the Community.
     *
     * @param integer $limit Amount of Users to return.
     * @return array Array of Api\User.
     */
    public function members(int $limit = 100) : array
    {
        $returnMembers = [];

        $members = $this->get(sprintf(self::COMMUNITY_ENDPOINT . 'communities/%s/members', $this->id()), [
            'limit' => $limit
        ]);

        if ($members->size === 0) return $returnMembers;

        foreach ($members->members as $member) {
            $returnMembers[] = new User($this->client, $member->onlineId);
        }

        return $returnMembers;
    }

    public function threads() : \stdClass
    {
        if ($this->threads === null) {
            $this->threads = $this->get(sprintf(self::COMMUNITY_ENDPOINT . 'communities/%s/threads', $this->id()));
        }

        return $this->threads;
    }

    /**
     * Gets the Game the Community is associated with.
     *
     * @return Game|null
     */
    public function game() : ?Game
    {
        if (!isset($this->info()->titleId)) return null;

        return new Game($this->client, $this->info()->titleId);
    }

    /**
     * Uploads an image to Sony's CDN and returns the URL.
     * 
     * Sony requires all images (profile images, community images, etc) to be set with this CDN URL.
     *
     * @param string $purpose
     * @return string
     */
    private function uploadImage(string $purpose, string $imageData) : string
    {
        $parameters = [
            [
                'name' => 'purpose',
                'contents' => $purpose,
            ],
            [
                'name' => 'file',
                'filename' => 'dummy_file_name',
                'contents' => $imageData,
                'headers' => [
                    'Content-Type' => 'image/jpeg'
                ]
            ],
            [
                'name' => 'mimeType',
                'contents' => 'image/jpeg',
            ]
        ];

        $response = $this->postMultiPart(sprintf(self::SATCHEL_ENDPOINT, $this->id()), $parameters);

        return $response->url;
    }

    /**
     * Sets a property on the Community.
     *
     * @param array $postData
     * @return object
     */
    private function set(array $postData)
    {
        return $this->putJson(sprintf(self::COMMUNITY_ENDPOINT . 'communities/%s', $this->id()), $postData);
    }
}