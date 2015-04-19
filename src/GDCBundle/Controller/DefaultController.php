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
        $door = $this->get('gdc.door');

        return new JsonResponse([
            'doorState' => $door->getState()
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