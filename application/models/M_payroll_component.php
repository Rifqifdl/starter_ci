<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_payroll_component extends CI_Model
{

	private $column_search = array('payroll_component.id_payroll_component','payroll_component.id_employee', 'payroll_component.pot_uang_makan', 'payroll_component.uang_makan_bersih', 'payroll_component.total_absen', 'payroll_component.id_payroll','payroll_component.total',  'payroll_component.total_overtime', 'payroll_component.total_dayoff', 'payroll_component.piutang', 'payroll_component.bonus','payroll_component.tanggal_gajian', 'payroll_component.potongan_absen',   'payroll_component.absen_hari',   'payroll_component.total_potongan' ,'payroll_component.description',  'employee.name', 'employee.id_product', 'employee.id_division', 'employee.basic_salary', 'employee.nip', 'employee.uang_makan', 'products.name_product', 'division.name_division');
	private $column_order = array('payroll_component.id_payroll_component','payroll_component.id_employee', 'payroll_component.pot_uang_makan', 'payroll_component.uang_makan_bersih', 'payroll_component.total_absen', 'payroll_component.id_payroll','payroll_component.total',  'payroll_component.total_overtime', 'payroll_component.total_dayoff', 'payroll_component.piutang', 'payroll_component.bonus','payroll_component.tanggal_gajian', 'payroll_component.potongan_absen', 'payroll_component.absen_hari',   'payroll_component.total_potongan' ,'payroll_component.description', 'employee.name', 'employee.id_product', 'employee.id_division', 'employee.basic_salary', 'employee.nip', 'employee.uang_makan', 'products.name_product', 'division.name_division');
	private $order = array('payroll_component.tanggal_gajian' => 'asc');

	public function findAll_get()
	{
		return $this->db->get('payroll_component')->result_array();
	}


	public function findById_get($id)
	{
		return $this->db->get_where('payroll_component', ['id_payroll_component' => $id])->row_array();
	}


	public function findByPayrollId_get($id)
	{
		return $this->db->get_where('payroll_component', ['id_payroll' => $id])->result_array();
	}


	public function findAllWithJoin_get()
	{
		$this->db->select('payroll_component.id_payroll_component, payroll_component.id_employee, payroll_component.total_absen, payroll_component.id_payroll, payroll_component.total, payroll_component.total_overtime, payroll_component.total_dayoff, payroll_component.piutang, payroll_component.bonus,payroll_component.tanggal_gajian, payroll_component.potongan_absen,   payroll_component.absen_hari,  payroll_component.total_potongan, payroll_component.description, employee.name, employee.id_product, employee.id_division, employee.basic_salary,  employee.nip, employee.uang_makan, products.name_product, division.name_division');
		$this->db->from('payroll_component');
		$this->db->join('employee', 'employee.id_employee = payroll_component.id_employee', 'left');
		$this->db->join('products', 'products.id_product = employee.id_product', 'left');
		$this->db->join('division', 'division.id_division = employee.id_division', 'left');
		$this->db->order_by('payroll_component.tanggal_gajian', 'ASC');

		return $this->db->get()->result_array();
	}


	public function update_post($id, $data)
	{
		$this->db->where('id_payroll_component', $id);
		return $this->db->update('payroll_component', $data);
	}


	public function delete($id)
	{
		return $this->db->delete('payroll_component', ['id_payroll_component' => $id]);
	}

	public function findByPayroll_get($id)
	{
		return $this->db->get_where('payroll_component', ['id_payroll' => $id])->row_array();
	}


	public function findByEmployeeId_get($id)
	{
		return $this->db->get_where('payroll_component', ['id_employee' => $id])->result_array();
	}


	private function _filterDATE($type)
	{
		switch ($type) {
			case 'today':
				$this->db->where('DATE(payroll_component.tanggal_gajian) = CURDATE()');
				break;
			case 'tomorrow':
				$this->db->where('DATE(payroll_component.tanggal_gajian) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)');
				break;
			case 'this_week':
				$this->db->where('payroll_component.tanggal_gajian >=', date('Y-m-d', strtotime('monday this week')));
				$this->db->where('payroll_component.tanggal_gajian <=', date('Y-m-d', strtotime('sunday this week')));
				break;
			case 'next_week':
				$this->db->where('payroll_component.tanggal_gajian >=', date('Y-m-d', strtotime('monday next week')));
				$this->db->where('payroll_component.tanggal_gajian <=', date('Y-m-d', strtotime('sunday next week')));
				break;
			case 'this_month':
				$this->db->where('payroll_component.tanggal_gajian >=', date('Y-m-01'));
				$this->db->where('payroll_component.tanggal_gajian <=', date('Y-m-t'));
				break;
			case 'next_month':
				$this->db->where('payroll_component.tanggal_gajian >=', date('Y-m-01', strtotime('+1 month')));
				$this->db->where('payroll_component.tanggal_gajian <=', date('Y-m-t', strtotime('+1 month')));
				break;
			case 'this_year':
				$this->db->where('payroll_component.tanggal_gajian >=', date('Y-01-01'));
				$this->db->where('payroll_component.tanggal_gajian <=', date('Y-12-31'));
				break;
			case 'next_year':
				$this->db->where('payroll_component.tanggal_gajian >=', date('Y-01-01', strtotime('+1 year')));
				$this->db->where('payroll_component.tanggal_gajian <=', date('Y-12-31', strtotime('+1 year')));
				break;
			case 'custom':
				$startDate = $this->input->post('startDate');
				$endDate = $this->input->post('endDate');
				if ($startDate && $endDate) {
					$this->db->where('payroll_component.tanggal_gajian >=', $startDate);
					$this->db->where('payroll_component.tanggal_gajian <=', $endDate);
				}
				break;
			default:
				break;
		}
	}


	public function getPayrollComponentDataCore_get()
	{

		$employee = $this->input->post('employee', true);
		$product = $this->input->post('product', true);
		$payroll = $this->input->post('payroll',true);
		$code = $this->input->post('code',true);

		$this->db->select('payroll_component.id_payroll_component, payroll_component.gaji_pokok, payroll_component.id_employee, payroll_component.pot_uang_makan, payroll_component.uang_makan_bersih, payroll_component.id_payroll, payroll_component.total_absen, payroll_component.total, payroll_component.total_overtime, payroll_component.total_dayoff, payroll_component.piutang, payroll_component.bonus,payroll_component.tanggal_gajian, payroll_component.potongan_absen,   payroll_component.absen_hari,   payroll_component.total_potongan, payroll_component.description, payroll_component.periode_gajian, payroll_component.total_potongan_telat,  payroll_component.gaji_bersih, payroll_component.basic_uang_makan, employee.name, employee.id_product, employee.id_division, employee.basic_salary, employee.nip, employee.no_hp, employee.uang_makan, products.name_product, products.logo, division.name_division, position.name_position, tax_config.hasil_pph, payroll.code_payroll, payroll_component.tunjangan_bpjs');
		if (!empty($payroll) ) {
			$this->db->where('payroll_component.id_payroll', $payroll);
		}
		if (!empty($code) ) {
			$this->db->where('payroll.code_payroll', $code);
		}
		if ($employee != 'false') {
			$this->db->where('payroll_component.id_employee', $employee);
		}
		$this->db->from('payroll_component');
		$this->db->join('employee', 'employee.id_employee = payroll_component.id_employee', 'left');
		$this->db->join('products', 'products.id_product = employee.id_product', 'left');
		$this->db->join('position', 'position.id_position = employee.id_position', 'left');
		$this->db->join('division', 'division.id_division = employee.id_division', 'left');
		$this->db->join('payroll', 'payroll.id_payroll = payroll_component.id_payroll', 'left');
		$this->db->join('tax_config', 'tax_config.id_payroll_component = payroll_component.id_payroll_component', 'left');

		if(!empty($product) && $product != 'all'){
			$this->db->where('products.id_product', $product);
		}


		$i = 0;
		foreach ($this->column_search as $item) {
			if (@$_POST['search']['value']) {
				if ($i === 0) {
					$this->db->group_start();
					$this->db->like($item, $_POST['search']['value']);
				} else {
					$this->db->or_like($item, $_POST['search']['value']);
				}
				if (count($this->column_search) - 1 === $i) {
					$this->db->group_end();
				}
			}
			$i++;
		}

		if (isset($_POST['order'])) {
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else {
			$this->db->order_by(key($this->order), $this->order[key($this->order)]);
		}

	}


	public function get_datatables()
	{
		$this->getPayrollComponentDataCore_get();
		if (@$_POST['length'] != -1) {
			$this->db->limit(@$_POST['length'], @$_POST['start']);
		}
		$query = $this->db->get();
		return $query->result_array();
	}


	public function count_filtered()
	{
		$this->getPayrollComponentDataCore_get();
		$query = $this->db->get();
		return $query->num_rows();
	}

	
	public function count_all()
	{
		$this->db->from('payroll_component');
		return $this->db->count_all_results();
	}


	public function create_batch_post($data)
	{
		if ($this->db->insert_batch('payroll_component', $data)) {
			return true;
		} else {
			return false;
		}
	}


	/*public function findTotalSalaryByEmployeeId_get($employee_id) {
		$this->db->select('COALESCE(SUM(employee.basic_salary), 0) AS total_penghasilan', false);
		$this->db->from('payroll_component');
		$this->db->where('id_employee', $employee_id);
		$this->db->where('YEAR(tanggal_gajian) =', date('Y')); // Menggunakan tanggal_gajian
		$this->db->join('employee', 'employee.id_employee = payroll_component.id_employee', 'left');

		$query = $this->db->get()->row();

		return intval($query->total_penghasilan);
	}*/

	public function findTotalSalaryByEmployeeId_get($employee_id) {
		$this->db->select('COALESCE(SUM(pc.gaji_pokok), 0) AS total_penghasilan', false);
		$this->db->from('payroll_component pc');
		$this->db->join('payroll', 'payroll.id_payroll = pc.id_payroll', 'left');
		$this->db->where('pc.id_employee', $employee_id);
		$this->db->where('YEAR(pc.tanggal_gajian)', date('Y')); // Pastikan tanggal_gajian ada di payroll_component
		$this->db->where('payroll.include_pph', 1);


		$query = $this->db->get()->row();
		return intval($query->total_penghasilan);
	}

	

	public function create_post($data)
	{
		if ($this->db->insert('payroll_component', $data)) {
			return $this->db->insert_id();
		} else {
			return false;
		}
	}



}
