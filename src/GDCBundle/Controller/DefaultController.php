<?php

namespace GDCBundle\Controller;

use GDC\Camera;
use GDC\CommandQueue\Command;
use GDC\Door\Door;
use GDCBundle\Entity\CommandQueueEntry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Template()
     */
    public function indexAction()
    {
        return [];
    }

    public function doorStateAction()
    {
        $doorState = $this->get('gdc.door_state_repository')->find(1);

        return new JsonResponse([
            'doorState' => $doorState->getState()->getValue(),
            'date' => $doorState->getDate()->format(DATE_ISO8601),
            'ageOfStateSeconds' => time() - (int)$doorState->getDate()->format('U')
        ]);
    }

    public function triggerAction()
    {
        $repository = $this->get('gdc.command_queue_entry_repository');
        $repository->save(new CommandQueueEntry(Command::TRIGGER_DOOR()));

        return new JsonResponse();
    }

    public function snapshotAction()
    {
        $camera = $this->get('gdc.camera');

        return new Response($camera->getSnapshot(), 200, array('Content-Type' => 'image/jpeg'));
    }
}
