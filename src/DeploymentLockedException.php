<?php
namespace In2code\DeploymentLocker;

use TYPO3\Surf\Domain\Model\Deployment;

/**
 * Class DeploymentLockedException
 */
class DeploymentLockedException extends \RuntimeException
{
    /**
     * DeploymentLockedException constructor.
     *
     * @param Deployment $deployment
     */
    public function __construct(Deployment $deployment)
    {
        parent::__construct(sprintf('The deployment "%s" is locked!', $deployment->getName()), 1457543101);
    }
}
