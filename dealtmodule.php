<?php

use DealtModule\Database\DealtInstaller;

if (!defined('_PS_VERSION_')) exit;
if (file_exists(__DIR__ . '/vendor/autoload.php')) require_once __DIR__ . '/vendor/autoload.php';

class DealtModule extends Module
{
  static $DEALT_PRODUCT_CATEGORY_NAME = "__dealt__";

  public function __construct()
  {
    $this->name = 'dealtmodule';
    $this->tab = 'administration';
    $this->version = '0.0.1';
    $this->author = 'Dealt Developers';
    $this->need_instance = 0;
    $this->ps_versions_compliancy = [
      'min' => '1.7.7',
      'max' => '1.7.99',
    ];
    $this->bootstrap = true;

    parent::__construct();
    $this->displayName = $this->l('Dealt Module');
    $this->description = $this->l('The official Dealt prestashop module.');
    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

    if (!Configuration::get('dealtmodule')) {
      $this->warning = $this->l('No name provided');
    }
  }

  public function install()
  {
    $this->setup();
    return parent::install();
  }

  public function uninstall()
  {
    $this->getInstaller()->dropTables();
    return parent::uninstall();
  }

  /**
   * Instantiates the DealtInstaller module if needed :
   * During the installation process, the module's services are not available
   * as they are not registered yet.
   * 
   * @return DealtInstaller
   */
  private function getInstaller()
  {
    try {
      $installer = $this->get('dealtmodule.installer');
    } catch (Exception $e) {
      $installer = null;
    }

    if (!$installer) {
      $installer = new DealtInstaller(
        $this->get('doctrine.dbal.default_connection'),
        $this->getContainer()->getParameter('database_prefix')
      );
    }

    return $installer;
  }

  private function setup()
  {

    /* create DealtModule SQL tables */
    $this->getInstaller()->createTables();

    /* create internal DealtModule category */
    $match = Category::searchByName(null, static::$DEALT_PRODUCT_CATEGORY_NAME, true);

    if (empty($match)) {
      $idLang = (int) Context::getContext()->language->id;

      $category = new Category();
      $category->name = [$idLang => static::$DEALT_PRODUCT_CATEGORY_NAME];
      $category->link_rewrite = [$idLang => Tools::link_rewrite(static::$DEALT_PRODUCT_CATEGORY_NAME)];
      $category->active = false;
      $category->id_parent = Configuration::get('PS_ROOT_CATEGORY');
      $category->description = "Internal DealtModule category used for Dealt mission virtual products";
      $category->add();
    }
  }
}