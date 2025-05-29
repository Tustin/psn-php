<?php

namespace Tustin\PlayStation\Iterators;

use Tustin\PlayStation\Models\Media;
use Tustin\PlayStation\Factories\CloudMediaGallery;

class CloudMediaGalleryIterator extends AbstractApiIterator
{
    public function __construct(private CloudMediaGallery $CloudMediaGallery, protected ?int $limit = 20)
    {
        parent::__construct();

        $this->access(0);
    }

    /**
     * Accesses a new page of results.
     */
    public function access($cursor): void
    {
        $body = [
            'includeTokenizedUrls' => 'true', // Doesn't change anything
            'limit' => $this->limit,
            // @TODO: Where does $cursor go?? Need more media to test this.
        ];

        $results = $this->get('gameMediaService/v2/c2s/category/cloudMediaGallery/ugcType/all', $body);

        $this->update($this->limit, $results->ugcDocument, $results->nextCursorMark);
    }

    /**
     * Gets the current media in the iterator.
     */
    public function current(): Media
    {
        return Media::fromObject(
            $this->getFromOffset($this->currentOffset)
        );
    }
}
