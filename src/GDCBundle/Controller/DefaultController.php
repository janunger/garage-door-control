<?php

namespace GDCBundle\Controller;

use GDC\CommandQueue\Command;
use GDCBundle\Entity\CommandQueueEntry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function triggerAction()
    {
        $repository = $this->get('gdc.command_queue_entry_repository');
        $repository->save(new CommandQueueEntry(Command::TRIGGER_DOOR()));

        return new JsonResponse();
    }
}
