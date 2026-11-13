<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyUserInvitation\Persistence;

use Generated\Shared\Transfer\CompanyUserInvitationCollectionTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationCriteriaFilterTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationStatusTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationTransfer;
use Generated\Shared\Transfer\FilterTransfer;
use Generated\Shared\Transfer\PaginationTransfer;
use Orm\Zed\CompanyBusinessUnit\Persistence\Map\SpyCompanyBusinessUnitTableMap;
use Orm\Zed\CompanyUser\Persistence\Map\SpyCompanyUserTableMap;
use Orm\Zed\CompanyUserInvitation\Persistence\Map\SpyCompanyUserInvitationTableMap;
use Orm\Zed\CompanyUserInvitation\Persistence\SpyCompanyUserInvitationQuery;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Util\PropelModelPager;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;
use Spryker\Zed\PropelOrm\Business\Runtime\ActiveQuery\Criteria;

/**
 * @method \Spryker\Zed\CompanyUserInvitation\Persistence\CompanyUserInvitationPersistenceFactory getFactory()
 */
class CompanyUserInvitationRepository extends AbstractRepository implements CompanyUserInvitationRepositoryInterface
{
    public function getCompanyUserInvitationCollection(
        CompanyUserInvitationCriteriaFilterTransfer $companyUserInvitationCriteriaFilterTransfer
    ): CompanyUserInvitationCollectionTransfer {
        $queryCompanyUserInvitation = $this->getFactory()
            ->createCompanyUserInvitationQuery()
            ->joinWithSpyCompanyBusinessUnit()
            ->joinWithSpyCompanyUserInvitationStatus();

        $queryCompanyUserInvitation = $this->applyQueryFilters($queryCompanyUserInvitation, $companyUserInvitationCriteriaFilterTransfer);

        if ($companyUserInvitationCriteriaFilterTransfer->getFilter() !== null) {
            $queryCompanyUserInvitation = $this->buildQueryFromCriteria(
                $queryCompanyUserInvitation,
                $companyUserInvitationCriteriaFilterTransfer->getFilter(),
            );
        }

        if ($companyUserInvitationCriteriaFilterTransfer->getPagination() === null) {
            return $this->getFactory()
            ->createCompanyUserInvitationMapper()
            ->mapCompanyUserInvitationCollection($queryCompanyUserInvitation->find());
        }

        $pager = $queryCompanyUserInvitation->paginate(
            $companyUserInvitationCriteriaFilterTransfer->getPagination()->requirePage()->getPage(),
            $companyUserInvitationCriteriaFilterTransfer->getPagination()->requireMaxPerPage()->getMaxPerPage(),
        );

        $companyUserInvitationCollectionTransfer = $this->getFactory()
            ->createCompanyUserInvitationMapper()
            ->mapCompanyUserInvitationCollection($pager->getResults());

        $companyUserInvitationCollectionTransfer->setPagination(
            $this->hydratePaginationTransfer($companyUserInvitationCriteriaFilterTransfer->getPagination(), $pager),
        );

        return $companyUserInvitationCollectionTransfer;
    }

    public function findCompanyUserInvitationStatusByStatusKey(string $statusKey): ?CompanyUserInvitationStatusTransfer
    {
        $spyCompanyUserInvitation = $this->getFactory()
            ->createCompanyUserInvitationStatusQuery()
            ->filterByStatusKey($statusKey)
            ->findOne();

        if ($spyCompanyUserInvitation !== null) {
            $companyUserInvitationStatusTransfer = new CompanyUserInvitationStatusTransfer();
            $companyUserInvitationStatusTransfer->fromArray($spyCompanyUserInvitation->toArray(), true);

            return $companyUserInvitationStatusTransfer;
        }

        return null;
    }

    /**
     * @module CompanyBusinessUnit
     * @module CompanyUser
     */
    public function findCompanyUserInvitationByIdAndIdCompanyUser(
        CompanyUserInvitationTransfer $companyUserInvitationTransfer,
        int $idCompanyUser
    ): ?CompanyUserInvitationTransfer {
        $spyCompanyUserInvitation = $this->getFactory()
            ->createCompanyUserInvitationQuery()
            ->filterByIdCompanyUserInvitation($companyUserInvitationTransfer->getIdCompanyUserInvitation())
            ->joinSpyCompanyBusinessUnit()
            ->addJoin(SpyCompanyBusinessUnitTableMap::COL_FK_COMPANY, SpyCompanyUserTableMap::COL_FK_COMPANY, Criteria::INNER_JOIN)
            ->add(SpyCompanyUserTableMap::COL_ID_COMPANY_USER, $idCompanyUser)
            ->findOne();

        if ($spyCompanyUserInvitation === null) {
            return null;
        }

        return $this->getFactory()
            ->createCompanyUserInvitationMapper()
            ->mapSpyCompanyUserInvitationToCompanyUserInvitationTransfer($spyCompanyUserInvitation);
    }

