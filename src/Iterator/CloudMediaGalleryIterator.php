<?php

namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Model\Media;
use Tustin\PlayStation\Factory\CloudMediaGalleryFactory;

class CloudMediaGalleryIterator extends AbstractApiIterator
{
    public function __construct(private CloudMediaGalleryFactory $cloudMediaGalleryFactory)
    {
        parent::__construct($cloudMediaGalleryFactory->getHttpClient());

        $this->limit = 20;

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
            $this->cloudMediaGalleryFactory->getHttpClient(),
            $this->getFromOffset($this->currentOffset)
        );
    }
}
