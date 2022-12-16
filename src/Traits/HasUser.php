<?php
namespace Tustin\PlayStation\Traits;

use Tustin\PlayStation\Model\User;

trait HasUser
{
    protected User $user;

    public function withUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function user(): User
    {
        return $this->user;
    }
}