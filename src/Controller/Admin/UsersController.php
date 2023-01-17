<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use App\Services\UploadService;
use App\Form\Users\Admin\UsersType;
use App\Repository\UsersRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/admin/clients")
 */
class UsersController extends AbstractController
{
    /**
     * @Route("/", name="app_admin_users_index", methods={"GET"})
     */
    public function index(UsersRepository $usersRepository): Response
    {
        return $this->render('profil/admin/index.html.twig', [
            'users' => $usersRepository->findAll(),
        ]);
    }

    /**
     * @Route("/nouveau", name="app_admin_users_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UsersRepository $usersRepository, UserPasswordHasherInterface $encoder): Response
    {
        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Ont initialise un mot de passe unique
            $password = md5(uniqid());
            // Ont crypte le mot de passe
            $user->setPassword($encoder->hashPassword($user, $password));
            // Ont ajoute le mot de passe à l'user
            $usersRepository->add($user, true);

            $this->addFlash(
                'success',
                "Fiche client ajouté avec succès"
            );

            return $this->redirectToRoute('app_admin_users_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profil/admin/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_admin_users_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Users $user, UsersRepository $usersRepository, UploadService $upload): Response
    {
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            // Ont récupère la photo de profil
            $fichier = $form->get('avatar')->getData();
            //Ont initialise les extensions autorisé
            $valideExt = ['image/png', 'image/jpg', 'image/jpeg'];
            //Ont initialise le repertoire
            $directory = 'avatar_directory';

            // Si nous avons des fichiers
            if ($fichier) {
                // Ont appel le service d'upload
                if ($upload->sendAvatar($fichier, $valideExt, $directory, $user)['statut'] === 'success') {

                    /**
                     * Si nous avons un statut success
                     * Ont initialise un message flash success
                     */
                    $this->addFlash(
                        'success',
                        "Fiche client modifié avec succès"
                    );

                }
                /**
                 * Si l'extension n'est pas autorisé
                 * Ont initialise un message flash danger
                 */
                else {
                    $this->addFlash(
                        'danger',
                        "Seul les images sont autorisés"
                    );
                }
            }
            /**
             * Si nous n'avons pas d'image
             * Ont fait un update de l'user
             */
            else {
                $usersRepository->add($user, true);

                $this->addFlash(
                    'success',
                    "Fiche client modifié avec succès"
                );
            }

            return $this->redirectToRoute('app_admin_users_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('profil/admin/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
        
    }

    /**
     * @Route("/delete/{id}", name="app_admin_users_delete", methods={"GET"})
     */
    public function delete(Request $request, Users $user, UsersRepository $usersRepository): Response
    {

        $this->addFlash(
            'warning',
            "Le client a bien été supprimé"
        );
            
        $usersRepository->remove($user, true);

        return $this->redirectToRoute('app_admin_users_index', [], Response::HTTP_SEE_OTHER);
    }
}
