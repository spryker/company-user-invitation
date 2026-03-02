<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyUserInvitation\Business\Model\Installer;

use Generated\Shared\Transfer\CompanyUserInvitationStatusTransfer;
use Spryker\Zed\CompanyUserInvitation\CompanyUserInvitationConfig;
use Spryker\Zed\CompanyUserInvitation\Persistence\CompanyUserInvitationEntityManagerInterface;
use Spryker\Zed\CompanyUserInvitation\Persistence\CompanyUserInvitationRepositoryInterface;

class CompanyUserInvitationStatusInstaller implements CompanyUserInvitationStatusInstallerInterface
{
    /**
     * @var \Spryker\Zed\CompanyUserInvitation\Persistence\CompanyUserInvitationRepositoryInterface
     */
    protected $repository;

    /**
     * @var \Spryker\Zed\CompanyUserInvitation\Persistence\CompanyUserInvitationEntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var \Spryker\Zed\CompanyUserInvitation\CompanyUserInvitationConfig
     */
    protected $config;

    public function __construct(
        CompanyUserInvitationRepositoryInterface $repository,
        CompanyUserInvitationEntityManagerInterface $entityManager,
        CompanyUserInvitationConfig $config
    ) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->config = $config;
    }

    public function install(): void
    {
        foreach ($this->config->getCompanyUserInvitationStatusKeys() as $statusKey) {
            if (!$this->repository->findCompanyUserInvitationStatusByStatusKey($statusKey)) {
                $companyUserInvitationStatusTransfer = (new CompanyUserInvitationStatusTransfer())
                    ->setStatusKey($statusKey);
                $this->entityManager->saveCompanyUserInvitationStatus($companyUserInvitationStatusTransfer);
            }
        }
    }
}
