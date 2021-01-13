<?php
namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation;
use InvalidArgumentException;
use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Factory\UsersFactory;

class UsersSearchIterator extends AbstractApiIterator
{
    /**
     * The search query.
     *
     * @var string
     */
    protected $query;

    /**
     * The language to search with.
     *
     * @var string
     */
    protected $languageCode;

    /**
     * The country code.
     *
     * @var string
     */
    protected $countryCode;

    /**
     * The users factory.
     *
     * @var UsersFactory
     */
    private $usersFactory;
    
    public function __construct(UsersFactory $usersFactory, string $query, string $languageCode = 'en', string $countryCode = 'us')
    {
        if (empty($query))
        {
            throw new InvalidArgumentException('[query] must contain a value.');
        }

        parent::__construct($usersFactory->getHttpClient());
        $this->usersFactory = $usersFactory;
        $this->query = $query;
        $this->languageCode = $languageCode;
        $this->countryCode = $countryCode;
        $this->access('');
    }

    public function access($cursor) : void
    {
        $results = $this->postJson('search/v1/universalSearch', [
            'age' => '69',
            'countryCode' => $this->countryCode,
            'domainRequests' => [
                [
                    'domain' => 'ConceptGameMobileApp',
                    'pagination' => [
                        'cursor' => $cursor,
                        'pageSize' => '20' // @TODO: Test if this can be altered.
                    ]
                ]
            ],
            'languageCode' => $this->languageCode,
            'searchTerm' => $this->query
		]);
		
        $this->update($results->domainResponses[0]->totalResultCount, $results->domainResponses[0]->results, $results->domainResponses[0]->next);
	}
	
	public function next() : void
    {
        $this->currentOffset++;
        if (($this->currentOffset % $this->limit) == 0)
        {
            $this->access($this->maxEventIndexCursor);
        }
    }

    public function current()
    {
        $socialMetadata = $this->getFromOffset($this->currentOffset)->socialMetadata;
        
        return new User(
            $this->usersFactory, 
            $socialMetadata->accountId,
            $socialMetadata->country
        );
    }
}
