<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\Articles;
use App\Entity\Categories;
use App\Entity\Commentaires;
use App\Form\Blog\Admin\CategoriesType;
use App\Form\Blog\Admin\ArticleType;
use App\Form\Blog\Admin\CommentType;
use App\Repository\ArticlesRepository;
use App\Repository\CategoriesRepository;
use App\Repository\CommentairesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
* @Route("/admin/blog")
*/
class BlogController extends AbstractController
{
    /**
     * @Route("/", name="app_admin_blog_index")
     */
    public function adminIndex(ArticlesRepository $articlesRepository): Response
    {
        return $this->render('blog/admin/index.html.twig', [
            'articles' => $articlesRepository->findBy([], ['date' => 'DESC']),
        ]);
    }

    /**
     * @Route("/new", name="app_admin_blog_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ArticlesRepository $articlesRepository): Response
    {
        $article = new Articles();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $article->setDate(new DateTime());
            $article->setAuteur($this->getUser());

            // Ont récupère la photo
            $fichier = $form->get('img')->getData();
            //Ont initialise les extensions autorisé
            $validExt = ['image/png', 'image/jpg', 'image/jpeg'];
            //Ont initialise le repertoire
            $directory = 'blog_directory';
            // Ont initialise le nombre d'erreur à zéro
            $errorFormat = 0;

            // Ont vérifie que le type de fichier est valide
            if (!in_array($fichier->getMimetype(), $validExt)) {
                $errorFormat++;
            }

            // Si le format de l'image est valide
            if ($errorFormat === 0) {
                // Ont crée un nom de fichier unique
                $nom = md5(uniqid()) . '.' . $fichier->guessExtension();
                
                
                // Ont copie ensuite l'avatar
                $fichier->move(
                    $this->getParameter($directory),
                    $nom
                );

                // Ont sauvegarde l'avatar en BDD
                $article->setImage($nom);
                $articlesRepository->add($article, true);
            }
            
            $this->addFlash(
                'success',
                "Votre article a bien été enregistré"
            );

            return $this->redirectToRoute('app_admin_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('blog/admin/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="app_admin_blog_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Articles $article, ArticlesRepository $articlesRepository): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ont récupère la photo
            $fichier = $form->get('img')->getData();
            //Ont initialise les extensions autorisé
            $validExt = ['image/png', 'image/jpg', 'image/jpeg'];
            //Ont initialise le repertoire
            $directory = 'blog_directory';
            // Ont initialise le nombre d'erreur à zéro
            $errorFormat = 0;

            if ($fichier) {
                // Ont vérifie que le type de fichier est valide
                if (!in_array($fichier->getMimetype(), $validExt)) {
                    $errorFormat++;
                }

                // Si le format de l'image est valide
                if ($errorFormat === 0) {
                    // Ont crée un nom de fichier unique
                    $nom = md5(uniqid()) . '.' . $fichier->guessExtension();

                    /**
                    * Si ont a déjà un avatar
                    * On supprime l'ancien
                    */
                    if($article->getImage()) {
                        unlink($this->getParameter($directory).'/'.$article->getImage());
                    }

                    // Ont copie ensuite l'avatar
                    $fichier->move(
                        $this->getParameter($directory),
                        $nom
                    );

                    // Ont sauvegarde l'avatar en BDD
                    $article->setImage($nom);
                    
                }
                
            }
            
            $articlesRepository->add($article, true);
            $this->addFlash(
                'success',
                "Votre article a bien été enregistré"
            );

            return $this->redirectToRoute('app_admin_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('blog/admin/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="app_admin_blog_delete", methods={"GET"})
     */
    public function delete(Articles $article, ArticlesRepository $articlesRepository): Response
    {
        $this->addFlash(
            'warning',
            "Votre article a bien été supprimé"
        );

        $articlesRepository->remove($article, true);

        return $this->redirectToRoute('app_admin_blog_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/categories", name="app_admin_categories_blog_index", methods={"GET"})
     */
    public function categoriesIndex(CategoriesRepository $categoriesRepository): Response
    {
        return $this->render('blog/admin/categories/index.html.twig', [
            'categories' => $categoriesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/categories/new", name="app_admin_categories_blog_new", methods={"GET", "POST"})
     */
    public function categoriesNew(Request $request, CategoriesRepository $categoriesRepository): Response
    {
        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoriesRepository->add($category, true);

            $this->addFlash(
                'success',
                "Votre catégorie a bien été enregistré"
            );

            return $this->redirectToRoute('app_admin_categories_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('blog/admin/categories/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/categories/{id}/edit", name="app_admin_categories_blog_edit", methods={"GET", "POST"})
     */
    public function CategoriesEdit(Request $request, Categories $category, CategoriesRepository $categoriesRepository): Response
    {
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoriesRepository->add($category, true);

            $this->addFlash(
                'success',
                "Votre catégorie a bien été enregistré"
            );

            return $this->redirectToRoute('app_admin_categories_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('blog/admin/categories/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/categories/delete/{id}", name="app_admin_categories_blog_delete", methods={"GET"})
     */
    public function CategoriesDelete(Request $request, Categories $category, CategoriesRepository $categoriesRepository): Response
    {
        $categoriesRepository->remove($category, true);

        $this->addFlash(
            'warning',
            "Votre catégorie a bien été supprimé"
        );

        return $this->redirectToRoute('app_admin_categories_blog_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/commentaires", name="app_admin_comments_blog_index", methods={"GET"})
     */
    public function commentsIndex(CommentairesRepository $commentairesRepository): Response
    {
        return $this->render('blog/admin/commentaires/index.html.twig', [
            'commentaires' => $commentairesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/commentaires/{id}/edit", name="app_admin_comments_blog_edit", methods={"GET", "POST"})
     */
    public function commentsEdit(Request $request, Commentaires $commentaire, CommentairesRepository $commentairesRepository): Response
    {
        $form = $this->createForm(CommentType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentairesRepository->add($commentaire, true);

            $this->addFlash(
                'success',
                "Le commentaire a bien été modifié"
            );

            return $this->redirectToRoute('app_admin_comments_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('blog/admin/commentaires/edit.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/commentaires/delete/{id}", name="app_admin_comments_blog_delete", methods={"GET"})
     */
    public function commentsDelete(Commentaires $commentaire, CommentairesRepository $commentairesRepository): Response
    {
            $commentairesRepository->remove($commentaire, true);

            $this->addFlash(
                'warning',
                "Le commentaire a bien été supprimé"
            );

        return $this->redirectToRoute('app_admin_comments_blog_index', [], Response::HTTP_SEE_OTHER);
    }

}
