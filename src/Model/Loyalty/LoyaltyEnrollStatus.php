<?php
namespace Tustin\PlayStation\Model\Loyalty;

use Carbon\Carbon;

class LoyaltyEnrollStatus
{
    /**
     * Loyalty enrollment status data
     *
     * @var object
     */
    private $data;

    public function __construct(object $data)
    {
        $this->data = $data;
    }

    /**
     * Gets the date and time the user enrolled in the loyalty program.
     *
     * @return \DateTime
     */
    public function enrolledDateTime(): \DateTime
    {
        return Carbon::parse($this->data->enrolledDateTime);
    }

    /**
     * Checks if the user is eligible to enroll in the loyalty program.
     *
     * @return boolean
     */
    public function isUserEligibleToEnroll(): bool
    {
        return (bool)$this->data->isUserEligibleToEnroll;
    }

    /**
     * Checks if the user is enrolled in the loyalty program.
     *
     * @return boolean
     */
    public function isUserEnrolled(): bool
    {
        return (bool)$this->data->isUserEnrolled;
    }

    /**
     * Gets the enrollment status.
     *
     * @return string
     */
    public function status(): string
    {
        return (bool)$this->data->status;
    }
}
