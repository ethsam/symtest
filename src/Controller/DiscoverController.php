<?php

namespace App\Controller;

use App\Entity\PostCustom;
use App\Repository\TagCustomRepository;
use App\Repository\CityRepository;
use App\Repository\PostCustomRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * @Route({"en":"/discover", "fr":"/decouvrir"})
 */
class DiscoverController extends AbstractController
{

    /**
     * @Route("/", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="discover_index")
     * @Route("/rss.xml", defaults={"page": "1", "_format"="xml"}, methods={"GET"}, name="discover_rss")
     * @Route("/page/{page<[1-9]\d*>}", defaults={"_format"="html"}, methods={"GET"}, name="discover_index_paginated")
     * @Cache(smaxage="10")
     */
    public function index(Request $request, int $page, string $_format, PostCustomRepository $posts, TagCustomRepository $tags, CityRepository $cities): Response
    {
        $tag = null;
        if ($request->query->has('tag')) {
            $tag = $tags->findOneBy(['name' => $request->query->get('tag')]);
        }

        $city = null;
        if ($request->query->has('city')) {
            $city = $cities->findOneBy(['name' => $request->query->get('city')]);
        }

        $latestPosts = $posts->findLatest($page, $tag);

        return $this->render('discover/index.' . $_format . '.twig', [
            'paginator' => $latestPosts,
            'menucontent' => $tags->findAll(),
            'cities' => $cities->findAll(),
        ]);
    }

    /**
     * @Route("/{slug}", methods={"GET"}, name="discover_post")
     */
    public function postShow(PostCustom $post): Response
    {
        return $this->render('discover/post_show.html.twig', ['post' => $post]);
    }
}