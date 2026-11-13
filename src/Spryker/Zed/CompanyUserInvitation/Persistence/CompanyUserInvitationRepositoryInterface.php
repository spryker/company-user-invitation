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

interface CompanyUserInvitationRepositoryInterface
{
    public function getCompanyUserInvitationCollection(
        CompanyUserInvitationCriteriaFilterTransfer $companyUserInvitationCriteriaFilterTransfer
    ): CompanyUserInvitationCollectionTransfer;

    public function findCompanyUserInvitationStatusByStatusKey(
        string $statusKey
    ): ?CompanyUserInvitationStatusTransfer;

    public function findCompanyUserInvitationByIdAndIdCompanyUser(
        CompanyUserInvitationTransfer $companyUserInvitationTransfer,
        int $idCompanyUser
    ): ?CompanyUserInvitationTransfer;

    /**
     * @param list<string> $emails
     *
     * @return list<string>
     */
    public function getCompanyUserInvitationEmailsByEmails(array $emails): array;

    public function getCompanyUserInvitationByHash(
        CompanyUserInvitationTransfer $companyUserInvitationTransfer
    ): CompanyUserInvitationTransfer;
}
