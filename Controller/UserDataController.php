<?php

namespace MauticPlugin\CustomPopUpFieldsBundle\Controller;

use Doctrine\ORM\EntityManager;
use Mautic\ApiBundle\Controller\CommonApiController;
use Mautic\CoreBundle\Helper\ClickthroughHelper;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserDataController extends CommonApiController
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return JsonResponse
     */
    public function sendAction()
    {
        $content = json_decode($this->request->getContent(), true);
        preg_match_all('#{\S.*?}#', $content['html'], $matches);

        if (count($matches[0]) == 0) {
            return new JsonResponse(['html' => $content['html']]);
        }

        $ct = substr($content['url'], 4);
        if ($leadId = (int)ClickthroughHelper::decodeArrayFromUrl($ct)['lead']) {
//            $leadId = 25242;
            $lead = $this->em->getRepository('MauticLeadBundle:Lead')->getEntity($leadId);

            if (!$lead) {
                return new JsonResponse(['error' => 'No lead was found by requested ID']);
            }
        }

        $slugs = [];
        foreach ($matches[0] as $slug) {
            $slug = 'get' . ucfirst(trim($slug, '{}'));
            try {
                $slugs[] = $lead->$slug();
            } catch (\Exception $e) {
                return new JsonResponse(['error' => "No field was found by slug '$slug'"]);
            }
        }

        $html = str_replace($matches[0], $slugs, $content['html']);

        return new JsonResponse(['html' => $html]);
    }
}
