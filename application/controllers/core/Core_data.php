<?php
class Core_data extends MY_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('M_employees');
        $this->load->model('M_piutang');
        $this->load->model('M_purchases');
        $this->load->model('M_purchase_piutang');
        $this->load->model('M_izin');
        $this->load->model('M_overtime');
        $this->load->model('M_leave');
        $this->load->model('M_day_off');
        $this->load->model('M_holyday');
        $this->load->model('M_payroll');
        $this->load->model('M_payroll_component');
        $this->load->model('M_schedule');
        $this->load->model('M_rekap');
        $this->load->model('M_attendance');
        $this->load->model('M_service_teknisi');
        $this->load->model('M_batch_uang_makan');
        $this->load->model('M_products');
        $this->load->model('M_uang_makan');
        $this->load->model('M_schedule');
    }


	public function data_piutang()
	{
		$tenor = $this->input->post('tanggal_pelunasan');
		$type = $this->input->post('type');
		$status_piutang = $this->input->post('status_piutang');

		$list = $this->M_piutang->get_datatables();

		$data = [];
		$no = $this->input->post('start');

		foreach($list as $item) {

			$type_ten = '';
			if($item['type_tenor'] == 1) {
				$type_ten = 'Hari';
			} else if($item['type_tenor'] == 2) {
				$type_ten = 'Minggu';
			}else if($item['type_tenor'] == 3) {
				$type_ten = 'Bulan';
			}else if($item['type_tenor'] == 4) {
				$type_ten = 'Tahun';
			}
			$action = $status_piutang == 1 ?
						'   
                        <button 
                            class="btn btn-warning btn-sm mb-2 rounded-pill btn-pay-piutang" style="width : 70px"
                            data-bs-toggle="modal" 
                            data-bs-target="#logPiutangModal"
                            data-id_piutang="'.htmlspecialchars($item['id_piutang']).'"
                            data-name_log="'.htmlspecialchars($item['name']).'"
                            data-totals_log="'.htmlspecialchars($item['amount_piutang']).'"
                            data-paydate_log="'.htmlspecialchars($item['piutang_date']).'"
                            data-tgl_lunas_log="'.htmlspecialchars($item['tgl_lunas']).'">
                             LOG
                        </button>
                        <button class="btn gradient-btn-delete btn-sm mb-2 rounded-pill btn-delete-piutang" onclick="handleDeleteButton('.htmlspecialchars($item['id_piutang']).')" style="width : 70px">
                            DELETE
                        </button>
						'
						:
						'   
                        <button 
                            class="btn btn-warning btn-sm mb-2 rounded-pill btn-pay-piutang" style="width : 70px"
                            data-bs-toggle="modal" 
                            data-bs-target="#payPiutangModal"
                            data-id_piutang="'.htmlspecialchars($item['id_piutang']).'"
                            data-remaining_piutang="'.htmlspecialchars($item['remaining_piutang']).'"
                            data-tgl_lunas="'.htmlspecialchars($item['tgl_lunas']).'"
                            data-tenor_piutang="'.htmlspecialchars($item['tenor_piutang']).'"
                            data-angsuran="'.htmlspecialchars($item['angsuran']).'"
                            data-type_tenor="'.htmlspecialchars($item['type_tenor']).'">
                             PAY
                        </button>
                        <button class="btn gradient-btn-delete btn-sm mb-2 rounded-pill btn-delete-piutang" onclick="handleDeleteButton('.htmlspecialchars($item['id_piutang']).')" style="width : 70px">
                            DELETE
                        </button>
						';
						
			$status = $item['status_piutang'] == 2 ?
						'
						  <td>
							  <span class="badge gradient-btn-unpaid btn-sm " style="width : 50px">
								  Unpaid
							  </span>
						  </td>
					    '
						:
						'
						  <td>
							  <span class="badge gradient-btn-paid btn-sm " style="width : 50px">
								  Paid
							  </span>
						  </td>
					   ';

			$row = [];
			$row[] = ++$no;
			$row[] = date('d M Y', strtotime($item['piutang_date']));
			$row[] = $item['name'];
			$row[] = $item['type_piutang'] == '2' ? 'Kasbon' : 'Pinjaman';
			$row[] = $item['tenor_piutang'] . ' ' . $type_ten;
			$row[] = 'Tanggal '.$item['jatuh_tempo'];
			$row[] = date('d M Y', strtotime($item['tgl_lunas']));
			$row[] = 'Rp.'. number_format($item['amount_piutang'], 0 , ',', '.');
			$row[] = 'Rp.'. number_format($item['remaining_piutang'], 0 , ',', '.');
			$row[] = $status;
			$row[] = 'Rp.'. number_format($item['angsuran'], 0 , ',', '.');
			$row[] = $item['description_piutang'];
			$row[] = $action;
			$data[] = $row;
		}

		$output = [
			"draw" =>@$_POST['draw'],
			"recordsTotal" => $this->M_piutang->count_all(),
			"recordsFiltered" => $this->M_piutang->count_filtered(),
			"tenor" => $tenor,
			"type" => $type,
			"data" => $data,
		];

		echo json_encode($output);
	}


	public function data_purchases()
	{
		$option = $this->input->post('option');
		$startDate = $this->input->post('startDate');
		$endDate = $this->input->post('endDate');
		$status = $this->input->post('status_purchases');

		$list = $this->M_purchases->get_datatables();

		$data = [];
		$no = $this->input->post('start');

		foreach($list as $item) {
			$action = $status == 1 ?
					'   
                        <button 
                            class="btn btn-warning btn-sm mb-2 rounded-pill btn-pay-piutang" style="width : 70px"
                            data-bs-toggle="modal" 
                            data-bs-target="#logModal"
                            data-id-supplier="'.htmlspecialchars($item['id_purchases']).'"
                            data-final-amount="'.htmlspecialchars($item['final_amount']).'"
                            data-remaining-amount="'.htmlspecialchars($item['remaining_amount']).'">
                            LOG
                        </button>
                        <button class="btn gradient-btn-delete btn-sm mb-2 rounded-pill btn-delete-pc" onclick="handleDeletePurchaseButton('.htmlspecialchars($item['id_purchases']).')" style="width : 70px">
                            DELETE
                        </button>
                     '
					:
					'   
                        <button 
                            class="btn btn-warning btn-sm mb-2 rounded-pill btn-pay-piutang" style="width : 70px"
                            data-bs-toggle="modal" 
                            data-bs-target="#payModal"
                            data-id-supplier="'.htmlspecialchars($item['id_purchases']).'"
                            data-final-amount="'.htmlspecialchars($item['final_amount']).'"
                            data-remaining-amount="'.htmlspecialchars($item['remaining_amount']).'">
                             PAY
                        </button>
                        <button class="btn gradient-btn-delete btn-sm mb-2 rounded-pill btn-delete-pc" onclick="handleDeletePurchaseButton('.htmlspecialchars($item['id_purchases']).')" style="width : 70px">
                            DELETE
                        </button>
                     ';

			$status = $item['status_purchases'] == 0 ?
							'
                              <td>
                                  <span class="badge gradient-btn-unpaid btn-sm " style="width : 50px">
                                      Unpaid
                                  </span>
                              </td>
                           '
							:
							'
                              <td>
                                  <span class="badge gradient-btn-paid btn-sm " style="width : 50px">
                                      Paid
                                  </span>
                              </td>
                           ';

			$type = $item['payment_type'] == 1 ?
							'
                              <td>
                                 <span class="badge gradient-btn-debit btn-sm" style="width:50px">
                                 	Debit
                           		 </span>
                              </td>
                           	'
							:
							'
                              <td>
                                  <span class="badge gradient-btn-kredit btn-sm" style="width:50px">
                                 	Kredit
                            	  </span>
                              </td>
                           ';

			$row = [];
			$row[] = ++$no;
			$row[] = date('d M Y', strtotime($item['input_at']));
			$row[] = $item['name_supplier'];
			$row[] = 'Rp.'. number_format($item['total_amount'], 0 , ',', '.');
			$row[] = 'Rp.'. number_format($item['pot_amount'], 0 , ',', '.');
			$row[] = 'Rp.'. number_format($item['final_amount'], 0 , ',', '.');
			$row[] = 'Rp.'. number_format($item['remaining_amount'], 0 , ',', '.');
			$row[] = $status;
			$row[] = $type;
			$row[] = date('d M Y', strtotime($item['jatuh_tempo']));
			$row[] = $item['description'];
			$row[] = $action;
			$data[] = $row;
		}

		$output = [
			"draw" =>@$_POST['draw'],
			"recordsTotal" => $this->M_purchases->count_all(),
			"recordsFiltered" => $this->M_purchases->count_filtered(),
			"option" => $option,
			"startDate" => $startDate,
			"endDate" => $endDate,
			"data" => $data,
		];

		echo json_encode($output);
	}


	public function data_izin()
	{
		$option = $this->input->post('option');
		$startDate = $this->input->post('startDate');
		$endDate = $this->input->post('endDate');
		$employee = $this->input->post('employee');


		$list = $this->M_izin->get_datatables();

		$data = [];
		$no = $this->input->post('start');

		foreach($list as $item) {
			$action = $employee == 'false' ?
				     '     
                        <a href="javascript:void(0)" onclick="updateBukti(this)" class="btn btn-success btn-sm rounded-pill btn-stts mb-2" style="width : 90px"
								data-id_izin="'. $item['id_izin'].'"
								data-bukti_surat_sakit="'. $item['bukti_surat_sakit'].'">
								 ADD SKD
						</a>
                        <button class="btn gradient-btn-delete btn-sm mb-2 rounded-pill btn-delete-izin" onclick="handleDeleteIzinButton('.htmlspecialchars($item['id_izin']).')" style="width : 90px">
                            DELETE
                        </button>
                     ':
					'     
					<a href="javascript:void(0)" onclick="updateBukti(this)" class="btn btn-success btn-sm rounded-pill btn-stts mb-2" style="width : 90px"
							data-id_izin="'. $item['id_izin'].'"
							data-bukti_surat_sakit="'. $item['bukti_surat_sakit'].'">
							 ADD SKD
					</a>
					';

			if($item['status'] == 1 && $employee == 'false'){
				$status =
						  ' 	
							<td>
							   <a href="javascript:void(0)" onclick="setStatusIzin(this)" class="btn btn-danger btn-sm rounded-pill btn-stts" style="width : 140px"
								data-id_izin="'. $item['id_izin'].'"
								data-id_employee="'. $item['id_employee'].'"
								data-tanggal_izin="'. $item['tanggal_izin'].'"
								data-end_date="'. $item['end_date'].'"
								data-status="'. $item['status'].'">
									<i class="bi bi-x-lg"></i> Disapprove
								</a>
							</td>
						  ';
			}
			else if($item['status'] == 2 && $employee == 'false'){
				$status =  '
                             <td>
							   <a href="javascript:void(0)" onclick="setStatusIzin(this)" class="btn btn-primary btn-sm rounded-pill btn-stts" style="width : 120px"
								data-id_izin="'. $item['id_izin'].'"
								data-id_employee="'. $item['id_employee'].'"
								data-tanggal_izin="'. $item['tanggal_izin'].'"
								data-end_date="'. $item['end_date'].'"
								data-status="'. $item['status'].'">
									<i class="ti ti-check"></i> Approve
								</a>
							</td>
                           ';
			}
			else if ($item['status'] == 3 && $employee == 'false') {
				$status =  '
                             <td>
							   <a a href="javascript:void(0)" onclick="setStatusIzin(this)" class="btn btn-info btn-sm rounded-pill btn-stts" style="width : 120px"
								data-id_izin="'. $item['id_izin'].'"
								data-id_employee="'. $item['id_employee'].'"
								data-tanggal_izin="'. $item['tanggal_izin'].'"
								data-end_date="'. $item['end_date'].'"
								data-status="'. $item['status'].'">
									<i class="bi bi-clock-history"></i> Pending
								</a>
							</td>
                           ';
			}
			else if($item['status'] == 1 && $employee != 'false'){
				$status =
					' 	
							<td>
							   <button class="btn btn-danger btn-sm rounded-pill btn-stts" style="width : 140px" disabled >
									<i class="bi bi-x-lg"></i> Disapprove
								</button>
							</td>
						  ';
			}
			else if($item['status'] == 2 && $employee != 'false'){
				$status =  '
                             <td>
							   <button class="btn btn-primary btn-sm rounded-pill btn-stts" style="width : 120px" disabled >
									<i class="ti ti-check"></i> Approve
								</button>
							</td>
                           ';
			}
			else if ($item['status'] == 3 && $employee != 'false') {
				$status =  '
                             <td>
							   <button  class="btn btn-info btn-sm rounded-pill btn-stts" style="width : 120px" disabled >
									<i class="bi bi-clock-history"></i> Pending
								</button>
							</td>
                           ';
			}

			$bukti = $item['bukti_surat_sakit'] == '-' ? '<td>
															<span> - </span>
															</td>
															':
															'<td>
																<img src="' . base_url('uploads/bukti/compressed/' . $item['bukti_surat_sakit']) . '"  alt="Logo" width="50" style="cursor: pointer;"  onclick="showImageModal(\'' . base_url('uploads/bukti/compressed/' . $item['bukti_surat_sakit']) . '\')">
															</td>';

			$row = [];
			$row[] = ++$no;
			$row[] = date('d M Y', strtotime($item['input_at']));
			$row[] = $item['name'];
			$row[] = $item['name_product'];
			$row[] = $item['name_division'];
			$row[] = $item['type_day'] == 2 ? date('d M Y', strtotime($item['tanggal_izin'])).' - '.date('d M Y', strtotime($item['end_date'])) : date('d M Y', strtotime($item['tanggal_izin']));
			$row[] = $item['alasan_izin'];
			$row[] = $bukti;
			$row[] = $status;
			$row[] = $item['description'];
			$row[] = $action;
			$data[] = $row;
		}

		$output = [
			"draw" =>@$_POST['draw'],
			"recordsTotal" => $this->M_izin->count_all(),
			"recordsFiltered" => $this->M_izin->count_filtered(),
			"option" => $option,
			"startDate" => $startDate,
			"endDate" => $endDate,
			"data" => $data,
		];

		echo json_encode($output);
	}


	public function data_overtime()
	{
		$option = $this->input->post('option');
		$startDate = $this->input->post('startDate');
		$endDate = $this->input->post('endDate');
		$is = $this->input->post('is');
		$employee = $this->input->post('employee');


		$list = $this->M_overtime->get_datatables();

		$status ='';

		$data = [];
		$no = $this->input->post('start');

		foreach($list as $item) {
			$action = $is == 1 || $is == 2 ?
					'  
                        <button class="btn gradient-btn-delete btn-sm mb-2 rounded-pill btn-delete-overtime" onclick="handleDeleteOvertimeButton('.htmlspecialchars($item['id_overtime']).')" style="width : 70px">
                            DELETE
                        </button>
                     ':
					'';

			if($item['status'] == 1 && $is !=3 && $employee == 'false'){
				$status =
					' 	
							<td>
							   <a href="javascript:void(0)" onclick="setStatusOvertime(this)" class="btn btn-danger btn-sm rounded-pill btn-stts" style="width : 140px"
								data-id_overtime="'. $item['id_overtime'].'"
								data-status="'. $item['status'].'">
									<i class="bi bi-x-lg"></i> Disapprove
								</a>
							</td>
						  ';
			}
			else if($item['status'] == 2 && $is !=3 && $employee == 'false'){
				$status =  '
                             <td>
							   <a href="javascript:void(0)" onclick="setStatusOvertime(this)" class="btn btn-primary btn-sm rounded-pill btn-stts" style="width : 120px"
								data-id_overtime="'. $item['id_overtime'].'"
								data-status="'. $item['status'].'">
									<i class="ti ti-check"></i> Approve
								</a>
							</td>
                           ';
			}
			else if ($item['status'] == 3 && $is !=3 && $employee == 'false') {
				$status =  '
                             <td>
							   <a href="javascript:void(0)" onclick="setStatusOvertime(this)" class="btn btn-info btn-sm rounded-pill btn-stts" style="width : 120px"
								data-id_overtime="'. $item['id_overtime'].'"
								data-status="'. $item['status'].'">
									<i class="bi bi-clock-history"></i> Pending
								</a>
							</td>
                           ';
			}
			else if($item['status'] == 1 && $is ==3 && $employee != 'false'){
				$status =
					' 	
							<td>
							   <button  class="btn btn-danger btn-sm rounded-pill btn-stts" style="width : 140px" disabled>
									<i class="bi bi-x-lg"></i> Disapprove
								</button>
							</td>
						  ';
			}
			else if($item['status'] == 2 &&  $is ==3 && $employee != 'false'){
				$status =  '
                             <td>
							   <button class="btn btn-primary btn-sm rounded-pill btn-stts" style="width : 120px" disabled>
									<i class="ti ti-check"></i> Approve
								</button>
							</td>
                           ';
			}
			else if ($item['status'] == 3 &&  $is ==3 && $employee != 'false') {
				$status =  '
                             <td>
							   <button class="btn btn-info btn-sm rounded-pill btn-stts" style="width : 120px" disabled>
									<i class="bi bi-clock-history"></i> Pending
								</button>
							</td>
                           ';
			}



			$row = [];
			$row[] = ++$no;
			$row[] = date('d M Y', strtotime($item['input_at']));
			$row[] = $item['name'];
			$row[] = $item['name_product'];
			$row[] = $item['name_division'];
			$row[] = date('d M Y', strtotime($item['tanggal']));;
			$row[] = $item['start'];
			$row[] = $item['end'];
			$row[] =  number_format($item['time_spend'], 0 , ',', '.') .' Jam';
			$row[] =  'Rp.'.number_format($item['pay'], 0 , ',', '.');
			$row[] = $status;
			$row[] = $item['description'];
			$row[] = $action;
			$data[] = $row;
		}

		$output = [
			"draw" =>@$_POST['draw'],
			"recordsTotal" => $this->M_overtime->count_all(),
			"recordsFiltered" => $this->M_overtime->count_filtered(),
			"option" => $option,
			"startDate" => $startDate,
			"endDate" => $endDate,
			"data" => $data,
		];

		echo json_encode($output);
	}


	public function data_leave()
	{
		$option = $this->input->post('option');
		$startDate = $this->input->post('startDate');
		$endDate = $this->input->post('endDate');
		$is = $this->input->post('is');
		$employee = $this->input->post('employee');

		$status = '';

		$list = $this->M_leave->get_datatables();

		$data = [];
		$no = $this->input->post('start');

		foreach($list as $item) {
			$action = $is == 1 || $is == 2  ?
					'   
                        <button class="btn gradient-btn-delete btn-sm mb-2 rounded-pill btn-delete-leave" onclick="handleDeleteLeaveButton('.htmlspecialchars($item['id_cuti']).')" style="width : 70px">
                            DELETE
                        </button>
                     ':
					'-';

			$endDay = $item['end_day'] == null ? '-' :  date('d M Y', strtotime($item['end_day']));
			if($item['status'] == 1 && $is !=3 && $employee == 'false'){
				$status =
					' 	
							<td>
							   <a href="javascript:void(0)" onclick="setStatusLeave(this)" class="btn btn-danger btn-sm rounded-pill btn-stts" style="width : 140px"
								data-id_cuti="'. $item['id_cuti'].'"
								data-type="'. $item['type'].'"
								data-id_employee="'. $item['id_employee'].'"
								data-start_day="'. $item['start_day'].'"
								data-end_day="'. $endDay.'"
								data-status="'. $item['status'].'">
									<i class="bi bi-x-lg"></i> Disapprove
								</a>
							</td>
						  ';
			}
			else if($item['status'] == 2 && $is !=3 && $employee == 'false'){
				$status =  '
                             <td>
							   <a href="javascript:void(0)" onclick="setStatusLeave(this)" class="btn btn-primary btn-sm rounded-pill btn-stts" style="width : 120px"
								data-id_cuti="'. $item['id_cuti'].'"
								data-type="'. $item['type'].'"
								data-id_employee="'. $item['id_employee'].'"
								data-start_day="'. $item['start_day'].'"
								data-end_day="'. $endDay.'"
								data-status="'. $item['status'].'">
									<i class="ti ti-check"></i> Approve
								</a>
							</td>
                           ';
			}
			else if ($item['status'] == 3 && $is !=3 && $employee == 'false') {
				$status =  '
                             <td>
							   <a a href="javascript:void(0)" onclick="setStatusLeave(this)" class="btn btn-info btn-sm rounded-pill btn-stts" style="width : 120px"
								data-id_cuti="'. $item['id_cuti'].'"
								data-type="'. $item['type'].'"
								data-id_employee="'. $item['id_employee'].'"
								data-start_day="'. $item['start_day'].'"
								data-end_day="'. $endDay.'"
								data-status="'. $item['status'].'">
									<i class="bi bi-clock-history"></i> Pending
								</a>
							</td>
                           ';
			}
			else if($item['status'] == 1 && $is ==3 && $employee != 'false'){
				$status =
						' 	
							<td>
							   <button class="btn btn-danger btn-sm rounded-pill btn-stts" style="width : 140px" disabled>
									<i class="bi bi-x-lg"></i> Disapprove
								</button>
							</td>
						  ';
			}
			else if($item['status'] == 2 && $is ==3 && $employee != 'false'){
				$status =  '
                             <td>
							   <button class="btn btn-primary btn-sm rounded-pill btn-stts" style="width : 120px" disabled>
									<i class="ti ti-check"></i> Approve
								</button>
							</td>
                           ';
			}
			else if ($item['status'] == 3 && $is ==3 && $employee != 'false') {
				$status =  '
                             <td>
							   <button class="btn btn-info btn-sm rounded-pill btn-stts" style="width : 120px" disabled>
									<i class="bi bi-clock-history"></i> Pending
								</button>
							</td>
                           ';
			}



			$row = [];
			$row[] = ++$no;
			$row[] = date('d M Y', strtotime($item['input_at']));
			$row[] = $item['name'];
			$row[] = $item['name_product'];
			$row[] = $item['name_division'];
			$row[] = $item['type'] == 1 ? 'Single Day' : 'Multiple Days';
			$row[] = $item['total_days'] . ' Hari' ;
			$row[] =  date('d M Y', strtotime($item['start_day']));
			$row[] =  $endDay;
			$row[] = $item['description'];
			$row[] = $status;
			$row[] = $action;
			$data[] = $row;
		}

		$output = [
			"draw" =>@$_POST['draw'],
			"recordsTotal" => $this->M_leave->count_all(),
			"recordsFiltered" => $this->M_leave->count_filtered(),
			"option" => $option,
			"startDate" => $startDate,
			"endDate" => $endDate,
			"data" => $data,
		];

		echo json_encode($output);
	}


	public function data_day_off()
	{
		$option = $this->input->post('option');
		$startDate = $this->input->post('startDate');
		$endDate = $this->input->post('endDate');
		$is = $this->input->post('is');
		$id = $this->input->post('employee');

		$list = $this->M_day_off->get_datatables();
		$status  = '';

		$data = [];
		$no = $this->input->post('start');

		foreach($list as $item) {
			$action = $is == 1 || $is == 2 ?
					 '   
                        <button class="btn gradient-btn-delete btn-sm mb-2 rounded-pill btn-delete-day_off" onclick="handleDeleteDayoffButton('.htmlspecialchars($item['id_day_off']).')" style="width : 70px">
                            DELETE
                        </button>
                     ':
					'-';


			if($item['status'] == 1 && $id == 'false' && $is !=3){
				$status =
					' 	
							<td>
							   <a href="javascript:void(0)" onclick="setStatusDayoff(this)" class="btn btn-danger btn-sm rounded-pill btn-stts" style="width : 140px"
								data-id_day_off="'. $item['id_day_off'].'"
								data-id_employee="'. $item['id_employee'].'"
								data-tgl_day_off="'. $item['tgl_day_off'].'"
								data-status="'. $item['status'].'">
									<i class="bi bi-x-lg"></i> Disapprove
								</a>
							</td>
						  ';
			}
			else if($item['status'] == 2 &&  $id == 'false'  && $is !=3){
				$status =  '
                             <td>
							   <a href="javascript:void(0)" onclick="setStatusDayoff(this)" class="btn btn-primary btn-sm rounded-pill btn-stts" style="width : 120px"
								data-id_day_off="'. $item['id_day_off'].'"
								data-id_employee="'. $item['id_employee'].'"
								data-tgl_day_off="'. $item['tgl_day_off'].'"
								data-status="'. $item['status'].'">
									<i class="ti ti-check"></i> Approve
								</a>
							</td>
                           ';
			}
			else if ($item['status'] == 3 &&  $id == 'false'  && $is !=3) {
				$status =  '
                             <td>
							   <a a href="javascript:void(0)" onclick="setStatusDayoff(this)" class="btn btn-info btn-sm rounded-pill btn-stts" style="width : 120px"
								data-id_day_off="'. $item['id_day_off'].'"
								data-id_employee="'. $item['id_employee'].'"
								data-tgl_day_off="'. $item['tgl_day_off'].'"
								data-status="'. $item['status'].'">
									<i class="bi bi-clock-history"></i> Pending
								</a>
							</td>
                           ';
			}
			else if($item['status'] == 1 &&  $id != 'false'  && $is ==3){
				$status =
					' 	
							<td>
							   <button class="btn btn-danger btn-sm rounded-pill btn-stts" style="width : 140px" disabled="">
									<i class="bi bi-x-lg"></i> Disapprove
								</button>
							</td>
						  ';
			}
			else if($item['status'] == 2 && $id != 'false' && $is ==3){
				$status =  '
                             <td>
							   <button class="btn btn-primary btn-sm rounded-pill btn-stts" style="width : 120px">
									<i class="ti ti-check"></i> Approve
								</button>
							</td>
                           ';
			}
			else if ($item['status'] == 3 && $id != 'false' && $is ==3) {
				$status =  '
                             <td>
							   <button class="btn btn-info btn-sm rounded-pill btn-stts" style="width : 120px" disabled>
									<i class="bi bi-clock-history"></i> Pending
								</button>
							</td>
                           ';
			}



			$row = [];
			$row[] = ++$no;
			$row[] = date('d M Y', strtotime($item['input_at']));
			$row[] = $item['name'];
			$row[] = $item['name_product'];
			$row[] = $item['name_division'];
			$row[] = date('d M Y', strtotime($item['tgl_day_off']));
			$row[] = $item['description'];
			$row[] = $status;
			$row[] = $action;
			$data[] = $row;
		}

		$output = [
			"draw" =>@$_POST['draw'],
			"recordsTotal" => $this->M_day_off->count_all(),
			"recordsFiltered" => $this->M_day_off->count_filtered(),
			"option" => $option,
			"startDate" => $startDate,
			"endDate" => $endDate,
			"data" => $data,
		];

		echo json_encode($output);
	}


	public function data_holyday()
	{
		$option = $this->input->post('option');
		$startDate = $this->input->post('startDate');
		$endDate = $this->input->post('endDate');


		$list = $this->M_holyday->get_datatables();

		$data = [];
		$no = $this->input->post('start');

		foreach($list as $item) {
			$action =
					'   
                        <button class="btn gradient-btn-delete btn-sm mb-2 rounded-pill btn-delete-holyday" onclick="handleDeleteHolydayButton('.htmlspecialchars($item['id_holyday']).')" style="width : 70px">
                            DELETE
                        </button>
                     ';

			$type_day =  $item['type_day'] == 1 ? 'Single Day' : 'Multiple Days';
			$jenis_libur =  $item['status_day'] == 2 ? 'Libur Minggu' : 'Libur Nasional';
			$end_day =  $item['type_day'] == 1 ? '-' : date('d M Y', strtotime($item['end_day']));

			$row = [];
			$row[] = ++$no;
			$row[] = $item['code_holyday'];
			$row[] = $item['name_product'];
			$row[] = $item['name_division'];
			$row[] = $type_day;
			$row[] = $jenis_libur;
			$row[] = date('d M Y', strtotime($item['date']));
			$row[] = $action;
			$data[] = $row;
		}

		$output = [
			"draw" =>@$_POST['draw'],
			"recordsTotal" => $this->M_holyday->count_all(),
			"recordsFiltered" => $this->M_holyday->count_filtered(),
			"option" => $option,
			"startDate" => $startDate,
			"endDate" => $endDate,
			"data" => $data,
		];

		echo json_encode($output);
	}


	public function data_schedule()
	{
		$option = $this->input->post('option');
		$startDate = $this->input->post('startDate');
		$endDate = $this->input->post('endDate');


		$list = $this->M_schedule->get_datatables();

		$data = [];
		$no = $this->input->post('start');

		foreach($list as $item) {
			$action =
				'   
                        <button class="btn gradient-btn-delete btn-sm mb-2 rounded-pill btn-delete-schedule" onclick="handleDeleteScheduleButton('.htmlspecialchars($item['id_schedule']).')" style="width : 70px">
                            DELETE
                        </button>
                     ';


			$row = [];
			$row[] = ++$no;
			$row[] = $item['name_workshift'];
			$row[] = $item['name'];
			$row[] = $item['status'];
			$row[] = date('d M Y', strtotime($item['waktu']));
			$row[] = $action;
			$data[] = $row;
		}

		$output = [
			"draw" =>@$_POST['draw'],
			"recordsTotal" => $this->M_schedule->count_all(),
			"recordsFiltered" => $this->M_schedule->count_filtered(),
			"option" => $option,
			"startDate" => $startDate,
			"endDate" => $endDate,
			"data" => $data,
		];

		echo json_encode($output);
	}


	public function data_payroll()
	{
		$option = $this->input->post('option');
		$startDate = $this->input->post('startDate');
		$endDate = $this->input->post('endDate');
		$groupByCode = $this->input->post('groupbycode');

		$list = $this->M_payroll->get_datatables();

		$data = [];
		$no = $this->input->post('start');


		foreach($list as $item) {
			if(!empty($groupByCode) || $groupByCode != ''){
				$action =
					'   
                      <a href="'. base_url('admin/payroll/detail_payroll?code='.$item['code_payroll']) .'" 
						   class="btn btn-warning btn-sm rounded-pill mb-1" 
						   style="width: 100px;">
						   DETAIL
						</a>
                     ';
				//                         <button class="btn  gradient-btn-delete btn-sm rounded-pill"
				//								onclick="handleDeletePayrollButton('. htmlspecialchars($item['id_payroll']) .')"
				//								style="width: 100px;">
				//							DELETE
				//						</button>
			} else {
				$action =
					'   
                      <a href="'. base_url('admin/payroll/detail_payroll?payroll='.$item['id_payroll']) .'" 
						   class="btn btn-warning btn-sm rounded-pill mb-1" 
						   style="width: 100px;">
						   DETAIL
						</a>
                         <button class="btn  gradient-btn-delete btn-sm rounded-pill" 
								onclick="handleDeletePayrollButton('. htmlspecialchars($item['id_payroll']) .')" 
								style="width: 100px;">
							DELETE
						</button>
                     ';
			}

			$piutang = $item['include_piutang'] == 1 ? '<span><i class="bi bi-check-circle-fill" style="color : green"></i></span>' : '<span><i class="bi bi-x-circle-fill" style="color : darkred"></i></span>';
			$finance_record = $item['include_finance_record'] == 1 ? '<span><i class="bi bi-check-circle-fill" style="color : green"></i></span>' : '<span><i class="bi bi-x-circle-fill" style="color : darkred"></i></span>';
			$telat = $item['include_potongan_telat'] == 1 ? '<span><i class="bi bi-check-circle-fill" style="color : green"></i></span>' : '<span><i class="bi bi-x-circle-fill" style="color : darkred"></i></span>';
			$uang_makan = $item['include_uang_makan'] == 1 ? '<span><i class="bi bi-check-circle-fill" style="color : green"></i></span>' : '<span><i class="bi bi-x-circle-fill" style="color : darkred"></i></span>';

			$row = [];
			$row[] = ++$no;
			$row[] = date('d M Y', strtotime($item['input_at']));
			$row[] = $item['code_payroll'];
			$row[] = $uang_makan;
			$row[] = $piutang;
			$row[] = $finance_record;
			$row[] = $telat;
			$row[] = date('d M Y', strtotime($item['periode_gajian'])) . '-' . date('d M Y', strtotime($item['tanggal_gajian']));
			$row[] = $action;
			$data[] = $row;
		}

		$output = [
			"draw" =>@$_POST['draw'],
			"recordsTotal" => $this->M_payroll->count_all(),
			"recordsFiltered" => $this->M_payroll->count_filtered(),
			"option" => $option,
			"startDate" => $startDate,
			"endDate" => $endDate,
			"data" => $data,
		];

		echo json_encode($output);
	}


	public function data_payroll_component()
	{

		$payroll = $this->input->post('payroll', true);
		$employee = $this->input->post('employee', true);
		$list = $this->M_payroll_component->get_datatables();


		$data = [];
		$no = $this->input->post('start');


		foreach($list as $item) {

			$periode = date('d-m-Y', strtotime($item['periode_gajian'])) . ' sampai ' . date('d-m-Y', strtotime($item['tanggal_gajian']));
			$jumlahGaji = 'Rp.' . number_format($item['gaji_bersih'], 0, ',', '.');
			$link = base_url('admin/payroll/payroll_employee');
			$nama = $item['name'];
			$nomor = preg_replace('/[^0-9]/', '', $item['no_hp']); // Pastikan hanya angka

		// Format nomor HP ke format internasional
			if (substr($nomor, 0, 1) == '0') {
				$nomor = '62' . substr($nomor, 1);
			}

			$pesan = "*Notifikasi | Penggajian | Sistem HR*\n\n"
				. "Halo $nama,\n\n"
				. "Gaji Anda untuk periode *$periode* sudah dikirim.\n\n"
				. "💰 *Jumlah Gaji:* $jumlahGaji\n\n"
				. "Silakan cek rekening Anda.\n\n"
				. "Untuk rincian lengkap, login ke akun karyawan Anda di:\n$link\n\n"
				. "Jika ada pertanyaan, hubungi HRD.\n\n"
				. "Terima kasih.\n\n"
				. "*Admin Finance*";

		// Gunakan urlencode sesuai format yang terbukti berhasil
			$pesanEncoded = urlencode($pesan);
			$nomorEncoded = urlencode("+$nomor");

		// Buat URL WhatsApp
			$url = "https://api.whatsapp.com/send/?phone=$nomorEncoded&text=$pesanEncoded&type=phone_number&app_absent=0";


			if($employee == 'false') {
				$action =
					'    <button 
                            class="btn btn-warning btn-sm mb-2 rounded-pill btn-rincian-gaji" style="width : 90px"
                            data-bs-toggle="modal" 
                            data-bs-target="#rincianModal"
                            data-id="'.htmlspecialchars($item['id_payroll_component']).'"
                            data-total-absent="'.htmlspecialchars($item['total_absen']).'"
                            data-total-lembur="'.htmlspecialchars($item['total_overtime']).'"
                            data-basic-salary="'.htmlspecialchars($item['gaji_pokok']).'"
                            data-total-dayoff="'.htmlspecialchars($item['total_dayoff']).'"
                            data-uang-makan="'.htmlspecialchars($item['basic_uang_makan']).'"
                            data-nip="'.htmlspecialchars($item['nip']).'"
                            data-name="'.htmlspecialchars($item['name']).'"
                            data-product="'.htmlspecialchars($item['name_product']).'"
                            data-divisi="'.htmlspecialchars($item['name_division']).'"
                            data-position="'.htmlspecialchars($item['name_position']).'"
                            data-tanggal-gajian="'.htmlspecialchars($item['tanggal_gajian']).'"
                            data-potongan-absen="'.htmlspecialchars($item['potongan_absen']).'"
                            data-absen-hari="'.htmlspecialchars($item['absen_hari']).'"
                            data-total-potongan="'.htmlspecialchars($item['total_potongan']).'"
                            data-gaji-bersih="'.htmlspecialchars($item['total']).'"
                            data-piutang="'.htmlspecialchars($item['piutang']).'"
                            data-periode-gajian="'.htmlspecialchars($item['periode_gajian']).'"
                            data-total-potongan-telat="'.htmlspecialchars($item['total_potongan_telat']).'"
                            data-pph="'.htmlspecialchars($item['hasil_pph']).'"
                            data-total-gaji-bersih="'.htmlspecialchars($item['gaji_bersih']).'"
                            data-code-payroll="'.htmlspecialchars($item['code_payroll']).'"
                            data-logo="'.htmlspecialchars($item['logo']).'"
                            data-uang-makan-bersih="'.htmlspecialchars($item['uang_makan_bersih']).'"
                            data-pot-uang-makan="'.htmlspecialchars($item['pot_uang_makan']).'"
                            data-bonus="'.htmlspecialchars($item['bonus']).'"
                            data-tunjangan-bpjs="'.htmlspecialchars($item['tunjangan_bpjs']).'">
                            RINCIAN
                        </button>
                        
                        <a class="btn btn-success btn-sm mb-2 rounded-pill btn-send-wa" href="'.$url.'"  target="_blank" style="width : 50px">
                            <span><i class="bi bi-whatsapp"></i></span>
                        </a>
                        
                       <button class="btn gradient-btn-delete btn-sm mb-2 rounded-pill btn-delete-payroll-component" 
							onclick="handleDeletePayrollComponentButton(\'' . 
								htmlspecialchars($item['id_payroll_component']) . '\', \'' . 
								htmlspecialchars($item['id_employee']) . '\', \'' . 
								htmlspecialchars($item['code_payroll']) . '\', \'' . 
								htmlspecialchars($item['gaji_bersih']) . '\')" 
							style="width: 70px">
							DELETE
						</button>

                        
                         
                     ';
			} else {
				$action =
					'    <button 
                            class="btn btn-warning btn-sm mb-2 rounded-pill btn-rincian-gaji" style="width : 90px"
                            data-bs-toggle="modal" 
                            data-bs-target="#rincianModal"
                            data-id="'.htmlspecialchars($item['id_payroll_component']).'"
                            data-total-absent="'.htmlspecialchars($item['total_absen']).'"
                            data-total-lembur="'.htmlspecialchars($item['total_overtime']).'"
                            data-basic-salary="'.htmlspecialchars($item['basic_salary']).'"
                            data-total-dayoff="'.htmlspecialchars($item['total_dayoff']).'"
                            data-uang-makan="'.htmlspecialchars($item['basic_uang_makan']).'"
                            data-nip="'.htmlspecialchars($item['nip']).'"
                            data-name="'.htmlspecialchars($item['name']).'"
                            data-product="'.htmlspecialchars($item['name_product']).'"
                            data-divisi="'.htmlspecialchars($item['name_division']).'"
                            data-position="'.htmlspecialchars($item['name_position']).'"
                            data-tanggal-gajian="'.htmlspecialchars($item['tanggal_gajian']).'"
                            data-potongan-absen="'.htmlspecialchars($item['potongan_absen']).'"
                            data-absen-hari="'.htmlspecialchars($item['absen_hari']).'"
                            data-total-potongan="'.htmlspecialchars($item['total_potongan']).'"
                            data-gaji-bersih="'.htmlspecialchars($item['total']).'"
                            data-piutang="'.htmlspecialchars($item['piutang']).'"
                            data-periode-gajian="'.htmlspecialchars($item['periode_gajian']).'"
                            data-total-potongan-telat="'.htmlspecialchars($item['total_potongan_telat']).'"
                            data-pph="'.htmlspecialchars($item['hasil_pph']).'"
                            data-total-gaji-bersih="'.htmlspecialchars($item['gaji_bersih']).'"
                            data-code-payroll="'.htmlspecialchars($item['code_payroll']).'"
                            data-logo="'.htmlspecialchars($item['logo']).'"
                            data-uang-makan-bersih="'.htmlspecialchars($item['uang_makan_bersih']).'"
                            data-pot-uang-makan="'.htmlspecialchars($item['pot_uang_makan']).'"
                            data-bonus="'.htmlspecialchars($item['bonus']).'"
                            data-tunjangan-bpjs="'.htmlspecialchars($item['tunjangan_bpjs']).'">
                            RINCIAN
                        </button>
  
                     ';
			}


			$row = [];
			$row[] = ++$no;
			$row[] = date('d M Y', strtotime($item['tanggal_gajian']));
			$row[] = $item['name'];
			$row[] = 'Rp.'.number_format($item['basic_salary'], 0 , ',', '.');
			$row[] = 'Rp.'.number_format($item['basic_uang_makan'], 0 , ',', '.');
			$row[] = 'Rp.'.number_format($item['bonus'], 0 , ',', '.');
			$row[] = 'Rp.'.number_format($item['tunjangan_bpjs'], 0 , ',', '.');
			$row[] = $item['total_dayoff'];
			$row[] = $item['total_absen'];
			$row[] = 'Rp.'.number_format($item['total_potongan'], 0 , ',', '.');
			$row[] = $item['hasil_pph'];
			$row[] = 'Rp.'.number_format($item['total_overtime'], 0 , ',', '.');
			$row[] = 'Rp.'.number_format($item['total'], 0 , ',', '.');
			$row[] = 'Rp.'.number_format($item['gaji_bersih'], 0 , ',', '.');
			$row[] = $item['description'];
			$row[] = $action;
			$data[] = $row;
		}

		$output = [
			"draw" =>@$_POST['draw'],
			"recordsTotal" => $this->M_payroll_component->count_all(),
			"recordsFiltered" => $this->M_payroll_component->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}


	public function data_attendance()
	{
		$startDate = $this->input->post('startDate', true);
		$endDate = $this->input->post('endDate', true);


			$list = $this->M_rekap->get_datatables();

			$data = [];
			$no = $this->input->post('start');

			foreach ($list as $item) {

				$totalAbsent = $this->M_schedule->totalScheduleByStatus_get($item['id_employee'], $startDate, $endDate, 7);
				$totalDayOff = $this->M_schedule->totalScheduleByStatus_get($item['id_employee'], $startDate, $endDate, 2);
				$totalIzin = $this->M_schedule->totalScheduleByStatus_get($item['id_employee'], $startDate, $endDate, 5);
				$totalCuti = $this->M_schedule->totalScheduleByStatus_get($item['id_employee'], $startDate, $endDate, 4);
				$totalHadir = $this->M_schedule->totalScheduleByStatus_get($item['id_employee'], $startDate, $endDate, 6);
				$totalTelat = $this->M_attendance->totalTelatByAttendance_get($item['id_employee'], $startDate, $endDate);



				$action =
					'   
                        <button class="btn gradient-btn-delete btn-sm mb-2 rounded-pill btn-delete-schedule" onclick="handleDeleteScheduleButton(' . htmlspecialchars($item['id_employee']) . ')" style="width : 70px">
                            DELETE
                        </button>
                     ';


				$row = [];
				$row[] = ++$no;
				$row[] = $item['name'];
				$row[] = $item['name_product'];
				$row[] = $item['name_division'];
				$row[] = $totalHadir;
				$row[] = $totalAbsent;
				$row[] = $totalTelat;
				$row[] = $totalDayOff;
				$row[] = $totalIzin;
				$row[] = $totalCuti;
				$data[] = $row;
			}

			$output = [
				"draw" => @$_POST['draw'],
				"recordsTotal" => $this->M_rekap->count_all(),
				"recordsFiltered" => $this->M_rekap->count_filtered(),
				"startDate" => $startDate,
				"endDate" => $endDate,
				"data" => $data,
			];

			echo json_encode($output);

	}


	public function data_log_attendance()
	{
		$startDate = $this->input->post('startDate', true);
		$endDate = $this->input->post('endDate', true);
		$employee = $this->input->post('employee', true);

		$list = $this->M_attendance->get_datatables();

		$data = [];
		$no = $this->input->post('start');

		foreach ($list as $item) {

			$action = '-';

//			if($item['time_management'] == false && $employee == 'false'){
//				$action = ' <a href="javascript:void(0)" onclick="setPotonganTelat(this)" class="btn btn-info btn-sm rounded-pill 	 	  			btn-ptng-telat" style="width : 120px"
//								data-id_attendance="'. $item['id_attendance'].'"
//								data-potongan_telat="'. $item['potongan_telat'].'">
//									Potong
//							</a>
//							 ';
//			}

			if($employee == 'false'){
				$action = '<a href="javascript:void(0)" onclick="setTimeManagement(this)" class="btn btn-success btn-sm rounded-pill 	 	  			btn-ptng-telat" style="width : 120px"
								data-id_attendance="'. $item['id_attendance'].'"
								data-time_management="'. $item['time_management'].'"
								data-jam_masuk="'. date('H:i', strtotime($item['jam_masuk'])).'">
									Edit
							</a>	';
				if($item['time_management'] == false){
					$action .= ' <a href="javascript:void(0)" onclick="setPotonganTelat(this)" class="btn btn-info btn-sm rounded-pill 	 	  			btn-ptng-telat" style="width : 120px"
								data-id_attendance="'. $item['id_attendance'].'"
								data-potongan_telat="'. $item['potongan_telat'].'">
									Potong
							</a>
							 ';
				}
			}


			$time_management = $item['time_management'] == true ? '<span class="badge bg-success"  style="width: 70px;">On Time</span>' : '<span class="badge bg-warning"  style="width: 70px;">Telat</span>';

			$row = [];
			$row[] = ++$no;
			$row[] = $item['name'];
			$row[] = $item['name_product'];
			$row[] = $item['name_workshift'];
			$row[] = $item['clock_in'];
			$row[] = $item['jam_masuk'];
			$row[] = $time_management;
			$row[] = 'Rp.'.number_format($item['potongan_telat'], 0 , ',', '.');
			$row[] =  date('d M Y', strtotime($item['tanggal_masuk']));
			$row[] =  date('d M Y', strtotime($item['waktu']));
			$row[] = $action;
			$data[] = $row;
		}

		$output = [
			"draw" => @$_POST['draw'],
			"recordsTotal" => $this->M_attendance->count_all(),
			"recordsFiltered" => $this->M_attendance->count_filtered(),
			"startDate" => $startDate,
			"endDate" => $endDate,
			"data" => $data,
		];

		echo json_encode($output);

	}


	public function data_service_teknisi()
	{
		$option = $this->input->post('option');
		$startDate = $this->input->post('startDate');
		$endDate = $this->input->post('endDate');
		$is = $this->input->post('is');
		$id = $this->input->post('employee');

		$list = $this->M_service_teknisi->get_datatables();
		$status  = '';

		$data = [];
		$no = $this->input->post('start');

		foreach($list as $item) {
			$action = $is == 1 || $is == 2 ?
				'   
                        <button class="btn gradient-btn-delete btn-sm mb-2 rounded-pill btn-delete-service_teknisi" onclick="handleDeleteServiceTeknisiButton('.htmlspecialchars($item['id_service_teknisi']).')" style="width : 70px">
                            DELETE
                        </button>
                     ':
				'-';


			if($item['status'] == 1 && $id == 'false' && $is !=3){
				$status =
					' 	
							<td>
							   <a href="javascript:void(0)" onclick="setStatusServiceTeknisi(this)" class="btn btn-danger btn-sm rounded-pill btn-stts" style="width : 140px"
								data-id_service_teknisi="'. $item['id_service_teknisi'].'"
								data-id_employee="'. $item['id_employee'].'"
								data-total_service="'. $item['total_service'].'"
								data-tgl_service_teknisi="'. $item['tanggal_service'].'"
								data-status="'. $item['status'].'">
									<i class="bi bi-x-lg"></i> Disapprove
								</a>
							</td>
						  ';
			}
			else if($item['status'] == 2 &&  $id == 'false'  && $is !=3){
				$status =  '
                             <td>
							   <a href="javascript:void(0)" onclick="setStatusServiceTeknisi(this)" class="btn btn-primary btn-sm rounded-pill btn-stts" style="width : 120px"
								data-id_service_teknisi="'. $item['id_service_teknisi'].'"
								data-id_employee="'. $item['id_employee'].'"
								data-total_service="'. $item['total_service'].'"
								data-tgl_service_teknisi="'. $item['tanggal_service'].'"
								data-status="'. $item['status'].'">
									<i class="ti ti-check"></i> Approve
								</a>
							</td>
                           ';
			}
			else if ($item['status'] == 3 &&  $id == 'false'  && $is !=3) {
				$status =  '
                             <td>
							   <a a href="javascript:void(0)" onclick="setStatusServiceTeknisi(this)" class="btn btn-info btn-sm rounded-pill btn-stts" style="width : 120px"
								data-id_service_teknisi="'. $item['id_service_teknisi'].'"
								data-total_service="'. $item['total_service'].'"

								data-id_employee="'. $item['id_employee'].'"
								data-tgl_service_teknisi="'. $item['tanggal_service'].'"
								data-status="'. $item['status'].'">
									<i class="bi bi-clock-history"></i> Pending
								</a>
							</td>
                           ';
			}
			else if($item['status'] == 1 &&  $id != 'false'  && $is ==3){
				$status =
					' 	
							<td>
							   <button class="btn btn-danger btn-sm rounded-pill btn-stts" style="width : 140px" disabled="">
									<i class="bi bi-x-lg"></i> Disapprove
								</button>
							</td>
						  ';
			}
			else if($item['status'] == 2 && $id != 'false' && $is ==3){
				$status =  '
                             <td>
							   <button class="btn btn-primary btn-sm rounded-pill btn-stts" style="width : 120px">
									<i class="ti ti-check"></i> Approve
								</button>
							</td>
                           ';
			}
			else if ($item['status'] == 3 && $id != 'false' && $is ==3) {
				$status =  '
                             <td>
							   <button class="btn btn-info btn-sm rounded-pill btn-stts" style="width : 120px" disabled>
									<i class="bi bi-clock-history"></i> Pending
								</button>
							</td>
                           ';
			}


			$upah = $item['total_service'] == null ? 0 : $item['total_service'];

			$row = [];
			$row[] = ++$no;
			$row[] = date('d M Y', strtotime($item['input_at']));
			$row[] = $item['name'];
			$row[] = $item['name_product'];
			$row[] = $item['name_division'];
			$row[] = $item['type_service'];
			$row[] = 'Rp.'. number_format($item['pendapatan_service'], 0 , ',', '.');
			$row[] = 'Rp.'. number_format($upah , 0 , ',', '.');
			$row[] = date('d M Y', strtotime($item['tanggal_service']));
			$row[] = $item['description'];
			$row[] = $status;
			$row[] = $action;
			$data[] = $row;
		}

		$output = [
			"draw" =>@$_POST['draw'],
			"recordsTotal" => $this->M_service_teknisi->count_all(),
			"recordsFiltered" => $this->M_service_teknisi->count_filtered(),
			"option" => $option,
			"startDate" => $startDate,
			"endDate" => $endDate,
			"data" => $data,
		];

		echo json_encode($output);
	}


	public function data_batch_uang_makan()
	{
		$option = $this->input->post('option');
		$startDate = $this->input->post('startDate');
		$endDate = $this->input->post('endDate');
		$groupByCode = $this->input->post('groupbycode');

		$list = $this->M_batch_uang_makan->get_datatables();

		$data = [];
		$no = $this->input->post('start');
		//		<a href="'. base_url('admin/uang_makan/detail_uang_makan?uang_makan='.$item['id_uang_makan']) .'"
		//						   class="btn btn-warning btn-sm rounded-pill mb-1"
		//						   style="width: 100px;">
		//							   DETAIL
		//						</a>

		foreach($list as $item) {

			if(!empty($groupByCode) || $groupByCode != ''){
				$action =
					'   		
								<a href="'. base_url('admin/uang_makan/detail_uang_makan?code='.$item['code_batch_uang_makan']) .'"
								   class="btn btn-warning btn-sm rounded-pill mb-1"
								   style="width: 100px;">
									   DETAIL
								</a> 
                      
                      
                     ';
			} else {
				$action =
					'   		<a href="'. base_url('admin/uang_makan/detail_uang_makan?uang_makan='.$item['id_batch_uang_makan']) .'"
							   class="btn btn-warning btn-sm rounded-pill mb-1"
							   style="width: 100px;">
								   DETAIL
								</a>
								
								 <button class="btn  gradient-btn-delete btn-sm rounded-pill" 
								onclick="handleDeleteBatchUangMakanButton('. htmlspecialchars($item['id_batch_uang_makan']) .')" 
								style="width: 100px;">
									DELETE
								</button>

                     ';
			}



			$finance_record = $item['auto_finance_record'] == 1 ? '<span><i class="bi bi-check-circle-fill" style="color : green"></i></span>' : '<span><i class="bi bi-x-circle-fill" style="color : darkred"></i></span>';
			$holiday = $item['include_holiday'] == 1 ? '<span><i class="bi bi-check-circle-fill" style="color : green"></i></span>' : '<span><i class="bi bi-x-circle-fill" style="color : darkred"></i></span>';
			$cuti = $item['include_leave'] == 1 ? '<span><i class="bi bi-check-circle-fill" style="color : green"></i></span>' : '<span><i class="bi bi-x-circle-fill" style="color : darkred"></i></span>';
			$absen = $item['include_absen'] == 1 ? '<span><i class="bi bi-check-circle-fill" style="color : green"></i></span>' : '<span><i class="bi bi-x-circle-fill" style="color : darkred"></i></span>';


			$row = [];
			$row[] = ++$no;
			$row[] = date('d M Y', strtotime($item['tanggal_batch_uang_makan']));
			$row[] = $item['code_batch_uang_makan'];
			$row[] = $finance_record;
			$row[] = $holiday;
			$row[] = $cuti;
			$row[] = $absen;
			$row[] = $action;
			$data[] = $row;
		}

		$output = [
			"draw" =>@$_POST['draw'],
			"recordsTotal" => $this->M_batch_uang_makan->count_all(),
			"recordsFiltered" => $this->M_batch_uang_makan->count_filtered(),
			"option" => $option,
			"startDate" => $startDate,
			"endDate" => $endDate,
			"data" => $data,
		];

		echo json_encode($output);
	}


	public function data_uang_makan()
	{
		$uang_makan = $this->input->post('uang_makan', true);
		$list = $this->M_uang_makan->get_datatables();

		$data = [];
		$no = $this->input->post('start');

		foreach($list as $item) {
			$action =
				'    <button 
                            class="btn btn-warning btn-sm mb-2 rounded-pill btn-rincian-gaji" style="width : 90px"
                            data-bs-toggle="modal" 
                            data-bs-target="#rincianUangMakanModal"
                            data-id="'.htmlspecialchars($item['id_batch_uang_makan']).'"
                            data-total-izin="'.htmlspecialchars($item['total_izin']).'"
                            data-total-cuti="'.htmlspecialchars($item['total_cuti']).'"
                            data-total-absent="'.htmlspecialchars($item['total_absen']).'"
                            data-uang-makan="'.htmlspecialchars($item['uang_makan']).'"
                            data-name="'.htmlspecialchars($item['name']).'"
                            data-product="'.htmlspecialchars($item['name_product']).'"
                            data-divisi="'.htmlspecialchars($item['name_division']).'"
                            data-position="'.htmlspecialchars($item['name_position']).'"
                            data-potongan-absen="'.htmlspecialchars($item['pot_absen']).'"
                            data-potongan-izin="'.htmlspecialchars($item['pot_izin']).'"
                            data-total-potongan="'.htmlspecialchars($item['total_pot_uang_makan']).'"
                            data-uang-makan-bersih="'.htmlspecialchars($item['total_uang_makan']).'"
                            data-potongan-libur-nasional="'.htmlspecialchars($item['pot_holiday']).'"
                            data-total-libur-nasional="'.htmlspecialchars($item['total_holiday']).'"
                            data-potongan-cuti="'.htmlspecialchars($item['pot_cuti']).'"
                            data-code-payroll="'.htmlspecialchars($item['code_batch_uang_makan']).'">
                            RINCIAN
                        </button>
                        <button class="btn gradient-btn-delete btn-sm mb-2 rounded-pill btn-delete-uang-makan" onclick="test('.htmlspecialchars($item['id_uang_makan']).')" style="width : 70px">
                            DELETE
                        </button>
                     ';


			$row = [];
			$row[] = ++$no;
			$row[] = date('d M Y', strtotime($item['input_at']));
			$row[] = $item['name'];
			$row[] = 'Rp.'.number_format($item['basic_uang_makan'], 0 , ',', '.');
			$row[] = $item['total_izin'];
			$row[] = 'Rp.'.number_format($item['pot_izin'], 0 , ',', '.');
			$row[] = $item['total_cuti'];
			$row[] = 'Rp.'.number_format( $item['pot_cuti'], 0 , ',', '.');
			$row[] = $item['total_absen'];
			$row[] = 'Rp.'.number_format($item['pot_absen'], 0 , ',', '.');
			$row[] = $item['total_holiday'];
			$row[] = $item['bonus'];
			$row[] = 'Rp.'.number_format($item['pot_holiday'], 0 , ',', '.');
			$row[] = 'Rp.'.number_format($item['total_pot_uang_makan'], 0 , ',', '.');
			$row[] = 'Rp.'.number_format($item['total_uang_makan'], 0 , ',', '.');
			$row[] = $action;
			$data[] = $row;
		}

		$output = [
			"draw" =>@$_POST['draw'],
				"recordsTotal" => $this->M_uang_makan->count_all(),
			"recordsFiltered" => $this->M_uang_makan->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}


	public function data_unattendance()
	{
		$startDate = $this->input->post('startDate1', true);
		$endDate = $this->input->post('endDate1', true);
		

		$list = $this->M_schedule->get_datatables1();

		$data = [];
		$no = $this->input->post('start');

		foreach ($list as $item) {

			

			$row = [];
			$row[] = ++$no;
			$row[] = $item['name'];
			$row[] = $item['name_product'];
			$row[] = $item['name_workshift'].' ';
			$row[] = $item['clock_in'];
			$row[] =  date('d M Y', strtotime($item['waktu']));
			$data[] = $row;
		}

		$output = [
			"draw" => @$_POST['draw'],
			"recordsTotal" => $this->M_schedule->count_all1(),
			"recordsFiltered" => $this->M_schedule->count_filtered1(),
			"startDate" => $startDate,
			"endDate" => $endDate,
			"data" => $data,
		];

		echo json_encode($output);

	}


	public function data_schedule_dayoff()
	{
		$startDate = $this->input->post('startDate2', true);
		$endDate = $this->input->post('endDat2', true);

		$list = $this->M_schedule->get_datatables2();

		$data = [];
		$no = $this->input->post('start');

		foreach ($list as $item) {
			$status_type = '';

			if($item['status'] == 2 ) {
				$status_type = '
							 
								  <span class="badge gradient-btn-unpaid btn-sm " style="width : 50px">
									  Day Off
								  </span>
							 
							';
			} else if($item['status'] == 4 ) {
				$status_type = '
							 
								  <span class="badge gradient-btn-paid btn-sm " style="width : 50px">
									  Cuti
								  </span>
							 
							';
			} else if($item['status' ]== 5 ) {
				$status_type = '
							  
								  <span class="badge btn-info btn-sm " style="width : 50px">
									  Izin
								  </span>
							 
							';
			}


			$row = [];
			$row[] = ++$no;
			$row[] = $item['name'];
			$row[] = $item['name_product'];
			$row[] = $item['name_division'].' ';
			$row[] = $status_type;
			$row[] =  date('d M Y', strtotime($item['waktu']));
			$data[] = $row;
		}

		$output = [
			"draw" => @$_POST['draw'],
			"recordsTotal" => $this->M_schedule->count_all2(),
			"recordsFiltered" => $this->M_schedule->count_filtered2(),
			"startDate" => $startDate,
			"endDate" => $endDate,
			"data" => $data,
		];

		echo json_encode($output);

	}

	public function data_koperasi()
	{
		$this->load->model('M_koperasi');
		$tenor = $this->input->post('tgl_lunas');
		$type = $this->input->post('type_koperasi');
		$status_piutang = $this->input->post('status');

		$list = $this->M_koperasi->get_datatables();

		$data = [];
		$no = $this->input->post('start');

		foreach($list as $item) {

			$type_ten = '';
			if($item['type_tenor'] == 1) {
				$type_ten = 'Hari';
			} else if($item['type_tenor'] == 2) {
				$type_ten = 'Minggu';
			}else if($item['type_tenor'] == 3) {
				$type_ten = 'Bulan';
			}else if($item['type_tenor'] == 4) {
				$type_ten = 'Tahun';
			}
			$action = $status_piutang == 1 ?
				'   
                        <button 
                            class="btn btn-warning btn-sm mb-2 rounded-pill btn-pay-piutang" style="width : 70px"
                            data-bs-toggle="modal" 
                            data-bs-target="#logKoperasiModal"
                            data-id_koperasi="'.htmlspecialchars($item['id_koperasi']).'"
                            data-name_log="'.htmlspecialchars($item['name']).'"
                            data-totals_log="'.htmlspecialchars($item['amount_koperasi']).'"
                            data-paydate_log="'.htmlspecialchars($item['koperasi_date']).'"
                            data-tgl_lunas_log="'.htmlspecialchars($item['tgl_lunas']).'">
                             LOG
                        </button>
                        <button class="btn gradient-btn-delete btn-sm mb-2 rounded-pill btn-delete-koperasi onclick="handleDeleteKoperasiButton('.htmlspecialchars($item['id_koperasi']).')" style="width : 70px">
                            DELETE
                        </button>
						'
				:
				'   
                        <button 
                            class="btn btn-warning btn-sm mb-2 rounded-pill btn-pay-piutang" style="width : 70px"
                            data-bs-toggle="modal" 
                            data-bs-target="#payKoperasiModal"
                            data-id_koperasi="'.htmlspecialchars($item['id_koperasi']).'"
                            data-remaining="'.htmlspecialchars($item['remaining']).'"
                            data-tgl_lunas="'.htmlspecialchars($item['tgl_lunas']).'"
                            data-tenor_koperasi="'.htmlspecialchars($item['tenor_koperasi']).'"
                            data-angsuran="'.htmlspecialchars($item['angsuran']).'"
                            data-type_tenor="'.htmlspecialchars($item['type_tenor']).'">
                             PAY
                        </button>
                        <button class="btn gradient-btn-delete btn-sm mb-2 rounded-pill btn-delete-koperasi" onclick="handleDeleteKoperasiButton('.htmlspecialchars($item['id_koperasi']).')" style="width : 70px">
                            DELETE
                        </button>
						';

			$status = $item['status'] == 2 ?
				'
				  <td>
					  <span class="badge gradient-btn-unpaid btn-sm " style="width : 50px">
						  Unpaid
					  </span>
				  </td>
				'
				:
				'
				  <td>
					  <span class="badge gradient-btn-paid btn-sm " style="width : 50px">
						  Paid
					  </span>
				  </td>
			   ';

			$row = [];
			$row[] = ++$no;
			$row[] = date('d M Y', strtotime($item['koperasi_date']));
			$row[] = $item['name'];
			$row[] = $item['type_koperasi'] == 2 ? 'Kasbon' : 'Pinjaman';
			$row[] = $item['tenor_koperasi'] . ' ' . $type_ten;
			$row[] = 'Tanggal '.$item['tgl_jatuh_tempo'];
			$row[] = date('d M Y', strtotime($item['tgl_lunas']));
			$row[] = 'Rp.'. number_format($item['amount_koperasi'], 0 , ',', '.');
			$row[] = 'Rp.'. number_format($item['remaining'], 0 , ',', '.');
			$row[] = $status;
			$row[] = 'Rp.'. number_format($item['angsuran'], 0 , ',', '.');
			$row[] = $item['description'];
			$row[] = $action;
			$data[] = $row;
		}

		$output = [
			"draw" =>@$_POST['draw'],
			"recordsTotal" => $this->M_koperasi->count_all(),
			"recordsFiltered" => $this->M_koperasi->count_filtered(),
			"tenor" => $tenor,
			"type" => $type,
			"data" => $data,
		];

		echo json_encode($output);
	}
}
