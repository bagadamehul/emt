<?php
namespace Enbolt\Emt\Http\Controllers;

use App\Http\Controllers\Controller;
use Enbolt\Emt\ApprovalPrequel;
use Auth;
use DB;
use Illuminate\Http\Request;

class EmtController extends Controller {
	public $_QRcodeFolderPath = "/app/public/google-auth-qr-codes/";
	public function __construct() {
		$this->moduleTitleS = 'Admin';
		$this->moduleTitleP = 'Theme.Admin.emt';
		$this->ApprovalPrequel = new ApprovalPrequel;

		view()->share('moduleTitleP', $this->moduleTitleP);
		view()->share('moduleTitleS', $this->moduleTitleS);
		view()->share('pageTitle', 'Administrator');
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		if (!auth()->user()) {
			return redirect('/');
		}
		$tablesList = DB::select('SHOW TABLES');
		view()->share('pageTitle', 'Administrator');
		view()->share("pageMetaTitle", "Administrator");

		return view($this->moduleTitleP . '.index',compact('tablesList'));
	}

	public function run(Request $request) {
		$user = \Auth::user();

		// dd($request->all());
		$qry = trim($request->qry);
		$format = $request->format;
		// $qry = "SELECT * FROM admins";
		$first = substr($qry, 0, 6);
		$drop_text = substr($qry, 0, 4);
		$alter_text = substr($qry, 0, 5);
		$data = [];
		$type = "select";
		$flag = true;
		$status = "1";
		try{
			if (strtolower($first) == "select") {
				// select
				$data = DB::select($qry);
				if ($request->submit == "DOWNLOAD") {
					$name = 'reports.csv';
					$headers = [
						'Content-Disposition' => 'attachment; filename=' . $name,
					];

					return response()->stream(function () use ($data) {
						$file = fopen('php://output', 'w+');

						foreach ($data as $key => $line) {
							$line = (array) $line;
							if ($key == 0) {
								$column = array_keys($line);
								fputcsv($file, $column);
							}
							fputcsv($file, $line);
						}
						fclose($file);
					}, 200, $headers);
				}
				if ($request->submit == "DOWNLOADSQL") {
					$name = 'reports.sql';
					$headers = [
						'Content-Disposition' => 'attachment; filename=' . $name,
					];

					$structure = '';
			        $insertQuery = '';
			        preg_match('/\bfrom\b\s*(\w+)/i',$qry,$matches);
			        $table=$matches[1];
			        $show_table_query = "SHOW CREATE TABLE " . $table . "";

			        $show_table_result = DB::select(DB::raw($show_table_query));

			        foreach ($show_table_result as $show_table_row) {
			            $show_table_row = (array)$show_table_row;
			            $structure .= "\n\n" . $show_table_row["Create Table"] . ";\n\n";
			        }
			        foreach ($data as $record) {
			            $record = (array)$record;
			            $table_column_array = array_keys($record);
			            foreach ($table_column_array as $key => $name) {
			                $table_column_array[$key] = '`' . $table_column_array[$key] . '`';
			            }

			            $table_value_array = array_values($record);
			            $insertQuery .= "\nINSERT INTO $table (";

			            $insertQuery .= "" . implode(", ", $table_column_array) . ") VALUES \n";

			            foreach($table_value_array as $key => $record_column)
			                $table_value_array[$key] = addslashes($record_column);

			            $insertQuery .= "('" . implode("','", $table_value_array) . "');\n";
			        }
			        $name = 'reports.sql';
			        $headers = [
			            'Content-Disposition' => 'attachment; filename=' . $name,
			        ];
			        return response()->stream(function () use ($insertQuery,$structure) {
			            $file_handle = fopen('php://output', 'w + ');

			            $output = $structure . $insertQuery;
			            fwrite($file_handle, $output);
			            fclose($file_handle);
			        }, 200, $headers);
				}
			}
			if (strtolower($first) == "update") {
				// update
				$type = "update";
				$flag = false;
				$status = "0";

			}
			if (strtolower($first) == "delete") {
				// delete
				$type = "delete";
				$flag = false;
				$status = "0";
			}
			if (strtolower($drop_text) == "drop") {
				// drop
				$type = "drop";
				$flag = false;
				$status = "0";
			}
			if (strtolower($alter_text) == "alter") {
				// alter
				$type = "alter";
				$flag = false;
				$status = "0";
			}

			$msg = "";
			if ($type != "select" && $flag == true) {
				DB::statement($qry);
				$data = $type . " successfully";
			}
			$is_approval_prequel = 	DB::table('approval_prequel')
										->where('qry',$qry)
										->where('type',$type)
										->where('user_id', auth()->user()->id)
										->where('status','1')
										->where('qry_count','0')
										->first();
			if ($is_approval_prequel && $type != "select") {
				DB::statement($qry);
				$data = $type . " successfully";
				DB::table('approval_prequel')->where('id',$is_approval_prequel->id)->update(['qry_count'=>1]);
			}else{
				$approval_prequel = DB::table('approval_prequel')->insertGetId(
					[
						'user_id' => $user->id,
						'qry' => $qry,
						'status' => $status,
						'type' => $type,
						'created_at' => date('Y-m-d H:i:s'),
					]
				);
				if ($status == 0) {
					// send mail for approval

					$data = $type . " sent for approval";
				}
			}


			if ($format == 'Json') {
				$data = json_encode($data, JSON_PRETTY_PRINT);
			}
			if ($format == 'Array') {

			}
			return view($this->moduleTitleP . '.data', compact('data', 'format','qry','type'));
		}catch(Exception $e){
			dd($e);
		}catch(\Illuminate\Database\QueryException $e){
			// dd($e->getMessage());
			return redirect()->back()
				->withInput(['qry' => $request->qry])
				->with(['error' => $e->getMessage()]);
		}
	}

