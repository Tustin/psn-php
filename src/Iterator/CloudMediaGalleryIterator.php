<?php
namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Model\Media;
use Tustin\PlayStation\Factory\CloudMediaGalleryFactory;

class CloudMediaGalleryIterator extends AbstractApiIterator
{
    private CloudMediaGalleryFactory $cloudMediaGalleryFactory;

    public function __construct(CloudMediaGalleryFactory $cloudMediaGalleryFactory)
    {
        parent::__construct($cloudMediaGalleryFactory->getHttpClient());

        $this->cloudMediaGalleryFactory = $cloudMediaGalleryFactory;

        $this->limit = 20;

        $this->access(0);
    }

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

    public function current(): object
    {
        return Media::fromObject(
            $this->cloudMediaGalleryFactory->getHttpClient(),
            $this->getFromOffset($this->currentOffset)
        );
    }
}
