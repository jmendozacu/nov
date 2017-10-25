<?php 
class Smartwave_Legenda_Helper_Data extends Mage_Core_Helper_Abstract
{
    // get config for theme setting
    public function getConfig($optionName) 
    {
        if (Mage::registry('legenda_css_generate_store')) {
            $store_code = Mage::registry('legenda_css_generate_store');
            $store_id = Mage::getModel('core/store')->load($store_code)->getId();
        } else {
            $store_id = NULL;
        }        
        return Mage::getStoreConfig('legenda_setting/' . $optionName, $store_id);
    }
    // get config group for theme setting section
    public function getConfigGroup($storeId = NULL)
    {
        if (!$storeId) {
            $store_code = Mage::registry('legenda_css_generate_store');
            $storeId = Mage::getModel('core/store')->load($store_code)->getId();
        }

        if ($storeId)
            return Mage::getStoreConfig('legenda_setting', $storeId);
        else
            return Mage::getStoreConfig('legenda_setting');
    }    
    
    // get config for theme design
    public function getConfigDesign($optionName)
    {
        if (Mage::registry('legenda_css_generate_store')) {
            $store_code = Mage::registry('legenda_css_generate_store');
            $store_id = Mage::getModel('core/store')->load($store_code)->getId();
        } else {
            $store_id = NULL;
        }
        return Mage::getStoreConfig('legenda_design/' . $optionName, $store_id);
    }
    
    // get config group for theme design section
    public function getConfigGroupDesign($storeId = NULL)
    {
        if (!$storeId) {
            $store_code = Mage::registry('legenda_css_generate_store');
            $storeId = Mage::getModel('core/store')->load($store_code)->getId();
        }

        if ($storeId)
            return Mage::getStoreConfig('legenda_design', $storeId);
        else
            return Mage::getStoreConfig('legenda_design');
    }
    public function getTopPanelId() {
        if ($this->getConfig('header_settings/top_panel'))
            return $this->getConfig('header_settings/top_panel');
        return false;
    }
    
    public function getCategoryColumns($code) {
//        $id = Mage::getModel('catalog/layer')->getCurrentCategory()->getId();
//        $catModel = Mage::getModel('catalog/category')->load($id);
        $columns = $this->_getBlocks(Mage::getModel('catalog/layer')->getCurrentCategory(), 'sw_lg_cat_design');
        if ($columns) {
            return $columns;
        } else {
            return Mage::getStoreConfig('legenda_setting/category_grid/columns', $code);
        }
    }
    
    public function getCategoryBannerBlock() {
        $cat_banner = $this->_getBlocks(Mage::getModel('catalog/layer')->getCurrentCategory(), 'sw_lg_cat_banner');        
        if($cat_banner) {            
            return $cat_banner;
        }
        return false;
    }
    
    public function getCategoryListPos() {
        $cat_pos = $this->_getBlocks(Mage::getModel('catalog/layer')->getCurrentCategory(), 'sw_lg_cat_position');
        if ($cat_pos) {
            return $cat_pos;
        } else {
            $store = Mage::app()->getStore();
            $code  = $store->getCode();
            $cat_pos = Mage::getStoreConfig('legenda_setting/category/main_cat_position', $code);
            return $cat_pos;
        }
    }
    private function _getBlocks($model, $block_signal) {
        if (!isset($this->_tplProcessor) || !$this->_tplProcessor)
        { 
            $this->_tplProcessor = Mage::helper('cms')->getBlockTemplateProcessor();            
        }
        return $this->_tplProcessor->filter( trim($model->getData($block_signal)) ); 
    }
    public function getHomeUrl() {
        return array(
            "label" => $this->__('Home'),
            "title" => $this->__("Home Page"),
            "link"  => Mage::getUrl()
        );
    }
    
