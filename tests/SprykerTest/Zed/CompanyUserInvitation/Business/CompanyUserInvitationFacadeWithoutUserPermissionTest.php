<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\CompanyUserInvitation\Business;

use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\PermissionCollectionBuilder;
use Generated\Shared\Transfer\CompanyBusinessUnitTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationCollectionTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationCreateRequestTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationCriteriaFilterTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationDeleteRequestTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationGetCollectionRequestTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationImportRequestTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationSendRequestTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationTransfer;
use Generated\Shared\Transfer\CompanyUserInvitationUpdateStatusRequestTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Spryker\Shared\CompanyUserInvitation\CompanyUserInvitationConfig;
use Spryker\Zed\CompanyRole\Communication\Plugin\PermissionStoragePlugin;
use Spryker\Zed\CompanyUserInvitation\Communication\Plugin\Permission\ManageCompanyUserInvitationPermissionPlugin;
use Spryker\Zed\Permission\PermissionDependencyProvider;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group CompanyUserInvitation
 * @group Business
 * @group Facade
 * @group CompanyUserInvitationFacadeWithoutUserPermissionTest
 * Add your own group annotations below this line
 */
class CompanyUserInvitationFacadeWithoutUserPermissionTest extends Unit
{
    /**
     * @var array
     */
    public const PERMISSION_PLUGINS = [
        ManageCompanyUserInvitationPermissionPlugin::class,
    ];

    /**
     * @var \SprykerTest\Zed\CompanyUserInvitation\CompanyUserInvitationBusinessTester
     */
    protected $tester;

    /**
     * @var \Generated\Shared\Transfer\CustomerTransfer
     */
    protected $customerTransfer;

    /**
     * @var \Generated\Shared\Transfer\CompanyTransfer
     */
    protected $companyTransfer;

    /**
     * @var \Generated\Shared\Transfer\CompanyBusinessUnitTransfer
     */
    protected $companyBusinessUnitTransfer;

    /**
     * @var \Generated\Shared\Transfer\CompanyUserTransfer
     */
    protected $companyUserTransfer;

    /**
     * @var \Generated\Shared\Transfer\PermissionCollectionTransfer
     */
    protected $permissionCollectionTransfer;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->getFacade()->install();

        $this->tester->addDependency(PermissionDependencyProvider::PLUGINS_PERMISSION_STORAGE, [
            new PermissionStoragePlugin(),
        ]);

        $this->customerTransfer = $this->tester->haveCustomer();
        $this->companyTransfer = $this->tester->haveCompany();
        $this->companyBusinessUnitTransfer = $this->tester->haveCompanyBusinessUnit([
            CompanyBusinessUnitTransfer::FK_COMPANY => $this->companyTransfer->getIdCompany(),
        ]);

        $this->companyUserTransfer = $this->tester->haveCompanyUser([
            CompanyUserTransfer::FK_COMPANY_BUSINESS_UNIT => $this->companyBusinessUnitTransfer->getIdCompanyBusinessUnit(),
            CompanyUserTransfer::FK_COMPANY => $this->companyTransfer->getIdCompany(),
            CompanyUserTransfer::CUSTOMER => $this->customerTransfer,
        ]);

