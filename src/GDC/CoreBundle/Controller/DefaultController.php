<?php

namespace GDC\CoreBundle\Controller;

use GDC\Door;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Template()
     */
    public function indexAction()
    {
        /** @var $door Door */
        $door = $this->get('gdc_core.door');

        try {
            $doorState = $door->getState();
        } catch (Door\HardwareErrorException $e) {
            $doorState = 'error';
        }

        return array(
            'doorState' => $doorState
        );
    }

    public function triggerAction()
    {
        /** @var $door Door */
        $door = $this->get('gdc_core.door');
        $door->triggerControl();

        return $this->redirect($this->generateUrl('gdc_core_homepage'));
    }
}
