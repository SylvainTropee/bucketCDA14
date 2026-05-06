<?php

namespace App\Services;

use App\Form\Model\SearchEvent;
use App\Form\SearchEventType;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EventService
{
    private readonly string $BASE_URL;

    public function __construct(private HttpClientInterface $httpClient)
    {
        $this->BASE_URL = 'https://public.opendatasoft.com/api/records/1.0/search/?dataset=evenements-publics-openagenda';
    }

    public function getEvents(SearchEvent $searchEvent)
    {
        $url = $this->BASE_URL;

        if ($searchEvent->getCity()) {
            $url .= "&refine.location_city=" . $searchEvent->getCity();
        }
        if ($searchEvent->getStartDate()) {
            $url .= "&refine.firstdate_begin=" . $searchEvent->getStartDate()->format('Y-m-d');
        }

        $data = $this->httpClient->request('GET', $url)->toArray();

        return $data['records'];
    }

}
