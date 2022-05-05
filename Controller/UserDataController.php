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

        if ($content['url']) {
            $ct = substr($content['url'], 4);

            try {
                $clickthrough = ClickthroughHelper::decodeArrayFromUrl($ct);
                $leadId = $clickthrough['lead'];
            } catch (\Exception $e) {
                return new JsonResponse(['error' => $e]);
            }
        } elseif ($content['cookie']) {
            $leadId = hexdec(base64_decode($content['cookie']));
        }

        $lead = null;
        if (isset($leadId) && (int)$leadId) {
            $lead = $this->em->getRepository('MauticLeadBundle:Lead')->getEntity($leadId);
        }

        if (!$lead) {
            return new JsonResponse(['error' => 'No lead was found by requested ID']);
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

        return new JsonResponse(['html' => $html, 'ct' => base64_encode(dechex($leadId))]);
    }
}
