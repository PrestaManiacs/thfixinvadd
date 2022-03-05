<?php
/**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Thfixinvaddr extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'thfixinvaddr';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Presta Maniacs';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Fix Invalid Address');
        $this->description = $this->l('Module to fix Invalid Address PrestaShop Exception');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install() &&
            $this->registerHook('actionAdminControllerSetMedia');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $this->context->smarty->assign('module_dir', $this->_path);
        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/_partials/maniacs.tpl');

        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitThfixinvaddrModule')) == true) {
            $this->postProcess();
            if (count($this->_errors)) {
                $output .= $this->displayError($this->_errors);
            } else {
                $output .= $this->displayConfirmation($this->l('Successfully fixed!'));
            }
        }

        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        $affected_carts_count = $this->getAffectedCarts();
        if ($affected_carts_count) {
            return $output.$this->renderForm();
        }

        $message = $this->context->smarty->fetch($this->local_path.'views/templates/admin/_partials/no_affected.tpl');
        return $output.$message;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitThfixinvaddrModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        $affected_carts_count = $this->getAffectedCarts();
        $this->context->smarty->assign(
            array(
                'affected_carts_count' => $affected_carts_count
            )
        );

        $message = $this->context->smarty->fetch($this->local_path.'views/templates/admin/_partials/affected.tpl');

        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'th_html',
                        'label' => '',
                        'name' => 'THFIXINVADDR_MESSAGE',
                        'html_content' => $message
                    ),
                    array(
                        'type' => 'select',
                        'name' => 'THFIXINVADDR_FIX_TYPE',
                        'label' => $this->l('Fix Type for Affected Items:'),
                        'options' => array(
                            'query' => array(
                                array(
                                    'option_value' => 'id_cart',
                                    'option_title' => 'Remove Cart Items from DB'
                                ),
                                array(
                                    'option_value' => 'id_address',
                                    'option_title' => 'Set ID Address to 0'
                                )
                            ),
                            'id' => 'option_value',
                            'name' => 'option_title'
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Fix Invalid Addresses'),
                    'class' => 'btn btn-default pull-right'
                ),
            ),
        );
    }

    public function getAffectedCarts($count = true)
    {
        $sql = 'SELECT `id_cart` FROM `'._DB_PREFIX_.'cart` AS c LEFT JOIN `'._DB_PREFIX_.'address` AS a ON c.`id_address_delivery` = a.`id_address` WHERE c.`id_address_delivery` > 0 AND a.`id_address` IS NULL';
        $result = Db::getInstance()->executeS($sql);
        if ($count) {
            if ($result) {
                return count($result);
            } else {
                return 0;
            }
        }

        return $result;
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'THFIXINVADDR_FIX_TYPE' => Tools::getValue('THFIXINVADDR_FIX_TYPE', Configuration::get('THFIXINVADDR_FIX_TYPE'))
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }

        $affected_carts = $this->getAffectedCarts(false);
        if ($affected_carts) {
            $affected_carts_ids = array();
            foreach ($affected_carts as $affected_cart) {
                $affected_carts_ids[] = $affected_cart['id_cart'];
            }

            $this->fixCarts($affected_carts_ids, Tools::getValue('THFIXINVADDR_FIX_TYPE'));
        }
    }

    public function fixCarts($affected_carts_ids, $type = 'id_cart')
    {
        if ($type == 'id_cart') {
            $result = $this->fixIdCarts($affected_carts_ids);
        } else {
            $result = $this->fixIdAddresses($affected_carts_ids);
        }

        if (!$result) {
            $this->_errors[] = 'An error occurred!';
        }
    }

    public function fixIdCarts($affected_carts_ids)
    {
        $sql = 'DELETE FROM `'._DB_PREFIX_.'cart` WHERE 
                `id_cart` IN (' . implode(',', array_map('intval', $affected_carts_ids)) . ')';

        return Db::getInstance()->execute($sql);
    }

    public function fixIdAddresses($affected_carts_ids)
    {
        return Db::getInstance()->update(
            'cart',
            array(
                'id_address_delivery' => 0,
                'id_address_invoice' => 0
            ),
            '`id_cart` IN (' . implode(',', array_map('intval', $affected_carts_ids)) . ')'
        );
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookActionAdminControllerSetMedia()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }
}
