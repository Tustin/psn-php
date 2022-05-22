<?php

namespace Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tustin\PlayStation\Model\User;

class UserTest extends TestCase
{
    /**
     * @var MockObject|User
     */
    protected $user;

    protected function setUp(): void
    {
        $this->user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fetch', 'accountId'])
            ->getMock();

        // Data response from 'userProfile/v1/internal/users/[accountId]/profiles'
        $data = [
            'onlineId' => 'psnusername',
            'aboutMe' => '',
            'avatars' => [],
            'languages' => [],
            'isPlus' => true,
            'isOfficiallyVerified' => false,
            'isMe' => false,
        ];

        $this->user->method('fetch')->willReturn((object)$data);
        $this->user->method('accountId')->willReturn('0123456789');
    }

    /**
     *  Try pluck data from onlineId().
     *
     * @test
     */
    public function pluck_onlineId(): void
    {
        $onlineId = $this->user->onlineId();
        $this->assertIsString($onlineId);
    }

    /**
     *  Try pluck data from aboutMe().
     *
     * @test
     */
    public function pluck_aboutMe(): void
    {
        $aboutMe = $this->user->aboutMe();
        $this->assertIsString($aboutMe);
    }

    /**
     *  Try pluck data from accountId().
     *
     * @test
     */
    public function pluck_accountId(): void
    {
        $accountId = $this->user->accountId();
        $this->assertIsString($accountId);
    }

    /**
     *  Try pluck data from avatarUrls().
     *
     * @test
     */
    public function pluck_avatarUrls(): void
    {
        $avatarUrls = $this->user->avatarUrls();
        $this->assertIsArray($avatarUrls);
    }

    /**
     *  Try pluck data from avatarUrl().
     *
     * @test
     */
    public function pluck_avatarUrl(): void
    {
        $avatarUrl = $this->user->avatarUrl();
        $this->assertIsString($avatarUrl);
    }

    /**
     *  Try pluck data from isBlocking().
     *
     * @test
     */
    public function pluck_isBlocking(): void
    {
        $isBlocking = $this->user->isBlocking();
        $this->assertIsBool($isBlocking);
    }

    /**
     *  Try pluck data from followerCount().
     *
     * @test
     */
    public function pluck_followerCount(): void
    {
        $followerCount = $this->user->followerCount();
        $this->assertIsInt($followerCount);
    }

    /**
     *  Try pluck data from isFollowing().
     *
     * @test
     */
    public function pluck_isFollowing(): void
    {
        $isFollowing = $this->user->isFollowing();
        $this->assertIsBool($isFollowing);
    }

    /**
     *  Try pluck data from isVerified().
     *
     * @test
     */
    public function pluck_isVerified(): void
    {
        $isVerified = $this->user->isVerified();
        $this->assertIsBool($isVerified);
    }

    /**
     *  Try pluck data from languages().
     *
     * @test
     */
    public function pluck_languages(): void
    {
        $languages = $this->user->languages();
        $this->assertIsArray($languages);
    }

    /**
     *  Try pluck data from mutualFriendCount().
     *
     * @test
     */
    public function pluck_mutualFriendCount(): void
    {
        $mutualFriendCount = $this->user->mutualFriendCount();
        $this->assertIsInt($mutualFriendCount);
    }

    /**
     *  Try pluck data from hasMutualFriends().
     *
     * @test
     */
    public function pluck_hasMutualFriends(): void
    {
        $hasMutualFriends = $this->user->hasMutualFriends();
        $this->assertIsBool($hasMutualFriends);
    }

    /**
     *  Try pluck data from isCloseFriend().
     *
     * @test
     */
    public function pluck_isCloseFriend(): void
    {
        $isCloseFriend = $this->user->isCloseFriend();
        $this->assertIsBool($isCloseFriend);
    }

    /**
     *  Try pluck data from hasFriendRequested().
     *
     * @test
     */
    public function pluck_hasFriendRequested(): void
    {
        $hasFriendRequested = $this->user->hasFriendRequested();
        $this->assertIsBool($hasFriendRequested);
    }

    /**
     *  Try pluck data from isOnline().
     *
     * @test
     */
    public function pluck_isOnline(): void
    {
        $isOnline = $this->user->isOnline();
        $this->assertIsBool($isOnline);
    }

    /**
     *  Try pluck data from hasPlus().
     *
     * @test
     */
    public function pluck_hasPlus(): void
    {
        $hasPlus = $this->user->hasPlus();
        $this->assertIsBool($hasPlus);
    }

    /**
     * Test fetch() method
     *
     * @test
     */
    public function fetch_user_data(): void
    {
        $data = $this->user->fetch();
        $this->assertIsObject($data);
    }
}