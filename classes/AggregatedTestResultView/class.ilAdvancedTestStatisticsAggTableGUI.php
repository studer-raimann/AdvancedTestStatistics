<?php

/**
 * Class ilAdvancedTestStatisticsAggTableGUI
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilAdvancedTestStatisticsAggTableGUI extends ilTable2GUI {

	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	protected $parent_obj;
	/**
	 * @var ilAdvancedTestStatisticsPlugin
	 */
	protected $pl;
	protected $results = array(
		'Total number of participants who started the test',
		'Total finished tests (Participants that used up all possible passes)',
		'Average test processing time',
		'Total passed tests',
		'Average points of passed tests',
		'Average processing time of all passed tests'
	);


	public function __construct($a_parent_obj, $a_parent_cmd = "", $a_template_context = "") {
		global $ilCtrl, $ilTabs;

		$this->ctrl = $ilCtrl;
		$this->tabs = $ilTabs;
		$this->pl = ilAdvancedTestStatisticsPlugin::getInstance();
		$this->ref_id = $_GET['ref_id'];
		$this->object = ilObjectFactory::getInstanceByRefId($this->ref_id);

		parent::__construct($a_parent_obj, $a_parent_cmd, $a_template_context);

		$this->setRowTemplate('tpl.row_template.html', $this->pl->getDirectory());



		$this->addCustomExtendedFields();
		$this->addColumns();
		$this->parseData();
	}

	public function construct_array(){
		$class = new ilAdvancedTestStatisticsAggResults();
		$tst_id = $class->getTstidforRefid($this->ref_id);

		$data = $this->object->getAggregatedResultsData();


		return $data['overview'];
	}


	public function addCustomExtendedFields(){
		$ext_fields = xatsExtendedStatisticsFields::where(array('ref_id' => $this->ref_id))->first();

		if($ext_fields->isAvgPointsFinished() == 1){
			array_push($this->results,'Average points finished tests');
		}
		if($ext_fields->isAvgResultPassed() == 1){
			array_push($this->results,'Average result (%) passed tests');
		}
		if($ext_fields->isAvgResultFinished() == 1){
			array_push($this->results,'Average result(%) finished tests');
		}
		if($ext_fields->isAvgResultPassedRunOne() == 1){
			array_push($this->results,'Average result(%) passed tests (Run 1)');
		}
		if($ext_fields->isAvgResultFinishedRunOne() == 1){
			array_push($this->results,'Average result(%) finished tests (Run 1)');
		}
		if($ext_fields->isAvgResultPassedRunTwo() == 1){
			array_push($this->results,'Average result(%) passed tests (Run 2)');
		}
		if($ext_fields->isAvgResultsFinishedRunTwo() == 1){
			array_push($this->results,'Average result(%) finished tests (Run 2)');
		}
	}


	public function getSelectableColumns() {
		$cols = array();

		$cols['result'] = array( 'txt' => $this->pl->txt('cols_result'), 'default' => true, 'width' => 'auto', 'sort_field' => 'result' );
		$cols['value'] = array( 'txt' => $this->pl->txt('cols_value'), 'default' => true, 'width' => 'auto', 'sort_field' => 'value' );

		return $cols;
	}


	public function addColumns() {
		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($this->isColumnSelected($k)) {
				if (isset($v['sort_field'])) {
					$sort = $v['sort_field'];
				} else {
					$sort = NULL;
				}
				$this->addColumn($v['txt'], $sort, $v['width']);
			}
		}
	}


	public function parseData() {
		$rows = array();
		$data = $this->construct_array();
		foreach ($data as $k => $v){
			$row['result'] = $k;
			$row['value'] = $v;

			$rows[] = $row;
		}
		$this->setData($rows);

		return $rows;
	}


	/**
	 * @param array $a_set
	 */
	public function fillRow($a_set) {
		foreach ($this->getSelectableColumns() as $k => $v){
			if($this->isColumnSelected($k)){
				if($a_set[$k]) {
					$this->tpl->setCurrentBlock('td');
					$this->tpl->setVariable('VALUE',$a_set[$k]);
					$this->tpl->parseCurrentBlock();
				}
				else{
					$this->tpl->setCurrentBlock('td');
					$this->tpl->setVariable('VALUE','&nbsp;');
					$this->tpl->parseCurrentBlock();

				}
			}
		}
	}
}