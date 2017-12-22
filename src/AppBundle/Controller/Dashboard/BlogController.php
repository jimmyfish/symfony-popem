<?php
/**
 * Created by PhpStorm.
 * User: afif
 * Date: 22/12/2017
 * Time: 13:49.
 */

namespace AppBundle\Controller\Dashboard;

use AppBundle\Entity\Category;
use AppBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends Controller
{
    public function blogAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $repository = $manager->getRepository(Post::class);

        $latest = $repository->findPaginationBlog();

        $limit = 3;
        $news = $repository->findLatestNews($limit);

        /**
         * @var \Knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $latest,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
        );

        return $this->render(
            'AppBundle:Client:blog/list.html.twig',
            [
            'pagination' => $pagination,
            'latest' => $news,
            ]
        );
    }

    public function detailBlogAction($slug)
    {
        $manager = $this->getDoctrine()->getManager();

        $repository = $manager->getRepository(Post::class);

        $data = $repository->findOneBy(['slug' => $slug]);

        $limited = 4;
        $related = $repository->findRelatedBlog($limited);

        $limit = 3;
        $news = $repository->findLatestNews($limit);

        return $this->render(
            'AppBundle:Client:articles/article.html.twig',
            [
            'data' => $data,
            'related' => $related,
            'latest' => $news,
            ]
        );
    }

    public function blogCategoryAction(Request $request, $category)
    {
        $manager = $this->getDoctrine()->getManager();

        $query = $manager->getRepository(Post::class);

        $data = $manager->getRepository(Category::class)->findOneBy(['nameCategory' => $category]);

        $post = $query->findCategoryBlog($data);

        $limit = 3;
        $news = $query->findLatestNews($limit);

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $post,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
        );

        return $this->render(
            'AppBundle:Client:blog/category.html.twig',
            [
            'pagination' => $pagination,
            'latest' => $news,
            ]
        );
    }
}