	public function Approval(Request $request) {
		if (!auth()->user()) {
			return redirect('/');
		}
		view()->share('pageTitle', 'Query Approval');
		$DCs = \DB::table("location_code")->orderBy("dc")->pluck("dc", "dc")->toArray();
		$agents = \DB::table("agents")->select('name', 'id', 'cm_id')->where("deleted", "0")->get();
		$o = [];
		foreach ($agents as $key => $value) {
			$o[$value->cm_id][$value->id] = $value->name;
		}
		return view($this->moduleTitleP . '.approval_list', compact('DCs'))->with('agents', $o);
	}
	public function getApproval(Request $request) {
		$filters = [];
		if ($request->has("status_filter")) {
			$filters[] = ["approval_prequel.status", "=", $request->get("status_filter")];
		}

		$data = $this->ApprovalPrequel->getData($filters);
		return \DataTables::of($data, $request)
			->addColumn('store_name', function ($data) use ($request) {
				return $data->store_name;
			})

			->addColumn('status', function ($data) {
				if ($data->status == '1') {
					return 'Approved';
				} elseif ($data->status == '2') {
					return 'Rejected';
				} else {
					return 'Pending';
				}
			})
			->editColumn('image', function ($data) {
				return '';
			})
			->addColumn('approvel_on', function ($data) {
				if ($data->status == '1') {
					return $data->admin_name;
				} elseif ($data->status == '2') {
					return $data->admin_name;
				} else {
					return '<a class="approval-record" data-toggle="modal" data-target="#delivery-boy-approval-modal" cm_id="' . $data->id . '" data-url="' . \URL::route('approval-system.emt-status', [$data->id, 1]) . '" href="#"><i class="ti-check"></i></a>
                            <span style="font-size: 20px">|</span>
                        <a class="reject-record" data-toggle="modal" data-target="#reject-bank-modal" data-url="' . \URL::route('approval-system.emt-status', [$data->id, 2]) . '" href="#"><i class="ti-close" style="color: red;"></i></a>';
				}
			})
			->setRowClass(function ($data) {
				if ($data->status == '1') {
					return 'green-bg';
				} elseif ($data->status == '2') {
					return 'tomato-bg';
				} else {
					return '';
				}
			})
			->rawColumns(['coi', 'status', 'approvel_on', 'nature_of_deviation'])
			->make(true);
	}

	// =============================================
	/* menthod : deliveryBoyApprovalStatusChange
		        * @param  : approval_pan_id,status
		        * @Description : approve or reject newly updated delivery boy
	*/// ==============================================
	public function ApprovalStatusChange($id = '', $status = '', Request $request) {

		$ApprovalPrequel = ApprovalPrequel::find($id);
		if (isset($ApprovalPrequel->id)) {
			if ($status == 1) {
				/*...Approved...*/

				ApprovalPrequel::where('id', $id)->update([
					'status' => $status,
					'approved_by' => Auth::user()->id,
					'approved_at' => date('Y-m-d H:i:s'),
				]);

				// $qry = $ApprovalPrequel->qry;

				// DB::statement($qry);

				notificationMsg('success', 'Query approved successfully');
				return redirect(route('approval-system.emt-approval'));

			} else if ($status == 2) {
				/*...Rejected...*/

				ApprovalPrequel::where('id', $id)->update([
					'status' => $status,
					'approved_by' => Auth::user()->id,
					'approved_at' => date('Y-m-d H:i:s'),
					"remarks" => $request->remarks,
				]);

				notificationMsg('success', 'Query rejected successfully');
				return redirect(route('approval-system.emt-approval'));
			} else {
				notificationMsg('error', 'try again');
				return redirect(route('approval-system.emt-approval'));
			}
		}
		notificationMsg('error', 'Error try again after sometime.');
		return redirect(route('approval-system.emt-approval'));

	}

