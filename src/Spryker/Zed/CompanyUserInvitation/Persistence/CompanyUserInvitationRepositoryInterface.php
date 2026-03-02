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

    public function findCompanyUserInvitationById(
        CompanyUserInvitationTransfer $companyUserInvitationTransfer
    ): ?CompanyUserInvitationTransfer;

    public function getCompanyUserInvitationByHash(
        CompanyUserInvitationTransfer $companyUserInvitationTransfer
    ): CompanyUserInvitationTransfer;
}
