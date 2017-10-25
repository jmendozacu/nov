<?php 
    class Smartwave_Legenda_Adminhtml_DemoController extends Mage_Adminhtml_Controller_Action{
        public function indexAction() {
            $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/legenda_settings/"));
        }
        public function importAction() {
            $refererUrl = $this->_getRefererUrl();
            if(empty($refererUrl)){
                $refererUrl = $this->getUrl("adminhtml/system_config/edit/section/legenda_settings/");
            }
            $demoversion = $this->getRequest()->getParam('demoversion');
            $website = $this->getRequest()->getParam('website');
            $store   = $this->getRequest()->getParam('store');
            Mage::getSingleton('legenda/import_demoversion')->importDemoversion($demoversion,$store,$website);
            Mage::getSingleton('legenda/config_generator')->generateCss('setting', $website, $store);
            Mage::getSingleton('legenda/config_generator')->generateCss('design', $website, $store);
            $this->getResponse()->setRedirect($refererUrl);
        }
    }
?>