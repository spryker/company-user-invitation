<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyUserInvitation\Business\Model\Validator;

use Generated\Shared\Transfer\CompanyBusinessUnitCriteriaFilterTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationCollectionTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationTransfer;
use Spryker\Zed\CompanyUserInvitation\Dependency\Facade\CompanyUserInvitationToCompanyBusinessUnitFacadeInterface;
use Spryker\Zed\CompanyUserInvitation\Dependency\Facade\CompanyUserInvitationToCompanyUserFacadeInterface;
use Spryker\Zed\CompanyUserInvitation\Persistence\CompanyUserInvitationRepositoryInterface;

class InvitationValidator implements InvitationValidatorInterface
{
    protected const int FIRST_NAME_MAX_LENGTH = 100;

    protected const int LAST_NAME_MAX_LENGTH = 100;

    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @var array
     */
    protected $businessUnitNameCache = [];

    /**
     * @var array
     */
    protected $emailCache = [];

    /**
     * @var array<string, bool>
     */
    protected array $existingEmailCache = [];

    /**
     * @var \Spryker\Zed\CompanyUserInvitation\Persistence\CompanyUserInvitationRepositoryInterface
     */
    protected $repository;

    /**
     * @var \Spryker\Zed\CompanyUserInvitation\Dependency\Facade\CompanyUserInvitationToCompanyUserFacadeInterface
     */
    protected $companyUserFacade;

    /**
     * @var \Spryker\Zed\CompanyUserInvitation\Dependency\Facade\CompanyUserInvitationToCompanyBusinessUnitFacadeInterface
     */
    protected $companyBusinessUnitFacade;

    public function __construct(
        CompanyUserInvitationRepositoryInterface $repository,
        CompanyUserInvitationToCompanyUserFacadeInterface $companyUserFacade,
        CompanyUserInvitationToCompanyBusinessUnitFacadeInterface $companyBusinessUnitFacade
    ) {
        $this->repository = $repository;
        $this->companyUserFacade = $companyUserFacade;
        $this->companyBusinessUnitFacade = $companyBusinessUnitFacade;
    }

    public function isValidInvitation(CompanyUserInvitationTransfer $invitationTransfer): bool
    {
        return $this->isValidFirstName($invitationTransfer)
            && $this->isValidLastName($invitationTransfer)
            && $this->isValidBusinessUnit($invitationTransfer)
            && $this->isValidEmail($invitationTransfer);
    }

    public function preloadExistingEmails(CompanyUserInvitationCollectionTransfer $companyUserInvitationCollectionTransfer): void
    {
        $normalizedEmails = [];

        foreach ($companyUserInvitationCollectionTransfer->getCompanyUserInvitations() as $companyUserInvitationTransfer) {
            if ($companyUserInvitationTransfer->getEmail() === null) {
                continue;
            }

            $normalizedEmails[mb_strtolower($companyUserInvitationTransfer->getEmail())] = true;
        }

        $this->loadExistingEmails(array_keys($normalizedEmails));
    }

    public function getLastErrorMessage(): string
    {
        return $this->errorMessage;
    }

    protected function isValidFirstName(CompanyUserInvitationTransfer $invitationTransfer): bool
    {
        if (!trim($invitationTransfer->getFirstName())) {
            $this->errorMessage = sprintf('First name cannot be empty blank');

            return false;
        }

        if (mb_strlen($invitationTransfer->getFirstNameOrFail()) > static::FIRST_NAME_MAX_LENGTH) {
            $this->errorMessage = sprintf('First name cannot be longer than %d characters', static::FIRST_NAME_MAX_LENGTH);

            return false;
        }

        return true;
    }

    protected function isValidLastName(CompanyUserInvitationTransfer $invitationTransfer): bool
    {
        if (!trim($invitationTransfer->getLastName())) {
            $this->errorMessage = sprintf('Last name cannot be blank');

            return false;
        }

        if (mb_strlen($invitationTransfer->getLastNameOrFail()) > static::LAST_NAME_MAX_LENGTH) {
            $this->errorMessage = sprintf('Last name cannot be longer than %d characters', static::LAST_NAME_MAX_LENGTH);

            return false;
        }

        return true;
    }

    protected function isValidBusinessUnit(CompanyUserInvitationTransfer $invitationTransfer): bool
    {
        if (!$this->businessUnitNameCache) {
            $this->populateBusinessUnitCache($invitationTransfer);
        }

        if (
            !trim($invitationTransfer->getCompanyBusinessUnitName())
            || !in_array($invitationTransfer->getCompanyBusinessUnitName(), $this->businessUnitNameCache)
        ) {
            $this->errorMessage = sprintf('Business Unit "%s" is not valid', $invitationTransfer->getCompanyBusinessUnitName());

            return false;
        }

        return true;
    }

    protected function isValidEmail(CompanyUserInvitationTransfer $invitationTransfer): bool
    {
        if (!filter_var($invitationTransfer->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $this->errorMessage = sprintf('Email "%s" is not valid', $invitationTransfer->getEmail());

            return false;
        }

        $normalizedEmail = mb_strtolower($invitationTransfer->getEmailOrFail());
        $this->loadExistingEmails([$normalizedEmail]);

        if (
            in_array($normalizedEmail, $this->emailCache, true)
            || $this->existingEmailCache[$normalizedEmail]
        ) {
            $this->errorMessage = sprintf('Invitation for %s is already imported', $invitationTransfer->getEmail());

            return false;
        }

        $this->emailCache[] = $normalizedEmail;

        return true;
    }

    /**
     * @param list<string> $normalizedEmails
     *
     * @return void
     */
    protected function loadExistingEmails(array $normalizedEmails): void
    {
        $emailsToLoad = [];

        foreach ($normalizedEmails as $normalizedEmail) {
            if (!isset($this->existingEmailCache[$normalizedEmail])) {
                $this->existingEmailCache[$normalizedEmail] = false;
                $emailsToLoad[] = $normalizedEmail;
            }
        }

        if (!$emailsToLoad) {
            return;
        }

        $existingEmails = $this->repository->getCompanyUserInvitationEmailsByEmails($emailsToLoad);

        foreach ($existingEmails as $existingEmail) {
            $this->existingEmailCache[mb_strtolower($existingEmail)] = true;
        }
    }

    /**
     * @param \Generated\Shared\Transfer\CompanyUserInvitationTransfer $invitationTransfer
     *
     * @return void
     */
    protected function populateBusinessUnitCache(CompanyUserInvitationTransfer $invitationTransfer)
    {
        $companyUserTransfer = $this->companyUserFacade->getCompanyUserById($invitationTransfer->getFkCompanyUser());

        $companyBusinessUnitCriteriaFilter = (new CompanyBusinessUnitCriteriaFilterTransfer())
            ->setIdCompany($companyUserTransfer->getFkCompany());

        $companyBusinessUnitCollectionTransfer = $this->companyBusinessUnitFacade->getCompanyBusinessUnitCollection(
            $companyBusinessUnitCriteriaFilter,
        );

        foreach ($companyBusinessUnitCollectionTransfer->getCompanyBusinessUnits() as $companyBusinessUnitTransfer) {
            $this->businessUnitNameCache[] = $companyBusinessUnitTransfer->getName();
        }
    }
}
