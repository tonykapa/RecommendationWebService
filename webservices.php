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

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

class Webservices extends Module implements WidgetInterface
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'webservices';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'TonyK';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Web Services');
        $this->description = $this->l('Call Web Service of Mobiplus');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('WEBSERVICES_LIVE_MODE', false);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('actionValidateOrder') &&
            $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName('WEBSERVICES_LIVE_MODE');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitWebservicesModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
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
        $helper->submit_action = 'submitWebservicesModule';
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
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'WEBSERVICES_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'WEBSERVICES_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'WEBSERVICES_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'WEBSERVICES_LIVE_MODE' => Configuration::get('WEBSERVICES_LIVE_MODE', true),
            'WEBSERVICES_ACCOUNT_EMAIL' => Configuration::get('WEBSERVICES_ACCOUNT_EMAIL', 'contact@prestashop.com'),
            'WEBSERVICES_ACCOUNT_PASSWORD' => Configuration::get('WEBSERVICES_ACCOUNT_PASSWORD', null),
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
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        
        
    }

     // For Debug
     function console_log($output, $with_script_tags = true) {
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . 
    ');';
        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
    }
    
    public function salesinsert()
    {
        $token = "12345";
        @session_start();
        $session_id = session_id();
        $cart = $this->context->cart;
        $cart_upd = $this->context->cart->date_upd;
        $products_cart = Context::getContext()->cart->getProducts();
        $user_id = $_COOKIE["user_id"];
        $this->context = Context::getContext() ;
        
       
        foreach ($products_cart as $pr)
        {
            $pr['name'] = str_replace(' ', '', $pr['name']);
            $pr['name'] = urlencode($pr['name']);
            $cart_upd = str_replace(' ', '', $cart_upd);
            $url = str_replace(' ','%',"https://b2b.portaplus.gr:8443/MobiplusWS/AppServices/saleinsert_et?itemid=".$pr['id_product']."&userid=".$user_id."&sessionid=".$session_id."&purchaseprice=".$pr['total_wt']."&name=".$pr['name']."&catid=".$pr['id_category_default']."&Timestamp=".$pr['date_add']."&token=".$token);
            
       
            $ch = curl_init();            
            // set url
            curl_setopt($ch, CURLOPT_URL,$url); 
                     
            //  server response 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $errmsg  = curl_error( $ch );
            curl_close($ch);
        }   
    }

   
    
    
    public function getPopularItems()
    {
        $token = "12345";
        @session_start();
        $session_id = session_id();
        $cart = $this->context->cart;
        $products_cart = Context::getContext()->cart->getProducts();
        $item_id = $products_cart[0]['id_product'] ;
        $url = "https://b2b.portaplus.gr:8443/MobiplusWS/AppServices/similar_items_et?itemsid=".$item_id."&userid=&sessionid=".$session_id."&token=".$token;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response_coded = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response_coded , true);
        if ($response)
        {
            foreach($response as $res)
            {
                $array_id[] = $res['Item_ID'];
            }
            return $array_id;
        }else 
        {
            $url = "https://b2b.portaplus.gr:8443/MobiplusWS/AppServices/popular_items_et?userid=&sessionid=".$session_id."&token=".$token;
        
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response_coded = curl_exec($ch);
            $errmsg  = curl_error( $ch );
            curl_close($ch);
            $response = json_decode($response_coded , true);
            return $response;
        }
    }
    

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path.'/views/js/front_ws.js');
        $user_id = $this->context->customer->id;        
        $cookie_name = "user_id";
        $cookie_value = $user_id;
        if(!isset($_COOKIE['$cookie_name']))
        {
            if ($this->context->customer->isLogged()) {
                setcookie($cookie_name, $cookie_value);
            }else{
                setcookie($cookie_name, '0');
            }
        }
    }

    public function hookActionValidateOrder()
    {
        $this->salesinsert();     
    }
     
    
    protected function getMobi()
    {

        $lang_id = (int) Configuration::get('PS_LANG_DEFAULT');
        
        $test_array = $this->getPopularItems() ;
        shuffle($test_array); 
        
        
        $assembler = new ProductAssembler($this->context);
        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                $this->context->link
            ),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );

        $products_for_template = [];

        foreach ($test_array as $rawProduct) 
        {
            $product = new Product($rawProduct, false);
            $this->console_log($product);
            if($product->quantity > 0)
            {
                $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct(['id_product' => $rawProduct]),
                $this->context->language);
            }else {continue;}
        }

        return $products_for_template;
    }

        
    public function renderWidget($hookName, array $configuration) 
    {
        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));

        return $this->fetch('module:'.$this->name.'/views/templates/widget/mymodule.tpl');
    }
 
    public function getWidgetVariables($hookName , array $configuration)
    {
        $products = $this->getMobi();

        if (!empty($products)) {
            return array(
                'products' => $products,
                
            );
        }

        return false;
    }
        
    
}
