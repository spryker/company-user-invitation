<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyUserInvitation\Business\Model\PostRegistration;

use Generated\Shared\Transfer\CompanyUserInvitationTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationUpdateStatusRequestTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Shared\CompanyUserInvitation\CompanyUserInvitationConfig;
use Spryker\Zed\CompanyUserInvitation\Business\Model\Updater\InvitationUpdaterInterface;
use Spryker\Zed\CompanyUserInvitation\Dependency\Facade\CompanyUserInvitationToCompanyUserFacadeInterface;
use Spryker\Zed\CompanyUserInvitation\Persistence\CompanyUserInvitationRepositoryInterface;

class CompanyUserCreator implements CompanyUserCreatorInterface
{
    /**
     * @var \Spryker\Zed\CompanyUserInvitation\Persistence\CompanyUserInvitationRepositoryInterface
     */
    protected $repository;

    /**
     * @var \Spryker\Zed\CompanyUserInvitation\Dependency\Facade\CompanyUserInvitationToCompanyUserFacadeInterface
     */
    protected $companyUserFacade;

    /**
     * @var \Spryker\Zed\CompanyUserInvitation\Business\Model\Updater\InvitationUpdaterInterface
     */
    protected $invitationUpdater;

    public function __construct(
        CompanyUserInvitationRepositoryInterface $repository,
        CompanyUserInvitationToCompanyUserFacadeInterface $companyUserFacade,
        InvitationUpdaterInterface $invitationUpdater
    ) {
        $this->repository = $repository;
        $this->companyUserFacade = $companyUserFacade;
        $this->invitationUpdater = $invitationUpdater;
    }

    public function create(CustomerTransfer $customerTransfer): void
    {
        $companyUserInvitationTransfer = $this->getCompanyUserInvitationTransfer($customerTransfer);

        if (!$this->isValidCompanyUserInvitationStatus($companyUserInvitationTransfer)) {
            return;
        }

        $companyUserTransfer = (new CompanyUserTransfer())
            ->setFkCustomer($customerTransfer->getIdCustomer())
            ->setFkCompany($companyUserInvitationTransfer->getCompanyId())
            ->setFkCompanyBusinessUnit($companyUserInvitationTransfer->getFkCompanyBusinessUnit())
            ->setCustomer($customerTransfer);

        $companyUserResponseTransfer = $this->companyUserFacade->update($companyUserTransfer);
        if ($companyUserResponseTransfer->getIsSuccessful()) {
            $customerTransfer->setCompanyUserTransfer($companyUserResponseTransfer->getCompanyUser());
            $companyUserInvitationUpdateStatusRequestTransfer = $this->getCompanyUserInvitationUpdateStatusRequestTransfer(
                $companyUserInvitationTransfer,
            );
            $this->invitationUpdater->updateStatus($companyUserInvitationUpdateStatusRequestTransfer);
        }
    }

    protected function getCompanyUserInvitationUpdateStatusRequestTransfer(
        CompanyUserInvitationTransfer $companyUserInvitationTransfer
    ): CompanyUserInvitationUpdateStatusRequestTransfer {
        return (new CompanyUserInvitationUpdateStatusRequestTransfer())
        ->setIdCompanyUser($companyUserInvitationTransfer->getFkCompanyUser())
        ->setCompanyUserInvitation($companyUserInvitationTransfer)
        ->setStatusKey(CompanyUserInvitationConfig::INVITATION_STATUS_ACCEPTED);
    }

    protected function getCompanyUserInvitationTransfer(CustomerTransfer $customerTransfer): CompanyUserInvitationTransfer
    {
        $companyUserInvitationTransfer = (new CompanyUserInvitationTransfer())
            ->setHash($customerTransfer->getCompanyUserInvitationHash());

        return $this->repository->getCompanyUserInvitationByHash($companyUserInvitationTransfer);
    }

    protected function isValidCompanyUserInvitationStatus(CompanyUserInvitationTransfer $companyUserInvitationTransfer): bool
    {
        return $companyUserInvitationTransfer->getIdCompanyUserInvitation()
            && $companyUserInvitationTransfer->getCompanyUserInvitationStatusKey() === CompanyUserInvitationConfig::INVITATION_STATUS_PENDING;
    }
}
