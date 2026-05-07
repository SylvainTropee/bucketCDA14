<?php

namespace App\Controller;

use App\Form\Model\SearchEvent;
use App\Form\SearchEventType;
use App\Services\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class EventController extends AbstractController
{


    #[Route('/events', name: 'event_list')]
    public function list(EventService $eventService, Request $request): Response
    {
        $searchEvent = new SearchEvent();
        $searchEventForm = $this->createForm(SearchEventType::class, $searchEvent);
        $searchEventForm->handleRequest($request);

        $searchEvent->setStartDate(new \DateTime());

        $events = $eventService->getEvents($searchEvent);

        return $this->render('event/list.html.twig', [
            'events' => $events,
            'searchEventForm' => $searchEventForm
        ]);
    }

}