        $this->permissionCollectionTransfer = (new PermissionCollectionBuilder())->build();
        foreach (static::PERMISSION_PLUGINS as $permissionPlugin) {
            $permissionTransfer = $this->tester->havePermission(new $permissionPlugin());
            $this->permissionCollectionTransfer->addPermission($permissionTransfer);
        }
    }

    /**
     * @return void
     */
    public function testImportCompanyUserInvitationsShouldFail(): void
    {
        $companyUserInvitationCollection = (new CompanyUserInvitationCollectionTransfer())
            ->addCompanyUserInvitation($this->createCompanyUserInvitationTransfer())
            ->addCompanyUserInvitation($this->createCompanyUserInvitationTransfer());
        $companyUserInvitationImportRequestTransfer = (new CompanyUserInvitationImportRequestTransfer())
            ->setCompanyUserInvitationCollection($companyUserInvitationCollection)
            ->setIdCompanyUser($this->companyUserTransfer->getIdCompanyUser());

        $companyUserInvitationImportResponseTransfer = $this->getFacade()
            ->importCompanyUserInvitations($companyUserInvitationImportRequestTransfer);

        $this->assertFalse($companyUserInvitationImportResponseTransfer->getIsSuccess());
        $this->assertEmpty($companyUserInvitationImportResponseTransfer->getErrors());
    }

    /**
     * @return void
     */
    public function testGetCompanyUserInvitationCollectionShouldReturnNoData(): void
    {
        $this->haveCompanyUserInvitation();
        $this->haveCompanyUserInvitation();
        $criteriaFilterTransfer = (new CompanyUserInvitationCriteriaFilterTransfer())
            ->setFkCompanyUser($this->companyUserTransfer->getFkCompany())
            ->setFkCompanyUser($this->companyUserTransfer->getIdCompanyUser())
            ->setCompanyUserInvitationStatusKeyIn([CompanyUserInvitationConfig::INVITATION_STATUS_NEW]);
        $companyUserInvitationGetCollectionRequestTransfer = (new CompanyUserInvitationGetCollectionRequestTransfer())
            ->setIdCompanyUser($this->companyUserTransfer->getIdCompanyUser())
            ->setCriteriaFilter($criteriaFilterTransfer);

        $companyUserInvitationCollectionTransfer = $this->getFacade()
            ->getCompanyUserInvitationCollection($companyUserInvitationGetCollectionRequestTransfer);

        $this->assertSame(0, $companyUserInvitationCollectionTransfer->getCompanyUserInvitations()->count());
    }

    /**
     * @return void
     */
    public function testSendCompanyUserInvitationShouldFail(): void
    {
        $companyUserInvitationSendRequestTransfer = (new CompanyUserInvitationSendRequestTransfer())
            ->setIdCompanyUser($this->companyUserTransfer->getIdCompanyUser())
            ->setCompanyUserInvitation($this->haveCompanyUserInvitation());

        $companyUserInvitationSendResponseTransfer = $this->getFacade()
            ->sendCompanyUserInvitation($companyUserInvitationSendRequestTransfer);

        $this->assertFalse($companyUserInvitationSendResponseTransfer->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testSendCompanyUserInvitationsShouldFail(): void
    {
        $this->haveCompanyUserInvitation();
        $this->haveCompanyUserInvitation();

        $companyUserInvitationSendBatchResponseTransfer = $this->getFacade()
            ->sendCompanyUserInvitations($this->companyUserTransfer);

        $this->assertFalse($companyUserInvitationSendBatchResponseTransfer->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testUpdateCompanyUserInvitationStatusShouldFail(): void
    {
        $companyUserInvitationTransfer = $this->haveCompanyUserInvitation();
        $companyUserInvitationUpdateStatusRequestTransfer = (new CompanyUserInvitationUpdateStatusRequestTransfer())
            ->setIdCompanyUser($this->companyUserTransfer->getIdCompanyUser())
            ->setStatusKey(CompanyUserInvitationConfig::INVITATION_STATUS_ACCEPTED)
            ->setCompanyUserInvitation($companyUserInvitationTransfer);

        $companyUserInvitationUpdateStatusResponseTransfer = $this->getFacade()
            ->updateCompanyUserInvitationStatus($companyUserInvitationUpdateStatusRequestTransfer);

        $this->assertFalse($companyUserInvitationUpdateStatusResponseTransfer->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testGetCompanyUserInvitationByHashShouldReturnCorrectData(): void
    {
        $companyUserInvitationTransfer = $this->haveCompanyUserInvitation();

        $queryResultCompanyUserInvitationTransfer = $this->getFacade()
            ->getCompanyUserInvitationByHash($companyUserInvitationTransfer);

        $this->assertEquals(
            $companyUserInvitationTransfer->getIdCompanyUserInvitation(),
            $queryResultCompanyUserInvitationTransfer->getIdCompanyUserInvitation(),
        );
    }

    /**
     * @return void
     */
    public function testCreateCompanyUserInvitationShouldFail(): void
    {
        $companyUserInvitationCreateRequestTransfer = (new CompanyUserInvitationCreateRequestTransfer())
            ->setIdCompanyUser($this->companyUserTransfer->getIdCompanyUser())
            ->setCompanyUserInvitation($this->createCompanyUserInvitationTransfer());

        $companyUserInvitationCreateResponseTransfer = $this->getFacade()
            ->createCompanyUserInvitation($companyUserInvitationCreateRequestTransfer);

        $this->assertFalse($companyUserInvitationCreateResponseTransfer->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testDeleteCompanyUserInvitationShouldFail(): void
    {
        $companyUserInvitationDeleteRequestTransfer = (new CompanyUserInvitationDeleteRequestTransfer())
            ->setIdCompanyUser($this->companyUserTransfer->getIdCompanyUser())
            ->setCompanyUserInvitation($this->haveCompanyUserInvitation());

        $companyUserInvitationDeleteResponseTransfer = $this->getFacade()
            ->deleteCompanyUserInvitation($companyUserInvitationDeleteRequestTransfer);

        $this->assertFalse($companyUserInvitationDeleteResponseTransfer->getIsSuccess());
    }

    /**
     * @param array $seedData
     *
     * @return \Generated\Shared\Transfer\CompanyUserInvitationTransfer
     */
    protected function createCompanyUserInvitationTransfer(array $seedData = []): CompanyUserInvitationTransfer
    {
        $seedData = $seedData + [
                CompanyUserInvitationTransfer::FK_COMPANY_USER => $this->companyUserTransfer->getIdCompanyUser(),
                CompanyUserInvitationTransfer::COMPANY_BUSINESS_UNIT_NAME => $this->companyBusinessUnitTransfer->getName(),
            ];

        return $this->tester->createCompanyUserInvitationTransfer($seedData);
    }

    /**
     * @param array $seedData
     *
     * @return \Generated\Shared\Transfer\CompanyUserInvitationTransfer
     */
    protected function haveCompanyUserInvitation(array $seedData = []): CompanyUserInvitationTransfer
    {
        $seedData = $seedData + [
            CompanyUserInvitationTransfer::FK_COMPANY_USER => $this->companyUserTransfer->getIdCompanyUser(),
            CompanyUserInvitationTransfer::COMPANY_BUSINESS_UNIT_NAME => $this->companyBusinessUnitTransfer->getName(),
        ];

        return $this->tester->haveCompanyUserInvitation($seedData);
    }

    /**
     * @return \Spryker\Zed\CompanyUserInvitation\Business\CompanyUserInvitationFacadeInterface|\Spryker\Zed\Kernel\Business\AbstractFacade
     */
    protected function getFacade()
    {
        return $this->tester->getFacade();
    }
}
