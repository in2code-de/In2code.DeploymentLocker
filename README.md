# In2code.DeploymentLocker - Prevent parallel execution of the same deployment

## Description

TYPO3.Surf is a CLI application for automated deployment. When a deployment is runs,
many tasks are executed in a order. If a deployment runs parallel in two seperate processes,
it might leave your deployment target in an unusable and hard recoverable state.

To prevent, that one deployment is executet twice ore more times at the same time, you
can simply lock it.

This does not affect any other deployment. You can still run another deployment in parallel!

## Installation

Install In2code.DeploymentLocker via composer

	composer require in2code/deploymentlocker

## How to Use

Open the deployment you want to protect and add followinf line after the php opening tag.
It should look like this: (Notice that the annotation is optional)

	<?php
	/** @var \TYPO3\Surf\Domain\Model\Deployment $deployment */
	\In2code\DeploymentLocker\DeploymentLocker::lockDeployment($deployment);

That's it. Repeat for any other deployment you want to protect.

## What happens?

The DeploymentLocker writes himself to $GLOBALS, so the object will exist for the whole
runtime. (Maybe a static property would be equivalent ore better?)
When the object is created, it checks if there's a lock file for the current deployment.
If true, then an Exception will be thrown and the deployment stops immediatley.
Otherwise the lock file is created. After runtime the lockfile will be
deleted and the deployment can be started again.

You can catch the Exception in your deployment and output some other information or
implement a "sleep for X and try again later" stuff, but i won't recommend that.

## Testing & Contributing

Feel free to open a discussion, create a fork or create pull requests.

This package is "tested by production" ;)
Honestly this is a excerpt from another package i wrote some years ago.
That package is still used for Surf < 2.0 and requires In2code.SurfGui.

## Copyright

All copyright belongs to in2code GmbH <info@in2code.de>.
The author of this package is Oliver Eglseder <oliver.eglseder@in2code.de>.
This package is licensed under GNU General Public License, version 3 or later (http://www.gnu.org/licenses/gpl.html).
