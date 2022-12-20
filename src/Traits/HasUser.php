<?php
namespace Tustin\PlayStation\Traits;

use Tustin\PlayStation\Model\User;

trait HasUser
{
    private User $user;

    /**
     * Sets the user for this object.
     */
    public function withUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Gets the user for this object.
     */
    public function user(): User
    {
        return $this->user;
    }
}