<?php

namespace App\Controller\admin;

use App\Repository\CategorieRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\PlaylistType;
use App\Entity\Playlist;
use App\Repository\FormationRepository;

/**
 * Description of AdminPlaylistsController
 *
 * @author hugoc
 */
class AdminPlaylistsController extends AbstractController{
    /**
     *
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
    /**
     *
     * @var CategorieRepository
     */
    private $categorieRepository;

    /**
     *
     * @var FormationRepository
     */
    private $formationRepository;

    private const VIEW = "admin/admin.playlists.html.twig";
    
    public function __construct(PlaylistRepository $playlistRepository, CategorieRepository $categorieRepository, FormationRepository $formationRespository) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository= $categorieRepository;
        $this->formationRepository = $formationRespository;
    }
    
    #[Route('/admin/playlists', name: 'admin.playlists')]
    public function index(): Response{
        $playlists = $this->playlistRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::VIEW, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    #[Route('/admin/playlists/suppr/{id}', name: 'admin.playlists.suppr')]
    public function suppr($id): Response{
        $playlist = $this->playlistRepository->find($id);
        if ($playlist->getNbFormations() > 0) {
            $this->addFlash(
                'danger',
                'Impossible de supprimer cette playlist : elle est associée à une ou plusieurs formations.'
            );

            return $this->redirectToRoute('admin.playlists');
        }
        $this->playlistRepository->remove($playlist);
        return $this->redirectToRoute('admin.playlists');

    }

    #[Route('/admin/playlists/edit/{id}', name: 'admin.playlists.edit')]
    public function edit($id, Request $request): Response{
        $playlist = $this->playlistRepository->find($id);
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist );
        $formPlaylist->handleRequest($request);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        if($formPlaylist->isSubmitted() && $formPlaylist->isValid()){
            $this->playlistRepository->add($playlist);
            return $this->redirectToRoute('admin.playlists');
        }
        return $this->render("admin/admin.playlist.edit.html.twig", [
            'playlist' => $playlist,
            'formplaylist' => $formPlaylist->createView(),
            'playlistformations' => $playlistFormations
        ]);
    }
    #[Route('/admin/playlists/ajout', name: 'admin.playlists.ajout')]
    public function ajout(Request $request): Response{
        $playlist = new Playlist();
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist );
        $formPlaylist->handleRequest($request);
        if($formPlaylist->isSubmitted() && $formPlaylist->isValid()){
            $this->playlistRepository->add($playlist);
            return $this->redirectToRoute('admin.playlists');
        }
        return $this->render("admin/admin.playlist.ajout.html.twig", [
            'playlist' => $playlist,
            'formplaylist' => $formPlaylist->createView()
        ]);
    }
}
