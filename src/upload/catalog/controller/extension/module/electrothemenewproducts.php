<?php
/** 
 * 
 * electrothemenewproducts.php
 * 
 * ControllerExtensionModuleElectrothemenewproducts
 * 
 * Controller file for front page.
*/
class ControllerExtensionModuleElectrothemenewproducts extends Controller
{
	/** 
	 * 
	 * index()
	 * 
	 * @param	$setting	The module's settings
	 * 
	*/
	public function index($setting)
	{		

		$this->load->model('extension/module/electrothemenewproducts');
		$this->load->language('extension/module/electrothemenewproducts');

		$currency=$this->session->data['currency'];
		
		$result=$this->model_extension_module_electrothemenewproducts->getProductList($setting,$currency);
		
		$data=array(
			'products'=>$result['products'],
			'categories'=>$result['categories']
		);
		$data['text_new_products']=$this->language->get('text_new_products');

		return $this->load->view('extension/module/electrothemenewproducts', $data);
	}
}