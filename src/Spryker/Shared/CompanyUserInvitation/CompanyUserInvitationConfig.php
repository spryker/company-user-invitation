<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\CompanyUserInvitation;

class CompanyUserInvitationConfig
{
    /**
     * @api
     *
     * @var string
     */
    public const INVITATION_STATUS_DELETED = 'deleted';

    /**
     * @api
     *
     * @var string
     */
    public const INVITATION_STATUS_NEW = 'new';

    /**
     * @api
     *
     * @var string
     */
    public const INVITATION_STATUS_ACCEPTED = 'accepted';

    /**
     * @api
     *
     * @var string
     */
    public const INVITATION_STATUS_PENDING = 'pending';

    /**
     * @api
     *
     * @var string
     */
    public const ROUTE_INVITATION_ACCEPT = 'invitation/accept';

    /**
     * @api
     *
     * @var string
     */
    public const INVITATION_HASH = 'hash';
}
