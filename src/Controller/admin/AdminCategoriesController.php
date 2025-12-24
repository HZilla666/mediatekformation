<?php

namespace App\Controller\admin;

use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categorie;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

/**
 * Description of AdminCategoriesController
 *
 * @author hugoc
 */
class AdminCategoriesController extends AbstractController{
    /**
     *
     * @var CategorieRepository
     */
    private $categorieRepository;

    private const VIEW = "admin/admin.categories.html.twig";
    
    public function __construct(CategorieRepository $categorieRepository) {
        $this->categorieRepository= $categorieRepository;
    }
    
    #[Route('/admin/categories', name: 'admin.categories')]
    public function index(): Response{
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::VIEW, [
            'categories' => $categories
        ]);
    }

    #[Route('/admin/categories/suppr/{id}', name: 'admin.categories.suppr')]
    public function suppr($id): Response{
        $categorie = $this->categorieRepository->find($id);
        if ($categorie->getFormations()->count() > 0) {
            $this->addFlash(
                'danger',
                'Impossible de supprimer cette catégorie : elle est associée à une ou plusieurs formations.'
            );

            return $this->redirectToRoute('admin.categories');
        }
        $this->categorieRepository->remove($categorie);
        $this->addFlash('success', 'Catégorie supprimée avec succès.');
        return $this->redirectToRoute('admin.categories');
    }

    
    #[Route('/admin/categories/ajout', name: 'admin.categories.ajout', methods: ['POST'])]
    public function ajout(Request $request): Response
    {
        $nomCategorie = trim($request->request->get('nom'));

        if ($nomCategorie === '') {
            $this->addFlash('danger', 'Le nom de la catégorie est obligatoire.');
            return $this->redirectToRoute('admin.categories');
        }

        $categorie = new Categorie();
        $categorie->setName($nomCategorie);

        try {
            $this->categorieRepository->add($categorie);
            $this->addFlash('success', 'Catégorie ajoutée avec succès.');
        } catch (UniqueConstraintViolationException $e) {
            $this->addFlash('danger', 'Cette catégorie existe déjà.');
        }

        return $this->redirectToRoute('admin.categories');
    }
}
