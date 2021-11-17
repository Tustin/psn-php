<?php
namespace Tustin\PlayStation\Factory;

use Iterator;
use IteratorAggregate;
use Tustin\PlayStation\Api;
use Tustin\PlayStation\Model\Media;
use Tustin\PlayStation\Enum\TranscodeStatusType;
use Tustin\PlayStation\Exception\FilterException;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Iterator\CloudMediaGalleryIterator;
use Tustin\PlayStation\Iterator\Filter\TrophyTitle\TitleIdFilter;

class CloudMediaGalleryFactory extends Api implements IteratorAggregate, FactoryInterface
{
    private $title;

    private string $titleId = '';
    private string $npCommId = '';
    private string $withDetail = '';
    private TranscodeStatusType $transcodeStatus;

    /**
     * Filters media based on title id.
     *
     * @param string $titleId PPSAxxxxx_00
     * @return CloudMediaGalleryFactory
     * @throws FilterException
     */
    public function withTitleId(string $titleId): CloudMediaGalleryFactory
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
     *
     * @param string $npCommId NPWRxxxxx_00
     * @return CloudMediaGalleryFactory
     * @throws FilterException
     */
    public function withCommunicationId(string $npCommId): CloudMediaGalleryFactory
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
     *
     * @param TranscodeStatusType $status
     * @return CloudMediaGalleryFactory
     */
    public function withStatus(TranscodeStatusType $status): CloudMediaGalleryFactory
    {
        $this->transcodeStatus = $status;

        return $this;
    }

    /**
     * Gets the iterator and applies any filters.
     *
     * @return Iterator
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
     *
     * @return Media
     */
    public function first(): Media
    {
        return $this->getIterator()->current();
    }
}