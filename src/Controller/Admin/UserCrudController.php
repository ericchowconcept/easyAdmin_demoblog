<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{

    public function __construct(public UserPasswordHasherInterface $hasher)
    {
        
    }
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

 
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('email'),
            TextField::new('firstname', 'Prénom'),
            TextField::new('lastname', 'Nom'),
            TextField::new('username', 'Pseudo'),
            TextField::new('password', 'mot de passe')->setFormType(PasswordType::class)->onlyWhenCreating(),
            CollectionField::new('roles')->setTemplatePath('admin/field/roles.html.twig'),
           
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // *on veut pouvoir hasher le password à la création car on cahce le password à la modification
        // *on verifie qu'il y ait pas d'ID dans l'objet actuellement lié au formulaire 
        // *donc on est en création
        if(!$entityInstance->getId())
        {
            // *on set le password dans l'objet lié au formulaire et d'abord on le hash
            $entityInstance->setPassword($this->hasher->hashPassword(
                $entityInstance, $entityInstance->getPassword()
            ));
        }
        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }
    
}
