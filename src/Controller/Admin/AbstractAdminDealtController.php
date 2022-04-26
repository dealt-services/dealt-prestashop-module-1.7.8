<?php

declare(strict_types=1);

namespace DealtModule\Controller\Admin;

use DealtModule\Database\DealtInstaller;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

abstract class AbstractAdminDealtController extends FrameworkBundleAdminController
{
  public function flashModuleWarnings()
  {
    if (!DealtInstaller::isModuleConfigured()) {
      $this->addFlash('error', $this->trans('The API Key provided is not a valid Dealt UUID.', 'Modules.DealtModule.Admin'));
    }

    if (!DealtInstaller::isProduction()) {
      $this->addFlash('warning', $this->trans('The module is currently running in Test mode', 'Modules.DealtModule.Admin'));
    }
  }
}