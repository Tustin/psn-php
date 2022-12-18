<?php
namespace Tustin\PlayStation\Factory;

use Tustin\PlayStation\Api;
use Tustin\PlayStation\Model\Media;
use Tustin\PlayStation\Enum\TranscodeStatusType;
use Tustin\PlayStation\Exception\FilterException;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Iterator\CloudMediaGalleryIterator;
use Tustin\PlayStation\Iterator\Filter\TrophyTitle\TitleIdFilter;

class CloudMediaGalleryFactory extends Api implements \IteratorAggregate, FactoryInterface
{
    private $title;

    private string $titleId = '';
    private string $npCommId = '';
    private string $withDetail = '';
    private TranscodeStatusType $transcodeStatus;

    /**
     * Filters media based on title id.
     */
    public function withTitleId(string $titleId): self
    {
        if ($this->npCommId) {
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
    public function withCommunicationId(string $npCommId): self
    {
        if ($this->title) {
            throw new FilterException('Cannot filter by communcation id when a title id filter is already set.');
        }

        $this->npCommId = $npCommId;
        
        return $this;
    }

    /**
     * Filters media based on it's transcoding status.
     * 
     * Useful for filtering out any non-completed media.
     */
    public function withStatus(TranscodeStatusType $status): self
    {
        $this->transcodeStatus = $status;

        return $this;
    }

    /**
     * Gets the iterator and applies any filters.
     */
    public function getIterator(): \Iterator
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