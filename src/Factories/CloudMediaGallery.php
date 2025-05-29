<?php

namespace Tustin\PlayStation\Factories;

use Iterator;
use IteratorAggregate;
use Tustin\PlayStation\Api;
use Tustin\PlayStation\Models\Media;
use Tustin\PlayStation\Enums\TranscodeStatusType;
use Tustin\PlayStation\Exceptions\FilterException;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Iterators\CloudMediaGalleryIterator;
use Tustin\PlayStation\Iterators\Filter\TrophyTitle\TitleIdFilter;

class CloudMediaGallery extends Api implements IteratorAggregate, FactoryInterface
{
    private $title;

    private string $titleId = '';
    private string $communcationId = '';
    private string $withDetail = '';
    private TranscodeStatusType $transcodeStatus;

    public function __construct() {}

    /**
     * Filters media based on title id.
     */
    public function withTitleId(string $titleId): CloudMediaGallery
    {
        if ($this->communcationId) {
            throw new FilterException('Cannot filter by title id when a communication id filter is already set.');
        }

        $this->titleId = $titleId;

        return $this;
    }

    /**
     * Filters media based on NP communication id (trophy id).
     * 
     * Cannot be paired with withTitleId.
     */
    public function withCommunicationId(string $communcationId): CloudMediaGallery
    {
        if ($this->title) {
            throw new FilterException('Cannot filter by communcation id when a title id filter is already set.');
        }

        $this->communcationId = $communcationId;

        return $this;
    }

    /**
     * Filters media based on it's transcoding status.
     * 
     * Useful for filtering out any non-completed media.
     */
    public function withStatus(TranscodeStatusType $status): CloudMediaGallery
    {
        $this->transcodeStatus = $status;

        return $this;
    }

    /**
     * Gets the iterator and applies any filters.
     */
    public function getIterator(): Iterator
    {
        $iterator = new CloudMediaGalleryIterator($this);

        if ($this->titleId) {
            $iterator = new TitleIdFilter($iterator, $this->titleId);
        }

        // @TODO: Implement other filters.

        return $iterator;
    }

    /**
     * Gets the first media asset in the collection.
     */
    public function first(): Media
    {
        return $this->getIterator()->current();
    }
}
