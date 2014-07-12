<?php

namespace GDC\CoreBundle\Controller;

use GDC\Camera;
use GDC\Door;
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
        return new JsonResponse([
            'doorState' => $this->get('gdc_core.door')->getState()
        ]);
    }

    public function triggerAction()
    {
        $door = $this->get('gdc_core.door');
        $door->triggerControl();

        return new JsonResponse();
    }

    public function snapshotAction()
    {
        $camera = $this->get('gdc_core.camera');

        return new Response($camera->getSnapshot(), 200, array('Content-Type' => 'image/jpeg'));
    }
}