	/**
	 * Write code on Method
	 *
	 * @return response()
	 */
	public function getApprovalList(Request $request)
	{
		$filters = [];
		$filters[] = ['approval_prequel.type','!=','select'];
		$filters[] = ['approval_prequel.user_id','=',auth()->user()->id];
		$data = $this->ApprovalPrequel->getDataApprovalStatusList($filters);
		return \DataTables::of($data)
			->addColumn('status', function ($data) {
				if ($data->status == '1') {
					return '<label class="label label-success">Approved</label>';
				} elseif ($data->status == '2') {
					return '<label class="label label-danger">Rejected</label>';
				} else {
					return '<label class="label label-warning">Pending</label>';
				}
			})
			->addColumn('approved_by', function ($data) {
				$approved_by = '-';
				if (!is_null($data->admin_name)) {
					$approved_by = $data->admin_name;
				}
				return $approved_by;
			})
			->addColumn('approved_at', function ($data) {
				$approved_at = '-';
				if (!is_null($data->approved_at)) {
					$approved_at = \Carbon\Carbon::parse($data->approved_at)->format('d-m-Y H:i:s');
				}
				return $approved_at;
			})
			->addColumn('action', function ($data) {
				return '-';
			})
			->rawColumns(['status','action'])
			->make(true);
	}

	/**
	 * Write code on Method
	 *
	 * @return response()
	 */
	public function getQueryData(Request $request)
	{
		if($request->has('qry')){
			$qry = $request->qry;
			if (($request->has('column') && !empty($request->value)) || (trim($request->op) == 'IS NULL' || trim($request->op) == 'IS NOT NULL')) {
				if ($request->op == 'LIKE %%') {
					$qry = $qry.' WHERE '.$request->column.' LIKE '.'"%'.$request->value.'%"';
				}else if ($request->op == 'IN' || $request->op == 'NOT IN') {
					$qry = $qry.' WHERE '.$request->column.' '.$request->op.' ('.$request->value.')';
				}else if (trim($request->op) == 'IS NULL' || trim($request->op) == 'IS NOT NULL') {
					$qry = $qry.' WHERE '.$request->column.' '.$request->op;
				}else{
					$qry = $qry.' WHERE '.$request->column.' '.$request->op.' "'.$request->value.'"';
				}
			}
			$data = DB::select($qry);
			view()->share('qry', $qry);
			return \DataTables::of($data)
				->make(true);
		}
	}

	public function mySqlStatistics()
	{
		// $q = DB::select("show processlist;");
		// $q = DB::select("SHOW ENGINE INNODB STATUS;");
		// $q = DB::select("SHOW FULL PROCESSLIST;");

		// Buffer Size
		// $q = DB::select("SHOW VARIABLES LIKE '%buffer%';");
		
		// Query Size
		// $q = DB::select("SHOW VARIABLES LIKE '%query%';");

		// memory alloc
		// dd($q);
		$q = DB::select("SELECT SUBSTRING_INDEX(event_name,'/',2) AS
       code_area, SUM(current_alloc)
       AS current_alloc
       FROM sys.memory_global_by_current_bytes
       GROUP BY SUBSTRING_INDEX(event_name,'/',2)
       ORDER BY SUM(current_alloc) DESC;");
		// $res = DB::select("SELECT * FROM sys.memory_global_by_current_bytes");
		// dd($res);
		// Total Memory DB Storage
// 		$q = DB::select('SELECT ( @@key_buffer_size
// + @@query_cache_size
// + @@innodb_buffer_pool_size
// + @@innodb_log_buffer_size
// + @@max_connections * ( 
//     @@read_buffer_size
//     + @@read_rnd_buffer_size
//     + @@sort_buffer_size
//     + @@join_buffer_size
//     + @@binlog_cache_size
//     + @@thread_stack
//     + @@tmp_table_size )
// ) / (1024 * 1024 * 1024) AS MAX_MEMORY_GB;');

		$res = DB::select("SELECT *
       FROM sys.memory_global_by_current_bytes
       GROUP BY SUBSTRING_INDEX(event_name,'/',2)
       ORDER BY SUM(current_alloc) DESC");
		dd($res);
		return view($this->moduleTitleP.'.mySqlStatistics');
	}
}