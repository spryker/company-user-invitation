<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyUserInvitation\Persistence;

use Generated\Shared\Transfer\CompanyUserInvitationStatusTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationTransfer;

interface CompanyUserInvitationEntityManagerInterface
{
    public function saveCompanyUserInvitation(
        CompanyUserInvitationTransfer $companyUserInvitationTransfer
    ): CompanyUserInvitationTransfer;

    public function saveCompanyUserInvitationStatus(
        CompanyUserInvitationStatusTransfer $companyUserInvitationStatusTransfer
    ): CompanyUserInvitationStatusTransfer;

    public function deleteCompanyUserInvitationById(int $idCompanyUserInvitation): void;
}
