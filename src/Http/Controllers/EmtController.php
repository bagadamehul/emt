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
		$tablesList = DB::select('SHOW TABLES');
		view()->share('pageTitle', 'Administrator');
		view()->share("pageMetaTitle", "Administrator");
		if (auth()->check() || auth()->guard('admin')->check()) {
			return view($this->moduleTitleP . '.index',compact('tablesList'));
		}else{
			return redirect('/');
		}
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

			if ($format == 'Json') {
				$data = json_encode($data, JSON_PRETTY_PRINT);
			}
			if ($format == 'Array') {

			}
			return view($this->moduleTitleP . '.data', compact('data', 'format'));
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
                        <a class="reject-record" data-toggle="modal" data-target="#reject-bank-modal" data-url="' . \URL::route('approval-system.prequel-status', [$data->id, 2]) . '" href="#"><i class="ti-close" style="color: red;"></i></a>';
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

				$qry = $ApprovalPrequel->qry;

				DB::statement($qry);

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

}
