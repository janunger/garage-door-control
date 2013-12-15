<?php

namespace GDC\CoreBundle\Controller;

use GDC\Door;
use Guzzle\Http\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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

    public function snapshotAction()
    {
        $client = new Client('http://192.168.73.229');
        $request = $client->get('/snapshot.cgi')->setAuth('visitor', 'lvp3k4XiPcnV');
        $response = $request->send();

        return new Response($response->getBody(true), 200, array('Content-Type' => 'image/jpeg'));
    }
}
