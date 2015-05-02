<?php

namespace GDCBundle\Controller;

use GDC\Camera;
use GDC\CommandQueue\Command;
use GDCBundle\Entity\CommandQueueEntry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
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
