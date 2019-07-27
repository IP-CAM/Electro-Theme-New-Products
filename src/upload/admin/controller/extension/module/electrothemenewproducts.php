<?php
/**
 * 
 * electrothemenewproducts.php
 * 
 * ControllerExtensionModuleElectrothemenewproducts
 * 
 * Controller class for admin panel.
 * 
*/
class ControllerExtensionModuleElectrothemenewproducts extends Controller
{
		private $error = array();

		/** 
		 * 
		 * index()
		 * 
		*/
		public function index() {

			$this->load->language('extension/module/electrothemenewproducts');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->load->model('setting/module');

			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
				if (!isset($this->request->get['module_id'])) {
					$this->model_setting_module->addModule('electrothemenewproducts', $this->request->post);
				} else {
					$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
				}

				$this->session->data['success'] = $this->language->get('text_success');

				$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
			}

			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
			} else {
				$data['error_warning'] = '';
			}

			if (isset($this->error['name'])) {
				$data['error_name'] = $this->error['name'];
			} else {
				$data['error_name'] = '';
			}


			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_extension'),
				'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
			);

			if (!isset($this->request->get['module_id'])) {
				$data['breadcrumbs'][] = array(
					'text' => $this->language->get('heading_title'),
					'href' => $this->url->link('extension/module/electrothemenewproducts', 'user_token=' . $this->session->data['user_token'], true)
				);
			} else {
				$data['breadcrumbs'][] = array(
					'text' => $this->language->get('heading_title'),
					'href' => $this->url->link('extension/module/electrothemenewproducts', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
				);
			}

			if (!isset($this->request->get['module_id'])) {
				$data['action'] = $this->url->link('extension/module/electrothemenewproducts', 'user_token=' . $this->session->data['user_token'], true);
			} else {
				$data['action'] = $this->url->link('extension/module/electrothemenewproducts', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
			}

			$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

			if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
				$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
			}

			if (isset($this->request->post['name'])) {
				$data['name'] = $this->request->post['name'];
			} elseif (!empty($module_info)) {
				$data['name'] = $module_info['name'];
			} else {
				$data['name'] = '';
			}

			if (isset($this->request->post['status'])) {
				$data['status'] = $this->request->post['status'];
			} elseif (!empty($module_info)) {
				$data['status'] = $module_info['status'];
			} else {
				$data['status'] = '';
			}

			$this->doAction($data,$module_info);


			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('extension/module/electrothemenewproducts', $data));
		}
		/** 
		 * 
		 * doAction()
		 * 
		 * @param	$data
		 * @param	$module_info
		 * 
		 * @return	null
		*/
		protected function doAction(&$data, &$module_info){
			$this->load->model("setting/setting");
			
			if (isset($this->request->post['type'])){
				
				if ($this->request->post['type'] == 'date'){
					$data['type']='date';

					if (isset(
						$this->request->post['dnp']) 
						&& (!!strtotime($this->request->post['dnp']))
					){
						$data['date_new_products']=new DateTime($this->request->post['dnp']);
					} elseif (!empty($module_info)){
						$data['date_new_products']=new DateTime($module_info['dnp']);
					} else {
						$data['date_new_products']=new Datetime('now');
					}
					$data['date_new_products']=$data['date_new_products']->format('Y-m-d');

				} else if ($this->request->post['type'] == 'period'){
					$data['type']='period';

					if (
						isset($this->request->post['period']) 
						&& in_array(
							$this->request->post['period'],
							['year','month','week','day','hour']
						)
					){
						$data['period']=$this->request->post['period'];
					} elseif (!empty($module_info)){
						$data['period']=$module_info['period'];
					} else {
						$data['period']='day';
					}
					
				}
			} else if (isset($module_info['type'])){	// if type is not set
					$data['type']=$module_info['type'];
					if ($data['type'] == 'date'){
						if (
							isset($module_info['dnp'])
							&& (!!strtotime($module_info['dnp']))
						){
							$data['date_new_products']=new DateTime($module_info['dnp']);
						} else {
							$data['date_new_products']=new DateTime('now');
						}
						
						$data['date_new_products']=$data['date_new_products']->format('Y-m-d');
					} else if ($data['type'] == 'period'){
						if (isset($module_info['period'])){
							$data['period']=$module_info['period'];
						} else {
							$data['period']='day';
						}
						
					}
			} else {
				$data['type']='date';
				$data['date_new_products']=new DateTime('now');
				$data['date_new_products']=$data['date_new_products']->format('Y-m-d');
			}
			
			$data['text_date_new_products']=$this->language->get('text_date_new_products');
			
		}
		/** 
		 * 
		 * validate()
		 * 
		*/
		protected function validate() {
			if (!$this->user->hasPermission('modify', 'extension/module/electrothemenewproducts')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}

			if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
				$this->error['name'] = $this->language->get('error_name');
			}

			return !$this->error;
		}
}