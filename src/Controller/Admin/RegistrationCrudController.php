<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\MoneyField;
use App\DBAL\Types\LicenseAgeCategoryType;
use App\DBAL\Types\LicenseType;
use App\Entity\Applicant;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RegistrationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Applicant::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Inscription')
            ->setEntityLabelInPlural('Inscriptions')
            ->setPaginatorPageSize(200)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm()
                ->setTemplatePath('admin/crud/fields/id.html.twig'),
            TextField::new('completeName', 'Nom'),
            BooleanField::new('docsRetrieved', 'Documents en ordre'),
            BooleanField::new('paid', 'Payé'),
            BooleanField::new('licenseCreated', 'License faite'),
            BooleanField::new('tournament', 'Veut faire compétition')->renderAsSwitch(false),
            TextField::new('realLicenseType', 'Type de licence')->formatValue(function (?string $value) {
                return null === $value ? null : LicenseType::getReadableValue($value);
            })->hideOnForm(),
            ChoiceField::new('ageCategory', "Catégorie d'âge")->setChoices(
                LicenseAgeCategoryType::getChoices(),
            )->hideOnForm(),
            MoneyField::new('toPay', 'À payer')->onlyOnIndex(),
            TextField::new('paymentObservations', 'Observations'),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('renewal')
            ->add('season')
            ->add('docsRetrieved')
            ->add('paid')
            ->add('licenseCreated')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
        ;
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters,
    ): QueryBuilder {
        $queryBuilder = parent::createIndexQueryBuilder(
            $searchDto,
            $entityDto,
            $fields,
            $filters,
        );

        // if user defined sort is not set
        if (0 === count($searchDto->getSort())) {
            $queryBuilder
                ->addSelect(
                    'CONCAT(entity.lastname, \' \', entity.firstname) AS HIDDEN completeName',
                )
                ->addOrderBy('completeName', 'ASC')
            ;
        }

        return $queryBuilder;
    }
}
