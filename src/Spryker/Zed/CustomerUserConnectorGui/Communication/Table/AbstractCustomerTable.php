<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CustomerUserConnectorGui\Communication\Table;

use Generated\Shared\Transfer\UserTransfer;
use Orm\Zed\Customer\Persistence\Map\SpyCustomerTableMap;
use Orm\Zed\Customer\Persistence\SpyCustomer;
use Propel\Runtime\Collection\ObjectCollection;
use Spryker\Zed\CustomerUserConnectorGui\Dependency\QueryContainer\CustomerUserConnectorGuiToCustomerQueryContainerInterface;
use Spryker\Zed\CustomerUserConnectorGui\Dependency\Service\CustomerUserConnectorGuiToUtilEncodingServiceInterface;
use Spryker\Zed\CustomerUserConnectorGui\Dependency\Service\CustomerUserConnectorGuiToUtilSanitizeServiceInterface;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

abstract class AbstractCustomerTable extends AbstractTable
{
    /**
     * @var string
     */
    public const COL_ID = 'id_customer';

    /**
     * @var string
     */
    public const COL_FIRST_NAME = 'first_name';

    /**
     * @var string
     */
    public const COL_LAST_NAME = 'last_name';

    /**
     * @var string
     */
    public const COL_GENDER = 'gender';

    /**
     * @var string
     */
    public const COL_EMAIL = 'email';

    /**
     * @var string
     */
    public const COL_ASSIGNED_USER = 'assigned_zed_user';

    /**
     * @var string
     */
    public const COL_CHECKBOX = 'checkbox';

    /**
     * @var array<int, string>
     */
    public const GENDER_MAP = [
        0 => 'Male',
        1 => 'Female',
    ];

    /**
     * @var bool
     */
    public const IS_CHECKBOX_SET_BY_DEFAULT = true;

    /**
     * @var \Spryker\Zed\CustomerUserConnectorGui\Dependency\QueryContainer\CustomerUserConnectorGuiToCustomerQueryContainerInterface
     */
    protected $customerQueryContainer;

    /**
     * @var \Generated\Shared\Transfer\UserTransfer
     */
    protected $userTransfer;

    /**
     * @var \Spryker\Zed\CustomerUserConnectorGui\Dependency\Service\CustomerUserConnectorGuiToUtilSanitizeServiceInterface
     */
    protected $utilSanitizeService;

    /**
     * @var \Spryker\Zed\CustomerUserConnectorGui\Dependency\Service\CustomerUserConnectorGuiToUtilEncodingServiceInterface
     */
    protected $utilEncoding;

    /**
     * @param \Spryker\Zed\CustomerUserConnectorGui\Dependency\QueryContainer\CustomerUserConnectorGuiToCustomerQueryContainerInterface $customerQueryContainer
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     * @param \Spryker\Zed\CustomerUserConnectorGui\Dependency\Service\CustomerUserConnectorGuiToUtilSanitizeServiceInterface $utilSanitizeService
     * @param \Spryker\Zed\CustomerUserConnectorGui\Dependency\Service\CustomerUserConnectorGuiToUtilEncodingServiceInterface $utilEncoding
     */
    public function __construct(
        CustomerUserConnectorGuiToCustomerQueryContainerInterface $customerQueryContainer,
        UserTransfer $userTransfer,
        CustomerUserConnectorGuiToUtilSanitizeServiceInterface $utilSanitizeService,
        CustomerUserConnectorGuiToUtilEncodingServiceInterface $utilEncoding
    ) {
        $this->customerQueryContainer = $customerQueryContainer;
        $this->userTransfer = $userTransfer;
        $this->utilSanitizeService = $utilSanitizeService;
        $this->utilEncoding = $utilEncoding;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            static::COL_ID => 'ID',
            static::COL_FIRST_NAME => 'First Name',
            static::COL_LAST_NAME => 'Last Name',
            static::COL_EMAIL => 'Customer E-mail',
            static::COL_GENDER => 'Gender',
            static::COL_ASSIGNED_USER => 'Assigned Zed User Reference',
            static::COL_CHECKBOX => $this->getCheckboxHeaderName(),
        ]);

        $config->setSortable([
            static::COL_ID,
            static::COL_FIRST_NAME,
            static::COL_LAST_NAME,
            static::COL_GENDER,
        ]);

        $config->setRawColumns([
            static::COL_ASSIGNED_USER,
            static::COL_CHECKBOX,
        ]);

        $config->setSearchable([
            SpyCustomerTableMap::COL_FIRST_NAME,
            SpyCustomerTableMap::COL_LAST_NAME,
            SpyCustomerTableMap::COL_EMAIL,
        ]);

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $query = $this->prepareQuery();
        /** @var \Propel\Runtime\Collection\ObjectCollection $customerCollection */
        $customerCollection = $this->runQuery($query, $config, true);
        $data = $this->buildResultData($customerCollection);

        return $data;
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection<\Orm\Zed\Customer\Persistence\SpyCustomer> $customerEntities
     *
     * @return array
     */
    protected function buildResultData(ObjectCollection $customerEntities)
    {
        $tableRows = [];
        foreach ($customerEntities as $customerEntity) {
            $tableRows[] = $this->getRow($customerEntity);
        }

        return $tableRows;
    }

    /**
     * @param \Orm\Zed\Customer\Persistence\SpyCustomer $customerEntity
     *
     * @return array
     */
    protected function getRow(SpyCustomer $customerEntity)
    {
        return [
            static::COL_ID => $this->formatInt($customerEntity->getIdCustomer()),
            static::COL_FIRST_NAME => $customerEntity->getFirstName(),
            static::COL_LAST_NAME => $customerEntity->getLastName(),
            static::COL_EMAIL => $customerEntity->getEmail(),
            static::COL_GENDER => $customerEntity->getGender(),
            static::COL_ASSIGNED_USER => $this->getAssignedUserColumn($customerEntity),
            static::COL_CHECKBOX => $this->getCheckboxColumn($customerEntity),
        ];
    }

    /**
     * @param \Orm\Zed\Customer\Persistence\SpyCustomer $customerEntity
     *
     * @return string
     */
    protected function getCheckboxColumn(SpyCustomer $customerEntity)
    {
        return sprintf(
            '<input class="%s" type="checkbox" name="idCustomer[]" value="%d" %s data-info="%s" />',
            'js-customer-checkbox',
            $customerEntity->getIdCustomer(),
            static::IS_CHECKBOX_SET_BY_DEFAULT ? 'checked' : '',
            $this->utilSanitizeService->escapeHtml($this->utilEncoding->encodeJson([
                'idCustomer' => $customerEntity->getIdCustomer(),
                'firstname' => $this->utilSanitizeService->escapeHtml($customerEntity->getFirstName()),
                'lastname' => $this->utilSanitizeService->escapeHtml($customerEntity->getLastName()),
                'gender' => $customerEntity->getGender(),
            ])),
        );
    }

    /**
     * @param \Orm\Zed\Customer\Persistence\SpyCustomer $customerEntity
     *
     * @return string
     */
    abstract protected function getAssignedUserColumn(SpyCustomer $customerEntity);

    /**
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    abstract protected function prepareQuery();

    /**
     * @return string
     */
    abstract protected function getCheckboxHeaderName();
}
