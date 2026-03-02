<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyUserInvitation\Communication\Controller;

use Generated\Shared\Transfer\CompanyUserInvitationCollectionTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationGetCollectionRequestTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationImportRequestTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationImportResponseTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationSendBatchResponseTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationSendRequestTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationSendResponseTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationUpdateStatusRequestTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationUpdateStatusResponseTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * @method \Spryker\Zed\CompanyUserInvitation\Business\CompanyUserInvitationFacadeInterface getFacade()
 * @method \Spryker\Zed\CompanyUserInvitation\Communication\CompanyUserInvitationCommunicationFactory getFactory()
 * @method \Spryker\Zed\CompanyUserInvitation\Persistence\CompanyUserInvitationRepositoryInterface getRepository()
 */
class GatewayController extends AbstractGatewayController
{
    public function importCompanyUserInvitationsAction(
        CompanyUserInvitationImportRequestTransfer $companyUserInvitationImportRequestTransfer
    ): CompanyUserInvitationImportResponseTransfer {
        return $this->getFacade()->importCompanyUserInvitations($companyUserInvitationImportRequestTransfer);
    }

    public function getCompanyUserInvitationCollectionAction(
        CompanyUserInvitationGetCollectionRequestTransfer $companyUserInvitationGetCollectionRequestTransfer
    ): CompanyUserInvitationCollectionTransfer {
        return $this->getFacade()->getCompanyUserInvitationCollection($companyUserInvitationGetCollectionRequestTransfer);
    }

    public function sendCompanyUserInvitationAction(
        CompanyUserInvitationSendRequestTransfer $companyUserInvitationSendRequestTransfer
    ): CompanyUserInvitationSendResponseTransfer {
        return $this->getFacade()->sendCompanyUserInvitation($companyUserInvitationSendRequestTransfer);
    }

    public function sendCompanyUserInvitationsAction(
        CompanyUserTransfer $companyUserTransfer
    ): CompanyUserInvitationSendBatchResponseTransfer {
        return $this->getFacade()->sendCompanyUserInvitations($companyUserTransfer);
    }

    public function updateCompanyUserInvitationStatusAction(
        CompanyUserInvitationUpdateStatusRequestTransfer $companyUserInvitationUpdateStatusRequestTransfer
    ): CompanyUserInvitationUpdateStatusResponseTransfer {
        return $this->getFacade()->updateCompanyUserInvitationStatus($companyUserInvitationUpdateStatusRequestTransfer);
    }

    public function getCompanyUserInvitationByHashAction(
        CompanyUserInvitationTransfer $companyUserInvitationTransfer
    ): CompanyUserInvitationTransfer {
        return $this->getFacade()->getCompanyUserInvitationByHash($companyUserInvitationTransfer);
    }
}
