<?php

namespace App\Services;

use App\Repository\ArticlesRepository;
use App\Repository\UsersRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UploadService {

    private $params;
    private $userRepo;
    private $articleRepo;

    /**
     * Ont donne accès à getParameter
     * 
     * Ont y accède via -> $this->params->get
     *
     * @param ParameterBagInterface $params
     * 
     * Ont récupère aussi UserRepository
     */
    public function __construct(ParameterBagInterface $params, UsersRepository $userRepo, ArticlesRepository $articleRepo)
    {
        $this->params = $params;
        $this->userRepo = $userRepo;
        $this->articleRepo = $articleRepo;
    }

    /**
     * Fonction permettant d'uploader un avatar
     *
     * @param [type] $fichier -> Ont doit donner la source en entrée
     * @param [type] $validExt -> Ont précise les extensions autorisés
     * @param [type] $directory -> Ont précise le répertoire de destination
     * @param [type] $user -> Ont donne l'utilisateur en question
     * @return void
     */
    public function sendAvatar($fichier, $validExt, $directory, $user) {

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

            /**
             * Si ont a déjà un avatar
             * On supprime l'ancien
             */
            if($user->getAvatar()) {
                unlink($this->params->get($directory).'/'.$user->getAvatar());
            }

            // Ont copie ensuite l'avatar
            $fichier->move(
                $this->params->get($directory),
                $nom
            );

            // Ont sauvegarde l'avatar en BDD
            $user->setAvatar($nom);
            $this->userRepo->add($user, true);

            // Ont initialise la statut success
            $result = [
                'statut' => 'success',
            ];
        }

        /**
         * Si le format n'est pas valid
         * Ont initialise le statut error
         */
        else {
            $result = [
                'statut' => 'error'
            ];
        }
        // Et ont retourne la réponse
        return $result;
    }

    public function sendimgBlog($fichier, $validExt, $directory, $article) {

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

            /**
             * Si ont a déjà un avatar
             * On supprime l'ancien
             */
            if($article->getImg()) {
                unlink($this->params->get($directory).'/'.$article->getImg());
            }

            // Ont copie ensuite l'avatar
            $fichier->move(
                $this->params->get($directory),
                $nom
            );

            // Ont sauvegarde l'avatar en BDD
            $article->setimg($nom);
            $this->articleRepo->save($article, true);

            // Ont initialise la statut success
            $result = [
                'statut' => 'success',
            ];
        }

        /**
         * Si le format n'est pas valid
         * Ont initialise le statut error
         */
        else {
            $result = [
                'statut' => 'error'
            ];
        }
        // Et ont retourne la réponse
        return $result;
    }

    /**
     * Fonction permettant d'uploader des fichiers
     *
     * @param [type] $fichiers -> Ont doit donner la ou les sources en entrée
     * @param [type] $valideExt -> Ont précise les extensions autorisés
     * @param [type] $directory -> Ont précise le répertoire de destination
     * @return void
     */
    public function send($fichiers, $valideExt, $directory, $avatar = "") {

        // Ont initialise le nombre de fichier à zéro
        $nombre = 0;
        // Ont initialise le nombre d'erreur à zéro
        $errorFormat = 0;
        


        // Ont vérifie que le format est valide
        foreach ($fichiers as $fichier) {
            if (!in_array($fichier->getMimetype(), $valideExt)) {
                // Si le format est invalide ont incrémente le compteur d'erreur
                $errorFormat++;
            }
            // Ont incrémente le nombre de fichier
            $nombre++;
        }

        // Si nous n'avons pas d'erreur
        if ($errorFormat === 0) {

            /**
             * Si le compteur de fichier est à zéro
             * Nous n'acceptons pas l'upload multiple
             */
            if ($nombre === 0) {
                
                // Ont génère un nom de fichier unique
                $nom = md5(uniqid()) . '.' . $fichiers->guessExtension();
                $fichiers->move($this->params->get($directory), $nom);
                //unlink($this->params->get($directory).'/'.$nom);
                $arr = ['statut => success,'];

        
                                
            }
            /**
             * Si nous avons plusieurs fichiers
             * Nous sommes donc sur de l'upload multiple
             * Ont boucle sur ces fichiers
             * Ont crée un nom de fichier unique pour chacun d'eux
             * Et ont upload dans le dossier précisé par $directory
             */
            else {
                //$arr .= "statut => success,";
                foreach ($fichiers as $fichier) {
                    $nom = md5(uniqid()) . '.' . $fichier->guessExtension();
                    $fichier->move($this->params->get($directory), $nom);

                    //$arr[] = $nom;
                    //$arr['cities'][] = $nom;
                    //$arr = ['statut => success,'];
                }
            }
        }

        

        //$result = json_encode($arr);
        // Ont retourne le résultat à la vue
        return $nom;

    }
}