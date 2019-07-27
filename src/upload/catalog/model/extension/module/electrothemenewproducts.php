<?php
/** 
 * 
 * electrothemenewproducts.php
 * 
 * ModelExtensionModuleElectrothemenewproducts
 * 
 * Model file for front page.
 * 
*/
class ModelExtensionModuleElectrothemenewproducts extends Model
{
    private function getMultiplexor($currency){
        $temp=$this->db->query("SELECT * FROM ".DB_PREFIX."currency WHERE code='".$currency."'");
        $multiplexor=$temp->row['value'];
        if (!isset($multiplexor) || !is_numeric($multiplexor) || ($multiplexor <= 0)){
            $multiplexor=1;
        }
        return $multiplexor;
    }
    /** 
     * 
     * getProductList()
     * 
     * @param   $setting    the module's settings
     * 
     * @return  $result     array of products
     * 
    */
    public function getProductList($setting,$currency){
        
        $this->load->model('catalog/product');
        $products=$this->model_catalog_product->getProducts();

        $this->load->model('setting/setting');
        if (isset($setting['type'])){
            if ($setting['type'] == 'date'){
                $filterDate=new DateTime($setting['dnp']);
            } else if ($setting['type'] == 'period'){
                switch ($setting['period']){
                    
                    case 'month':$interval=new DateInterval('P1M');break;
                    case 'week':$interval=new DateInterval('P7D');break;
                    case 'day':$interval=new DateInterval('P1D');break;
                    case 'hour':$interval=new DateInterval('P0DT1H');break;
                }
                $now=new DateTime('now');
                $filterDate=$now->sub($interval);
            } else {
                $now=new DateTime('now');
                $filterDate=$now->sub(new DateInterval('P1D'));
            }
        } else {
            $now=new DateTime('now');
            $filterDate=$now->sub(new DateInterval('P1D'));
        }
        
        // Setting a result array 
        $result=array(
            'products'=>array(),
            'categories'=>array()
        );

        // Getting all of the categories
        $this->load->model('catalog/category');
        $categories=$this->model_catalog_category->getCategories();

        $temp_categories=array();

        // Getting current currency
        $multiplexor=$this->getMultiplexor($currency);

        foreach ($products as $product){
            // Getting the categories for the current product
            $product['categories']=$this->model_catalog_product->getCategories($product['product_id']);

            $d0=new DateTime($product['date_available']);
            $d1=$filterDate;

            
            // if product's date_available is later than the saved date
            if ( $d0 > $d1){
                $product['price']=$this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

                if ($product['special']){
                        
                    $product['special'] = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

                }

                // adds the current product if its new one
                $result['products'][]=$product;

                // creates a list of categories to display
                foreach ($categories as $category){
                    foreach ($product['categories'] as $product_category){

                        // gets the proper category
                        if ($category['category_id'] == $product_category['category_id']){
                            // if current category has not been added to the categories list
                            if (!key_exists($category['category_id'],$result['categories'])){
                                // then adds current category to the categories list
                                $result['categories'][$category['category_id']]=$category['name'];
                                $temp_categories[]=array(
                                    'id'=>$category['category_id'],
                                    'name'=>$category['name']
                                );

                            }
                        }

                    }
                    
                }

            }
        }
        unset($result['categories']);
        
        $result['categories']=$temp_categories;

        return $result;
    }
}