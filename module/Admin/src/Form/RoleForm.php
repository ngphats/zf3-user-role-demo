<?php 
namespace Admin\Form;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class RoleForm extends Form {

	protected $aclConfig;
	
	function __construct($aclConfig) {
		$this->aclConfig = $aclConfig;
		parent::__construct('role_form');
		$this->setAttribute('method', 'POST');
		$this->addElement();
		$this->addInputFilter();
	}

	public function addElement() {

		//add name form
		$this->add([
			'name' => 'name',
			'type' => 'text',
			'attributes' => [
				'class' => 'form-control',
			],
			'options' => [
				'label' => 'Role name'
			]
		]);

		//add content form:
		$this->add([
			'name' => 'description',
			'type' => 'textarea',
			'attributes' => [
				'class' => 'form-control',
				'rows' => 3
			],
			'options' => [
				'label' => 'Description'
			]
		]);			

		if (!empty($this->aclConfig)) 
		{
			foreach ($this->aclConfig as $key => $val) :
				$data2 = array_map(function($action) {
					return ucfirst($action) . ' Action';
				}, $val['privileges']);
				$action = array_combine($val['privileges'], $data2);

				$this->add([
					'name' => $val['resource'],
					'type' => 'MultiCheckBox',
					'attributes' => [
						'class' => 'multi-checkbox',
					],
					'options' => [
						'label' => $key.' Controller',
						'value_options' => $action,
					],
				]);

			endforeach;
		}

		//add submit form:
		$this->add([
			'name' => 'submit',
			'type' => 'submit',
			'attributes' => [
				'class' => 'btn btn-primary'
			]
		]);
	}

	public function addInputFilter() {
		//create new inputfilter:
		$input = new InputFilter;

		//set inputfilter in form:
		$this->setInputFilter($input);

		/** add input filter */
		$input->add(array(
				'name' => 'name',
				'required' => true,
				'filters' => array(
					array('name'=>'StringTrim'),
					array('name'=>'StripTags')
				),
				'validators' => array(
					array('name' => 'NotEmpty')
				)
			));	

		$input->add(array(
				'name' => 'description',
				'required' => true,
				'filters' => array(
					array('name'=>'StringTrim'),
				),
				'validators' => array(
					array('name' => 'NotEmpty')
				)
			));	

		if (!empty($this->aclConfig)) 
		{
			foreach ($this->aclConfig as $val) {
		        $input->add([
		            'name'     => $val['resource'],
		            'required' => false,           
		        ]); 
			}
		}     
	}
}