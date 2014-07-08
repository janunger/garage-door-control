<?php

namespace GDC\CoreBundle\Controller;

use DateTime;
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

    public function triggerAction()
    {
        /** @var $door Door */
        $door = $this->get('gdc_core.door');
        $door->triggerControl();

        return $this->redirect($this->generateUrl('gdc_core_homepage'));
    }

    public function snapshotAction()
    {
        $camera = new Camera();

        return new Response($camera->getSnapshot(), 200, array('Content-Type' => 'image/jpeg'));
    }

    public function doorStateAction()
    {
        return new JsonResponse([
            'doorState' => $this->get('gdc_core.door')->getState()
        ]);
    }
}