    /**
     * @param list<string> $emails
     *
     * @return list<string>
     */
    public function getCompanyUserInvitationEmailsByEmails(array $emails): array
    {
        if (!$emails) {
            return [];
        }

        /** @var list<string> */
        return $this->getFactory()
            ->createCompanyUserInvitationQuery()
            ->addUsingAlias(SpyCompanyUserInvitationTableMap::COL_EMAIL, $emails, Criteria::IN)
            ->select(SpyCompanyUserInvitationTableMap::COL_EMAIL)
            ->find()
            ->getData();
    }

    public function getCompanyUserInvitationByHash(
        CompanyUserInvitationTransfer $companyUserInvitationTransfer
    ): CompanyUserInvitationTransfer {
        $spyCompanyUserInvitation = $this->getFactory()
            ->createCompanyUserInvitationQuery()
            ->joinWithSpyCompanyBusinessUnit()
            ->joinWithSpyCompanyUserInvitationStatus()
            ->filterByHash($companyUserInvitationTransfer->getHash())
            ->findOne();

        if ($spyCompanyUserInvitation == null) {
            return $companyUserInvitationTransfer;
        }

        return $this->getFactory()
            ->createCompanyUserInvitationMapper()
            ->mapSpyCompanyUserInvitationToCompanyUserInvitationTransfer($spyCompanyUserInvitation);
    }

    protected function applyQueryFilters(
        SpyCompanyUserInvitationQuery $queryCompanyUserInvitation,
        CompanyUserInvitationCriteriaFilterTransfer $companyUserInvitationCriteriaFilterTransfer
    ): SpyCompanyUserInvitationQuery {
        if ($companyUserInvitationCriteriaFilterTransfer->getFkCompany()) {
            $queryCompanyUserInvitation->useSpyCompanyUserQuery()->filterByFkCompany(
                $companyUserInvitationCriteriaFilterTransfer->getFkCompany(),
                Criteria::IN,
            )->endUse();
        }

        if ($companyUserInvitationCriteriaFilterTransfer->getCompanyUserInvitationStatusKeyIn()) {
            $queryCompanyUserInvitation
                ->useSpyCompanyUserInvitationStatusQuery()
                    ->filterByStatusKey_In($companyUserInvitationCriteriaFilterTransfer->getCompanyUserInvitationStatusKeyIn())
                ->endUse();
        }

        if ($companyUserInvitationCriteriaFilterTransfer->getCompanyUserInvitationStatusKeyNotIn()) {
            $queryCompanyUserInvitation
                ->useSpyCompanyUserInvitationStatusQuery()
                    ->filterByStatusKey($companyUserInvitationCriteriaFilterTransfer->getCompanyUserInvitationStatusKeyNotIn(), Criteria::NOT_IN)
                ->endUse();
        }

        return $queryCompanyUserInvitation;
    }

    public function buildQueryFromCriteria(ModelCriteria $modelCriteria, ?FilterTransfer $filterTransfer = null): ModelCriteria
    {
        $modelCriteria = parent::buildQueryFromCriteria($modelCriteria, $filterTransfer);

        $modelCriteria->setFormatter(ModelCriteria::FORMAT_OBJECT);

        return $modelCriteria;
    }

    protected function hydratePaginationTransfer(
        PaginationTransfer $paginationTransfer,
        PropelModelPager $paginationModel
    ): PaginationTransfer {
        return $paginationTransfer
            ->setNbResults($paginationModel->getNbResults())
            ->setFirstIndex($paginationModel->getFirstIndex())
            ->setLastIndex($paginationModel->getLastIndex())
            ->setFirstPage($paginationModel->getFirstPage())
            ->setLastPage($paginationModel->getLastPage())
            ->setNextPage($paginationModel->getNextPage())
            ->setPreviousPage($paginationModel->getPreviousPage());
    }
}
