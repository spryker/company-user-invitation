<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyUserInvitation\Dependency\Facade;

use Generated\Shared\Transfer\CompanyUserResponseTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;

interface CompanyUserInvitationToCompanyUserFacadeInterface
{
    public function getCompanyUserById(int $idCompanyUser): CompanyUserTransfer;

    public function update(CompanyUserTransfer $companyUserTransfer): CompanyUserResponseTransfer;
}
