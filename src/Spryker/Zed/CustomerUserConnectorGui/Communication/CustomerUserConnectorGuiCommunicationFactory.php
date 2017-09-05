<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CustomerUserConnectorGui\Communication;

use Generated\Shared\Transfer\UserTransfer;
use Spryker\Zed\CustomerUserConnectorGui\Communication\Form\CustomerUserConnectorForm;
use Spryker\Zed\CustomerUserConnectorGui\Communication\Table\AssignedCustomerTable;
use Spryker\Zed\CustomerUserConnectorGui\Communication\Table\AvailableCustomerTable;
use Spryker\Zed\CustomerUserConnectorGui\CustomerUserConnectorGuiDependencyProvider;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;

class CustomerUserConnectorGuiCommunicationFactory extends AbstractCommunicationFactory
{

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Spryker\Zed\CustomerUserConnectorGui\Communication\Table\AssignedCustomerTable
     */
    public function createAssignedCustomerTable(UserTransfer $userTransfer)
    {
        return new AssignedCustomerTable(
            $this->getProvidedDependency(CustomerUserConnectorGuiDependencyProvider::QUERY_CONTAINER_CUSTOMER),
            $userTransfer
        );
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Spryker\Zed\CustomerUserConnectorGui\Communication\Table\AvailableCustomerTable
     */
    public function createAvailableCustomerTable(UserTransfer $userTransfer)
    {
        return new AvailableCustomerTable(
            $this->getProvidedDependency(CustomerUserConnectorGuiDependencyProvider::QUERY_CONTAINER_CUSTOMER),
            $userTransfer
        );
    }

    /**
     * @param int $idUser
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createCustomerUserConnectorForm($idUser)
    {
        return $this->getFormFactory()->create(new CustomerUserConnectorForm(), [CustomerUserConnectorForm::FIELD_ID_USER => $idUser]);
    }

    /**
     * @return \Spryker\Zed\Customer\Persistence\CustomerQueryContainerInterface
     */
    public function getCustomerQueryContainer()
    {
        return $this->getProvidedDependency(CustomerUserConnectorGuiDependencyProvider::QUERY_CONTAINER_CUSTOMER);
    }

}