    //custom tab
    public function isEnabledonConfig($id){
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        if(Mage::getStoreConfig("legenda_setting/product_view_custom_tab/custom_tab",$code)){
            if(Mage::getStoreConfig("legenda_setting/product_view_custom_tab/".$id,$code))
                return true;
        }
        return false;
    }
    public function isEnabledfromCategory(){
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        if(Mage::getStoreConfig("legenda_setting/product_view_custom_tab/from_category",$code))
            return true;
        return false;
    }
    public function getTabIdField($type,$id){
        $num = substr($id,-1);
        $config_id = "";
        switch($type){
            case "attribute":
                $config_id = "attribute_tab_id_".$num;
                break;
            case "static_block":
                $config_id = "static_block_tab_id_".$num;
                break;
        }
        return $config_id;
    }
    public function isEnabledonParentCategory($attribute, $category){
        //$category = Mage::getModel("catalog/category")->load($category_id);
        if($category->getData($attribute) == "yes"){
            return true;
        }
        if($category->getData($attribute) == "no"){
            return false;
        }
        if(!$category->getData($attribute)){
            if($category->getId() == Mage::app()->getStore()->getRootCategoryId() || $category->getId() == 1){
                return true;
            }
            return $this->isEnabledonParentCategory($attribute, $category->getParentCategory());
        }
    }
    public function isEnabledonCategory($type, $id, $product_id){
        $product = Mage::getModel("catalog/product")->load($product_id);
        $attribute = "";
        if($type=="attribute"){
            $attribute = "sw_product_attribute_tab_".substr($id,-1);
        }else{
            $attribute = "sw_product_staticblock_tab_".substr($id,-1);
        }
        $category = $product->getCategory();
        if(!$category){
            $c = $product->getCategoryCollection()->addAttributeToSelect("*");
            $category = $c->getFirstItem();
        }
        return $this->isEnabledonParentCategory($attribute, $category);
    }
    public function isEnabledTab($type, $id, $product_id){
        $store = Mage::app()->getStore();
        $code  = $store->getCode();

        if(!$this->isEnabledonConfig($id)){
            return false;
        }
        $config_id = Mage::getStoreConfig("legenda_setting/product_view_custom_tab/".$this->getTabIdField($type,$id),$code);
        if(!$config_id)
            return false;
        if(!$this->getTabTitle($type, $id, $product_id))
            return false;
        if($this->isEnabledfromCategory()){
            if(!$this->isEnabledonCategory($type, $id, $product_id))
                return false;
        }
        return true;
    }
    public function getTabTitle($type, $id, $product_id){
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        $config_id = Mage::getStoreConfig("legenda_setting/product_view_custom_tab/".$this->getTabIdField($type,$id),$code);
        $title = "";
        switch($type){
            case "attribute":
                $product = Mage::getModel("catalog/product")->load($product_id);
                $title = "";
                if($product->getResource()->getAttribute($config_id)){
                    $title = $product->getResource()->getAttribute($config_id)->getStoreLabel();
                    if(!$product->getResource()->getAttribute($config_id)->getFrontend()->getValue($product))
                        $title = "";
                }
                break;
            case "static_block":
                $block = Mage::getModel("cms/block")->setStoreId(Mage::app()->getStore()->getId())->load($config_id);
                $title = $block->getTitle();
                if(!$block->getIsActive())
                    $title = "";
                break;
        }
        return $title;
    }
    public function getTabContents($type, $id, $product_id){
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        $config_id = Mage::getStoreConfig("legenda_setting/product_view_custom_tab/".$this->getTabIdField($type,$id),$code);
        $content = "";
        switch($type){
            case "attribute":
                $product = Mage::getModel("catalog/product")->load($product_id);
                $content = $product->getResource()->getAttribute($config_id)->getFrontend()->getValue($product);
                $proc_helper = Mage::helper('cms');
                $processor = $proc_helper->getPageTemplateProcessor();
                $content = $processor->filter($content);
                break;
            case "static_block":
                $block = Mage::getModel("cms/block")->setStoreId(Mage::app()->getStore()->getId())->load($config_id);
                $content = $block->getContent();
                $proc_helper = Mage::helper('cms');
                $processor = $proc_helper->getPageTemplateProcessor();
                $content = $processor->filter($content);
                break;
        }
        return $content;
    }
}