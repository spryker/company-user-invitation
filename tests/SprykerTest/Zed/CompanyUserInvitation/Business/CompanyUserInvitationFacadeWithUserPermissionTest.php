<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\CompanyUserInvitation\Business;

use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\CompanyRoleCollectionBuilder;
use Generated\Shared\DataBuilder\PermissionCollectionBuilder;
use Generated\Shared\Transfer\CompanyBusinessUnitTransfer;
use Generated\Shared\Transfer\CompanyRoleTransfer;
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
 * @group CompanyUserInvitationFacadeWithUserPermissionTest
 * Add your own group annotations below this line
 */
class CompanyUserInvitationFacadeWithUserPermissionTest extends Unit
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

        $companyRoleTransfer = $this->tester->haveCompanyRole([
            CompanyRoleTransfer::FK_COMPANY => $this->companyTransfer->getIdCompany(),
            CompanyRoleTransfer::PERMISSION_COLLECTION => $this->permissionCollectionTransfer,
        ]);
        $companyRoleCollectionTransfer = (new CompanyRoleCollectionBuilder())->build()
            ->addRole($companyRoleTransfer);

        $this->companyUserTransfer->setCompanyRoleCollection($companyRoleCollectionTransfer);

        $this->tester->assignCompanyRolesToCompanyUser($this->companyUserTransfer);
    }

    public function testImportCompanyUserInvitationsShouldReturnNoErrorsWhenEmailsDoNotExist(): void
    {
        $companyUserInvitationCollection = (new CompanyUserInvitationCollectionTransfer())
            ->addCompanyUserInvitation($this->createCompanyUserInvitationTransfer())
            ->addCompanyUserInvitation($this->createCompanyUserInvitationTransfer());
        $companyUserInvitationImportRequestTransfer = (new CompanyUserInvitationImportRequestTransfer())
            ->setCompanyUserInvitationCollection($companyUserInvitationCollection)
            ->setIdCompanyUser($this->companyUserTransfer->getIdCompanyUser());

        $companyUserInvitationImportResponseTransfer = $this->getFacade()
            ->importCompanyUserInvitations($companyUserInvitationImportRequestTransfer);

        $this->assertTrue($companyUserInvitationImportResponseTransfer->getIsSuccess());
        $this->assertEmpty($companyUserInvitationImportResponseTransfer->getErrors());
    }

    public function testImportCompanyUserInvitationsShouldReturnErrorsWhenEmailsExist(): void
    {
        $companyUserInvitationTransfer = $this->createCompanyUserInvitationTransfer();
        $this->haveCompanyUserInvitation(['email' => $companyUserInvitationTransfer->getEmail()]);
        $companyUserInvitationCollection = (new CompanyUserInvitationCollectionTransfer())
            ->addCompanyUserInvitation($companyUserInvitationTransfer);
        $companyUserInvitationImportRequestTransfer = (new CompanyUserInvitationImportRequestTransfer())
            ->setCompanyUserInvitationCollection($companyUserInvitationCollection)
            ->setIdCompanyUser($this->companyUserTransfer->getIdCompanyUser());

        $companyUserInvitationImportResponseTransfer = $this->getFacade()
            ->importCompanyUserInvitations($companyUserInvitationImportRequestTransfer);

        $this->assertTrue($companyUserInvitationImportResponseTransfer->getIsSuccess());
        $this->assertNotEmpty($companyUserInvitationImportResponseTransfer->getErrors());
    }

    public function testGetCompanyUserInvitationCollectionShouldReturnCorrectData(): void
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

        $this->assertNotEmpty($companyUserInvitationCollectionTransfer->getCompanyUserInvitations()->count());
    }

    public function testSendCompanyUserInvitationShouldReturnSuccess(): void
    {
        $companyUserInvitationSendRequestTransfer = (new CompanyUserInvitationSendRequestTransfer())
            ->setIdCompanyUser($this->companyUserTransfer->getIdCompanyUser())
            ->setCompanyUserInvitation($this->haveCompanyUserInvitation());

        $companyUserInvitationSendResponseTransfer = $this->getFacade()
            ->sendCompanyUserInvitation($companyUserInvitationSendRequestTransfer);

        $this->assertTrue($companyUserInvitationSendResponseTransfer->getIsSuccess());
    }

    public function testSendCompanyUserInvitationsShouldReturnSuccess(): void
    {
        $this->haveCompanyUserInvitation();
        $this->haveCompanyUserInvitation();

        $companyUserInvitationSendBatchResponseTransfer = $this->getFacade()
            ->sendCompanyUserInvitations($this->companyUserTransfer);

        $this->assertTrue($companyUserInvitationSendBatchResponseTransfer->getIsSuccess());
        $this->assertEmpty(0, $companyUserInvitationSendBatchResponseTransfer->getInvitationsFailed());
    }

    public function testUpdateCompanyUserInvitationStatusShouldReturnSuccess(): void
    {
        $companyUserInvitationTransfer = $this->haveCompanyUserInvitation();
        $companyUserInvitationUpdateStatusRequestTransfer = (new CompanyUserInvitationUpdateStatusRequestTransfer())
            ->setIdCompanyUser($this->companyUserTransfer->getIdCompanyUser())
            ->setStatusKey(CompanyUserInvitationConfig::INVITATION_STATUS_ACCEPTED)
            ->setCompanyUserInvitation($companyUserInvitationTransfer);

        $companyUserInvitationUpdateStatusResponseTransfer = $this->getFacade()
            ->updateCompanyUserInvitationStatus($companyUserInvitationUpdateStatusRequestTransfer);

        $this->assertTrue($companyUserInvitationUpdateStatusResponseTransfer->getIsSuccess());
    }

    public function testCreateCompanyUserInvitationShouldReturnSuccess(): void
    {
        $companyUserInvitationCreateRequestTransfer = (new CompanyUserInvitationCreateRequestTransfer())
            ->setIdCompanyUser($this->companyUserTransfer->getIdCompanyUser())
            ->setCompanyUserInvitation($this->createCompanyUserInvitationTransfer());

        $companyUserInvitationCreateResponseTransfer = $this->getFacade()
            ->createCompanyUserInvitation($companyUserInvitationCreateRequestTransfer);

        $this->assertTrue($companyUserInvitationCreateResponseTransfer->getIsSuccess());
    }

    public function testCreateCompanyUserInvitationShouldFailWhenFirstNameIsTooLong(): void
    {
        // Arrange
        $companyUserInvitationCreateRequestTransfer = (new CompanyUserInvitationCreateRequestTransfer())
            ->setIdCompanyUser($this->companyUserTransfer->getIdCompanyUser())
            ->setCompanyUserInvitation($this->createCompanyUserInvitationTransfer([
                CompanyUserInvitationTransfer::FIRST_NAME => str_repeat('a', 101),
            ]));

        // Act
        $companyUserInvitationCreateResponseTransfer = $this->getFacade()
            ->createCompanyUserInvitation($companyUserInvitationCreateRequestTransfer);

        // Assert
        $this->assertFalse($companyUserInvitationCreateResponseTransfer->getIsSuccess());
        $this->assertSame('First name cannot be longer than 100 characters', $companyUserInvitationCreateResponseTransfer->getError());
    }

    public function testCreateCompanyUserInvitationShouldFailWhenLastNameIsTooLong(): void
    {
        // Arrange
        $companyUserInvitationCreateRequestTransfer = (new CompanyUserInvitationCreateRequestTransfer())
            ->setIdCompanyUser($this->companyUserTransfer->getIdCompanyUser())
            ->setCompanyUserInvitation($this->createCompanyUserInvitationTransfer([
                CompanyUserInvitationTransfer::LAST_NAME => str_repeat('a', 101),
            ]));

        // Act
        $companyUserInvitationCreateResponseTransfer = $this->getFacade()
            ->createCompanyUserInvitation($companyUserInvitationCreateRequestTransfer);

        // Assert
        $this->assertFalse($companyUserInvitationCreateResponseTransfer->getIsSuccess());
        $this->assertSame('Last name cannot be longer than 100 characters', $companyUserInvitationCreateResponseTransfer->getError());
    }

    public function testCreateCompanyUserInvitationShouldSucceedWhenNamesHaveMaximumLength(): void
    {
        // Arrange
        $companyUserInvitationCreateRequestTransfer = (new CompanyUserInvitationCreateRequestTransfer())
            ->setIdCompanyUser($this->companyUserTransfer->getIdCompanyUser())
            ->setCompanyUserInvitation($this->createCompanyUserInvitationTransfer([
                CompanyUserInvitationTransfer::FIRST_NAME => str_repeat('ä', 100),
                CompanyUserInvitationTransfer::LAST_NAME => str_repeat('ä', 100),
            ]));

        // Act
        $companyUserInvitationCreateResponseTransfer = $this->getFacade()
            ->createCompanyUserInvitation($companyUserInvitationCreateRequestTransfer);

        // Assert
        $this->assertTrue($companyUserInvitationCreateResponseTransfer->getIsSuccess());
    }

    public function testCreateCompanyUserInvitationShouldFailWhenEmailIsAlreadyInvitedInAnotherLetterCase(): void
    {
        // Arrange
        $companyUserInvitationTransfer = $this->haveCompanyUserInvitation();
        $companyUserInvitationCreateRequestTransfer = (new CompanyUserInvitationCreateRequestTransfer())
            ->setIdCompanyUser($this->companyUserTransfer->getIdCompanyUser())
            ->setCompanyUserInvitation($this->createCompanyUserInvitationTransfer([
                CompanyUserInvitationTransfer::EMAIL => strtoupper($companyUserInvitationTransfer->getEmail()),
            ]));

        // Act
        $companyUserInvitationCreateResponseTransfer = $this->getFacade()
            ->createCompanyUserInvitation($companyUserInvitationCreateRequestTransfer);

        // Assert
        $this->assertFalse($companyUserInvitationCreateResponseTransfer->getIsSuccess());
        $this->assertStringContainsString('is already imported', $companyUserInvitationCreateResponseTransfer->getError());
    }

    public function testImportCompanyUserInvitationsShouldReturnErrorForDuplicateEmailInSameFile(): void
    {
        // Arrange
        $email = $this->createCompanyUserInvitationTransfer()->getEmail();
        $companyUserInvitationCollectionTransfer = (new CompanyUserInvitationCollectionTransfer())
            ->addCompanyUserInvitation($this->createCompanyUserInvitationTransfer([CompanyUserInvitationTransfer::EMAIL => $email]))
            ->addCompanyUserInvitation($this->createCompanyUserInvitationTransfer([CompanyUserInvitationTransfer::EMAIL => strtoupper($email)]));
        $companyUserInvitationImportRequestTransfer = (new CompanyUserInvitationImportRequestTransfer())
            ->setIdCompanyUser($this->companyUserTransfer->getIdCompanyUser())
            ->setCompanyUserInvitationCollection($companyUserInvitationCollectionTransfer);

        // Act
        $companyUserInvitationImportResponseTransfer = $this->getFacade()
            ->importCompanyUserInvitations($companyUserInvitationImportRequestTransfer);

        // Assert
        $this->assertCount(1, $companyUserInvitationImportResponseTransfer->getErrors());
        $this->assertStringContainsString('is already imported', $companyUserInvitationImportResponseTransfer->getErrors()[0]);
    }

    public function testImportCompanyUserInvitationsShouldReturnErrorWhenEmailIsInvitedByAnotherCompany(): void
    {
        // Arrange
        $anotherCompanyBusinessUnitTransfer = $this->haveCompanyBusinessUnitOfAnotherCompany();
        $companyUserInvitationTransfer = $this->tester->haveCompanyUserInvitation([
            CompanyUserInvitationTransfer::FK_COMPANY_USER => $this->haveCompanyUserOfAnotherCompany($anotherCompanyBusinessUnitTransfer)->getIdCompanyUser(),
            CompanyUserInvitationTransfer::COMPANY_BUSINESS_UNIT_NAME => $anotherCompanyBusinessUnitTransfer->getName(),
        ]);
        $companyUserInvitationImportRequestTransfer = (new CompanyUserInvitationImportRequestTransfer())
            ->setIdCompanyUser($this->companyUserTransfer->getIdCompanyUser())
            ->setCompanyUserInvitationCollection((new CompanyUserInvitationCollectionTransfer())->addCompanyUserInvitation(
                $this->createCompanyUserInvitationTransfer([CompanyUserInvitationTransfer::EMAIL => $companyUserInvitationTransfer->getEmail()]),
            ));

        // Act
        $companyUserInvitationImportResponseTransfer = $this->getFacade()
            ->importCompanyUserInvitations($companyUserInvitationImportRequestTransfer);

        // Assert
        $this->assertCount(1, $companyUserInvitationImportResponseTransfer->getErrors());
        $this->assertStringContainsString('is already imported', $companyUserInvitationImportResponseTransfer->getErrors()[0]);
    }

    public function testDeleteCompanyUserInvitationShouldReturnSuccess(): void
    {
        $companyUserInvitationDeleteRequestTransfer = (new CompanyUserInvitationDeleteRequestTransfer())
            ->setIdCompanyUser($this->companyUserTransfer->getIdCompanyUser())
            ->setCompanyUserInvitation($this->haveCompanyUserInvitation());

        $companyUserInvitationDeleteResponseTransfer = $this->getFacade()
            ->deleteCompanyUserInvitation($companyUserInvitationDeleteRequestTransfer);

        $this->assertTrue($companyUserInvitationDeleteResponseTransfer->getIsSuccess());
    }

    public function testSendCompanyUserInvitationShouldFailForInvitationOfAnotherCompany(): void
    {
        // Arrange
        $companyUserInvitationTransfer = $this->haveCompanyUserInvitation();
        $companyUserInvitationSendRequestTransfer = (new CompanyUserInvitationSendRequestTransfer())
            ->setIdCompanyUser($this->haveCompanyUserOfAnotherCompany()->getIdCompanyUser())
            ->setCompanyUserInvitation($companyUserInvitationTransfer);

        // Act
        $companyUserInvitationSendResponseTransfer = $this->getFacade()
            ->sendCompanyUserInvitation($companyUserInvitationSendRequestTransfer);

        // Assert
        $this->assertFalse($companyUserInvitationSendResponseTransfer->getIsSuccess());
        $this->assertCompanyUserInvitationIsUnchanged($companyUserInvitationTransfer);
    }

    public function testUpdateCompanyUserInvitationStatusShouldFailForInvitationOfAnotherCompany(): void
    {
        // Arrange
        $companyUserInvitationTransfer = $this->haveCompanyUserInvitation();
        $companyUserInvitationUpdateStatusRequestTransfer = (new CompanyUserInvitationUpdateStatusRequestTransfer())
            ->setIdCompanyUser($this->haveCompanyUserOfAnotherCompany()->getIdCompanyUser())
            ->setStatusKey(CompanyUserInvitationConfig::INVITATION_STATUS_DELETED)
            ->setCompanyUserInvitation($companyUserInvitationTransfer);

        // Act
        $companyUserInvitationUpdateStatusResponseTransfer = $this->getFacade()
            ->updateCompanyUserInvitationStatus($companyUserInvitationUpdateStatusRequestTransfer);

        // Assert
        $this->assertFalse($companyUserInvitationUpdateStatusResponseTransfer->getIsSuccess());
        $this->assertCompanyUserInvitationIsUnchanged($companyUserInvitationTransfer);
    }

    public function testDeleteCompanyUserInvitationShouldFailForInvitationOfAnotherCompany(): void
    {
        // Arrange
        $companyUserInvitationTransfer = $this->haveCompanyUserInvitation();
        $companyUserInvitationDeleteRequestTransfer = (new CompanyUserInvitationDeleteRequestTransfer())
            ->setIdCompanyUser($this->haveCompanyUserOfAnotherCompany()->getIdCompanyUser())
            ->setCompanyUserInvitation($companyUserInvitationTransfer);

        // Act
        $companyUserInvitationDeleteResponseTransfer = $this->getFacade()
            ->deleteCompanyUserInvitation($companyUserInvitationDeleteRequestTransfer);

        // Assert
        $this->assertFalse($companyUserInvitationDeleteResponseTransfer->getIsSuccess());
        $this->assertCompanyUserInvitationIsUnchanged($companyUserInvitationTransfer);
    }

    protected function assertCompanyUserInvitationIsUnchanged(CompanyUserInvitationTransfer $companyUserInvitationTransfer): void
    {
        $persistedCompanyUserInvitationTransfer = $this->getFacade()->getCompanyUserInvitationByHash(
            (new CompanyUserInvitationTransfer())->setHash($companyUserInvitationTransfer->getHash()),
        );

        $this->assertSame($companyUserInvitationTransfer->getIdCompanyUserInvitation(), $persistedCompanyUserInvitationTransfer->getIdCompanyUserInvitation());
        $this->assertSame($companyUserInvitationTransfer->getEmail(), $persistedCompanyUserInvitationTransfer->getEmail());
        $this->assertSame($companyUserInvitationTransfer->getFkCompanyUserInvitationStatus(), $persistedCompanyUserInvitationTransfer->getFkCompanyUserInvitationStatus());
    }

    protected function haveCompanyBusinessUnitOfAnotherCompany(): CompanyBusinessUnitTransfer
    {
        return $this->tester->haveCompanyBusinessUnit([
            CompanyBusinessUnitTransfer::FK_COMPANY => $this->tester->haveCompany()->getIdCompany(),
        ]);
    }

    protected function haveCompanyUserOfAnotherCompany(?CompanyBusinessUnitTransfer $companyBusinessUnitTransfer = null): CompanyUserTransfer
    {
        $companyBusinessUnitTransfer ??= $this->haveCompanyBusinessUnitOfAnotherCompany();
        $companyUserTransfer = $this->tester->haveCompanyUser([
            CompanyUserTransfer::FK_COMPANY_BUSINESS_UNIT => $companyBusinessUnitTransfer->getIdCompanyBusinessUnit(),
            CompanyUserTransfer::FK_COMPANY => $companyBusinessUnitTransfer->getFkCompany(),
            CompanyUserTransfer::CUSTOMER => $this->tester->haveCustomer(),
        ]);

        $companyRoleTransfer = $this->tester->haveCompanyRole([
            CompanyRoleTransfer::FK_COMPANY => $companyBusinessUnitTransfer->getFkCompany(),
            CompanyRoleTransfer::PERMISSION_COLLECTION => $this->permissionCollectionTransfer,
        ]);
        $companyUserTransfer->setCompanyRoleCollection((new CompanyRoleCollectionBuilder())->build()->addRole($companyRoleTransfer));
        $this->tester->assignCompanyRolesToCompanyUser($companyUserTransfer);

        return $companyUserTransfer;
    }

    protected function createCompanyUserInvitationTransfer(array $seedData = []): CompanyUserInvitationTransfer
    {
        $seedData = $seedData + [
            CompanyUserInvitationTransfer::FK_COMPANY_USER => $this->companyUserTransfer->getIdCompanyUser(),
            CompanyUserInvitationTransfer::COMPANY_BUSINESS_UNIT_NAME => $this->companyBusinessUnitTransfer->getName(),
        ];

        return $this->tester->createCompanyUserInvitationTransfer($seedData);
    }

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
